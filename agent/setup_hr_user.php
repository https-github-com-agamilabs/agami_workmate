<?php
// load db
require 'db.php';

try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Execute schema
    $sql = file_get_contents('backend/schema_hr_user.sql');
    if ($conn->query($sql) === TRUE) {
        echo "Table hr_user created successfully.\n";
    } else {
        echo "Error creating table: " . $conn->error . "\n";
    }

    // Insert test user if not exists
    $pass = password_hash('password123', PASSWORD_DEFAULT);
    $check = $conn->query("SELECT * FROM hr_user WHERE username='john.doe'");
    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO hr_user (username, firstname, lastname, passphrase, isactive) VALUES (?, ?, ?, ?, 1)");
        $u = 'john.doe'; $f = 'John'; $l = 'Doe';
        $stmt->bind_param("ssss", $u, $f, $l, $pass);
        if ($stmt->execute()) {
             echo "Test user 'john.doe' / 'password123' created.\n";
        } else {
            echo "Error inserting user: " . $stmt->error . "\n";
        }
    } else {
        echo "Test user already exists.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
