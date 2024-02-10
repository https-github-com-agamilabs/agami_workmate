<?php
$base_path = dirname(dirname(dirname(__FILE__)));
include_once($base_path . "/ui/login/check_session.php");

$response = array();
$response['error'] = false;
$response['message'] = '';
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = 'Invalid request method!';
    echo json_encode($response);
    exit();
}

require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try {

    $attlocno=-1;
    if (isset($_POST['attlocno']) && strlen($_POST['attlocno']) > 0) {
        $attlocno = (int) $_POST['attlocno'];
    }

    //orgno,setid, setlabel, fileurl
    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int) $_POST['orgno'];
    } else {
        throw new Exception("Organization must be selected!!", 1);
    }

    if (isset($_POST['userno']) && strlen($_POST['userno']) > 0) {
        $userno = (int) $_POST['userno'];
    } else {
        throw new Exception("Employee must be selected!!", 1);
    }

    if (isset($_POST['loclat']) && strlen($_POST['loclat']) > 0) {
        $loclat = (double) $_POST['loclat'];
    } else {
        throw new Exception("Latitude cannot be empty!!", 1);
    }

    if (isset($_POST['loclon']) && strlen($_POST['loclon']) > 0) {
        $loclon = (int) $_POST['loclon'];
    } else {
        throw new Exception("Longitude cannot be empty!!", 1);
    }

    if (isset($_POST['starttime']) && strlen($_POST['starttime']) > 0) {
        $starttime = trim(strip_tags($_POST['starttime']));
    } else {
        throw new Exception("Starting time cannot be empty!!", 1);
    }

    if (isset($_POST['stendtimearttime']) && strlen($_POST['endtime']) > 0) {
        $endtime = trim(strip_tags($_POST['endtime']));
    } else {
        throw new Exception("End time cannot be empty!!", 1);
    }

    if($attlocno>0){
        $unos = update_userattlocset($dbcon,  $loclat, $loclon,$starttime,$endtime,$orgno,$attlocno);
    
        if ($unos == 0) {
            throw new Exception("Could not update!", 1);
        } else {
            $response['error'] = false;
            $response['message'] = 'Updated successfully.';
        }
    }else{
        $inos = add_userattlocset($dbcon, $orgno, $userno, $loclat, $loclon,$starttime,$endtime);
    
        if ($inos == 0) {
            throw new Exception("Could not add!", 1);
        } else {
            $response['error'] = false;
            $response['message'] = 'Added successfully.';
        }
    }
} catch (\Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();


//com_userattlocset (attlocno,orgno,userno, loclat, loclon,starttime,endtime)
function add_userattlocset($dbcon, $orgno, $userno, $loclat, $loclon,$starttime,$endtime)
{
    $sql = "INSERT INTO com_userattlocset(orgno,userno, loclat, loclon,starttime,endtime)
            VALUES(?,?,?,?,?,?)";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("iiddss", $orgno, $userno, $loclat, $loclon,$starttime,$endtime);

    if ($stmt->execute()) {
        $result = $stmt->insert_id;
        $stmt->close();
        return $result;
    } else {
        return 0;
    }
}

function update_userattlocset($dbcon,  $loclat, $loclon,$starttime,$endtime,$orgno,$attlocno)
{
    $sql = "UPDATE com_userattlocset
            SET loclat=?, loclon=?,starttime=?,endtime=?
            WHERE orgno=? AND attlocno=?";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("ddssii", $loclat, $loclon,$starttime,$endtime,$orgno,$attlocno);

    if ($stmt->execute()) {
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    } else {
        return 0;
    }
}
