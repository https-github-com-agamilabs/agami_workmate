<?php
$base_path = dirname(dirname(__FILE__));
//echo $base_path;
require_once($base_path . "/db/Database.php");

$db = new Database();
$dbcon = $db->db_connect();
if (!$db->is_connected()) {
    $response['error'] = true;
    $response['message'] = "Database is not connected!";
    echo json_encode($response);
    exit();
}
?>