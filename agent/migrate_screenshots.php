<?php
// load db.php
require_once 'db.php';

echo "Connecting to MySQL...\n";
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "Adding 'screenshot_path' to 'app_usage' table...\n";
$sql = "ALTER TABLE app_usage ADD COLUMN screenshot_path VARCHAR(512) DEFAULT NULL";

if ($conn->query($sql) === TRUE) {
    echo "Column 'screenshot_path' added successfully.\n";
} else {
    echo "Error updating table (column might already exist): " . $conn->error . "\n";
}

$conn->close();
?>
