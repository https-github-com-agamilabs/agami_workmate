<?php
include_once  dirname(dirname(__FILE__)) . "/login/check_session.php";

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

try {

    $base_path = dirname(dirname(dirname(__FILE__)));
    require_once($base_path . "/db/Database.php");

    $db = new Database();
    $dbcon = $db->db_connect();
    if (!$db->is_connected()) {
        throw new \Exception("Database is not connected!", 1);
    }

    if (isset($_POST['specialdayno'])) {
        $specialdayno = (int) $_POST['specialdayno'];
    } else {
        throw new \Exception("No specialday Selected!", 1);
    }

    $status = delete_specialday($dbcon, $specialdayno);

    if ($status > 0) {
        $response['error'] = false;
        $response['message'] = "specialday is removed.";
    } else {
        $response['error'] = true;
        $response['message'] = "Cannot Delete specialday!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

$dbcon->close();

function delete_specialday($dbcon, $specialdayno)
{
    $sql = "DELETE
                FROM emp_specialdays
                WHERE specialdayno=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $specialdayno);
    $stmt->execute();
    return $stmt->affected_rows;
}
