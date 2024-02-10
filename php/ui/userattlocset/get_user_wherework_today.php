<?php
$base_path = dirname(dirname(dirname(__FILE__)));
include_once($base_path . "/ui/login/check_session.php");

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";


try {
    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int) $_POST['orgno'];
    } else {
        if (!isset($orgno) || strlen($orgno) <= 0) {
            throw new Exception("Organization must be selected!!", 1);
        }
    }

    $rs_wherework = get_user_wherework($dbcon, $orgno,$userno);

    if ($rs_wherework->num_rows > 0) {
        $meta_array = array();
        while ($row = $rs_wherework->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
        }
        $response['error'] = false;
        $response['results'] = $meta_array;
    } else {
        throw new \Exception("No Data Found!", 1);
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//com_userattlocset (attlocno,orgno,userno, loclat, loclon,starttime,endtime)
function get_user_wherework($dbcon, $orgno,$userno)
{
    $sql = "SELECT loclat, loclon,starttime,endtime
            FROM com_userattlocset
            WHERE orgno=? 
                AND userno =?
                AND (CURRENT_DATE() BETWEEN DATE(starttime) AND DATE(DATE_ADD(starttime, INTERVAL duration DAY)))
            ";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ii", $orgno,$userno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}
