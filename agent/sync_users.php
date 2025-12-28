<?php
require_once 'db.php';
try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    
    // Get unique users from app_usage who are NOT in hr_user
    $query = "SELECT DISTINCT user_id FROM app_usage WHERE user_id NOT IN (SELECT username FROM hr_user) AND user_id != '1'";
    $result = $conn->query($query);
    
    $pass = password_hash('password123', PASSWORD_DEFAULT);
    
    while ($row = $result->fetch_assoc()) {
        $u = $row['user_id'];
        $stmt = $conn->prepare("INSERT INTO hr_user (username, firstname, lastname, passphrase, isactive) VALUES (?, ?, ?, ?, 1)");
        $f = ucfirst($u);
        $l = 'User';
        $stmt->bind_param("ssss", $u, $f, $l, $pass);
        if ($stmt->execute()) {
            echo "Successfully added user: $u with password: password123\n";
        } else {
            echo "Error adding user $u: " . $stmt->error . "\n";
        }
    }
    
    echo "Sync complete.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
