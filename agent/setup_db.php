<?php
// load db.php
require_once 'db.php';

echo "Connecting to MySQL at $dbHost as $dbUser...\n";

try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error . "\n");
    }
    echo "Connected successfully.\n";

    $sql = "
    CREATE DATABASE IF NOT EXISTS ".$dbName." DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
    USE ".$dbName.";

    CREATE TABLE IF NOT EXISTS app_usage (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id VARCHAR(255) NOT NULL,
      app_name VARCHAR(255) NOT NULL,
      screenshot_path VARCHAR(512) DEFAULT NULL,
      duration INT NOT NULL,
      online BOOLEAN NOT NULL DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS managers (
      id INT AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(255) UNIQUE NOT NULL,
      password_hash VARCHAR(255) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    INSERT IGNORE INTO managers (username, password_hash) VALUES (
      'admin',
      '".password_hash('11135984', PASSWORD_DEFAULT)."'
    );
    ";

    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
        echo "Database '".$dbName."' and tables created successfully.\n";
    } else {
        echo "Error creating database/tables: " . $conn->error . "\n";
    }

    $conn->close();

} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Please ensure MySQL is running (e.g., via XAMPP Control Panel).\n";
}
?>
