<?php
$employee_id = $_POST['employee_id'] ?? 'unknown';
$keystrokes = $_POST['keystrokes'] ?? '0';

// Save keystroke log
$log_dir = "logs/";
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0777, true);
}
$log_file = $log_dir . "keystrokes_" . $employee_id . ".log";
file_put_contents($log_file, date('c') . " - Keystrokes: $keystrokes\n", FILE_APPEND);

// Save uploaded screenshot
if (isset($_FILES['screenshot'])) {
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $filename = basename($_FILES['screenshot']['name']);
    move_uploaded_file($_FILES['screenshot']['tmp_name'], $upload_dir . $filename);
    echo "Upload successful";
} else {
    echo "No screenshot uploaded";
}
?>
