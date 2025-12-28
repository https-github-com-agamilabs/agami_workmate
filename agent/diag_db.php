<?php
require_once 'db.php';
try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    $result = $conn->query("SELECT username, isactive FROM hr_user");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo "Users in hr_user:\n";
    print_r($users);

    $usage = $conn->query("SELECT DISTINCT user_id FROM app_usage");
    $usage_users = [];
    while ($row = $usage->fetch_assoc()) {
        $usage_users[] = $row['user_id'];
    }
    echo "\nUsers in app_usage:\n";
    print_r($usage_users);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
