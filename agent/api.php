<?php 
// Set JSON header immediately
header('Content-Type: application/json');

// load db.php
require_once 'db.php';

// Enable mysqli exceptions
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Validate Bearer token from Authorization header
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (empty($authHeader) || !preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'error' => 'unauthorized', 'message' => 'Missing or invalid Authorization header']);
    exit;
}

$token = $matches[1];
if ($token !== $apiSecret) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'error' => 'unauthorized', 'message' => 'Invalid token: ' . $token]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'error' => 'invalid_json', 'message' => 'Invalid JSON payload received']);
    exit;
}

try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    
    // 1. Verify credentials against hr_user
    $username = $data['user_id'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing username or password"]);
        exit;
    }

    $authStmt = $conn->prepare("SELECT passphrase FROM hr_user WHERE username = ? AND isactive = 1");
    $authStmt->bind_param("s", $username);
    $authStmt->execute();
    $authResult = $authStmt->get_result();

    if ($authResult->num_rows === 0) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid username or account inactive"]);
        exit;
    }

    $userRow = $authResult->fetch_assoc();
    if (!password_verify($password, $userRow['passphrase'])) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid password"]);
        exit;
    }

    // 2. Auto-update schema to add record_id if missing
    $checkColumn = $conn->query("SHOW COLUMNS FROM app_usage LIKE 'record_id'");
    if ($checkColumn->num_rows === 0) {
        $conn->query("ALTER TABLE app_usage ADD COLUMN record_id VARCHAR(255) UNIQUE AFTER user_id");
    }

    // 3. Insert activity data with deduplication
    $record_id = $data['record_id'] ?? null;
    $user_id = $username;
    $app = $data['app'] ?? 'Unknown';
    $duration = isset($data['duration']) ? intval($data['duration']) : 0;
    $online = isset($data['online']) ? intval($data['online']) : 0;

    // Handle screenshot
    $screenshot_path = null;
    if (!empty($data['screenshot'])) {
        $uploadDir = __DIR__ . '/uploads/' . date('Y-m-d') . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        // Decode base64
        $imgData = base64_decode($data['screenshot']);
        if ($imgData !== false) {
            $filename = uniqid('scr_') . '.png';
            $fullPath = $uploadDir . $filename;
            if (file_put_contents($fullPath, $imgData)) {
                $screenshot_path = 'uploads/' . date('Y-m-d') . '/' . $filename;
            }
        }
    }

    // Use INSERT IGNORE or check for existence to prevent double entry
    $stmt = $conn->prepare(
      "INSERT IGNORE INTO app_usage (record_id, user_id, app_name, duration, online, screenshot_path) VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssiis", $record_id, $user_id, $app, $duration, $online, $screenshot_path);
    $stmt->execute();
    
    echo json_encode(['status' => 'success', 'ok' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'error' => 'server_error',
        'message' => $e->getMessage()
    ]);
}

