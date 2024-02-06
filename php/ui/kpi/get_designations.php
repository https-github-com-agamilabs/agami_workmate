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

    if (isset($_SESSION['wm_userno'])) {
        $userno = (int) $_SESSION['wm_userno'];
    } else {
        throw new \Exception("You must login first!", 1);
    }

    $ucatno = 0;
    if (isset($_SESSION['wm_ucatno'])) {
        $ucatno = (int) $_SESSION['wm_ucatno'];
    }

    $result = get_designations($dbcon);
    $data_array = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $data_array[] = $row;
        }
    }
    $response['error'] = false;
    $response['data'] = $data_array;
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();


/*
    *   LOCAL FUNCTIONS
    */

// hr_designationsetting(desigid,desigtitle,isactive)
function get_designations($dbcon)
{

    $sql = "SELECT desigid,desigtitle
            FROM hr_designationsetting
            WHERE isactive>=1";
    $stmt = $dbcon->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
