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

    if (isset($_POST['locno']) && strlen($_POST['locno']) > 0) {
        $locno = (int) $_POST['locno'];
    } else {
        throw new Exception("You must select a working location!!", 1);
    }

    $mindistance=25;
    if (isset($_POST['mindistance']) && strlen($_POST['mindistance']) > 0) {
        $mindistance = (int) $_POST['mindistance'];
    }

    if (isset($_POST['starttime']) && strlen($_POST['starttime']) > 0) {
        $starttime = trim(strip_tags($_POST['starttime']));
    } else {
        throw new Exception("Starting time cannot be empty!!", 1);
    }

    if (isset($_POST['endtime']) && strlen($_POST['endtime']) > 0) {
        $endtime = trim(strip_tags($_POST['endtime']));
    } else {
        throw new Exception("End time cannot be empty!!", 1);
    }

    if($attlocno>0){
        $unos = update_userattlocset($dbcon,  $locno, $mindistance,$starttime,$endtime,$orgno,$attlocno);
    
        if ($unos == 0) {
            throw new Exception("Could not update!", 1);
        } else {
            $response['error'] = false;
            $response['message'] = 'Updated successfully.';
        }
    }else{
        $inos = add_userattlocset($dbcon, $orgno, $userno, $locno, $mindistance,$starttime,$endtime);
    
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


//com_userattlocset (attlocno,orgno,userno, locno, mindistance,starttime,endtime)
function add_userattlocset($dbcon, $orgno, $userno, $locno, $mindistance,$starttime,$endtime)
{
    $sql = "INSERT INTO com_userattlocset(orgno,userno, locno, mindistance,starttime,endtime)
            VALUES(?,?,?,?,?,?)";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("iiiiss", $orgno, $userno, $locno, $mindistance,$starttime,$endtime);

    if ($stmt->execute()) {
        $result = $stmt->insert_id;
        $stmt->close();
        return $result;
    } else {
        return 0;
    }
}

function update_userattlocset($dbcon,  $locno, $mindistance,$starttime,$endtime,$orgno,$attlocno)
{
    $sql = "UPDATE com_userattlocset
            SET locno=?, mindistance=?,starttime=?,endtime=?
            WHERE orgno=? AND attlocno=?";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("iissii", $locno, $mindistance,$starttime,$endtime,$orgno,$attlocno);

    if ($stmt->execute()) {
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    } else {
        return 0;
    }
}
