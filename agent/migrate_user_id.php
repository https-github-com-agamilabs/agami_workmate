<?php
// load db.php
require_once 'db.php';

echo "Connecting to MySQL...\n";
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "Altering 'app_usage' table...\n";
$sql = "ALTER TABLE app_usage MODIFY user_id VARCHAR(255) NOT NULL";

if ($conn->query($sql) === TRUE) {
    echo "Column 'user_id' updated to VARCHAR(255) successfully.\n";
} else {
    echo "Error updating table: " . $conn->error . "\n";
}

$conn->close();
?>
