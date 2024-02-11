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

    if (isset($_POST['userno']) && strlen($_POST['userno']) > 0) {
        $userno = (int) $_POST['userno'];
    } else {
        throw new Exception("Employee must be selected!!", 1);
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

//com_userattlocset (attlocno,orgno,userno, locno,mindistance,starttime,endtime)
//com_workinglocation(locno,locname,loclat,loclon,active)
function get_user_wherework($dbcon, $orgno,$userno)
{
    $sql = "SELECT wl.locno, wl.loclat, wl.loclon, wl.mindistance, uls.starttime, uls.endtime
            FROM 
                com_userattlocset as uls
            INNER JOIN 
                com_workinglocation as wl 
            ON uls.locno=wl.locno
            WHERE uls.orgno=? 
                AND uls.userno =?
                AND uls.starttime> NOW()
            ";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ii", $orgno,$userno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}
