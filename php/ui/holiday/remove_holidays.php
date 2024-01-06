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

    if (isset($_POST['holidayno'])) {
        $holidayno = (int) $_POST['holidayno'];
    } else {
        throw new \Exception("No Holiday Selected!", 1);
    }

    $status = delete_holiday($dbcon, $holidayno);

    if ($status > 0) {
        $response['error'] = false;
        $response['message'] = "Holiday is removed.";
    } else {
        $response['error'] = true;
        $response['message'] = "Cannot Delete Holiday!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

$dbcon->close();

function delete_holiday($dbcon, $holidayno)
{
    $sql = "DELETE
                FROM emp_holidays
                WHERE holidayno=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $holidayno);
    $stmt->execute();
    return $stmt->affected_rows;
}
