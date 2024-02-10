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

    $loclon=-1;
    if (isset($_POST['loclon']) && strlen($_POST['loclon']) > 0) {
        $loclon = (int) $_POST['loclon'];
    }

    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int) $_POST['orgno'];
    } else {
        throw new Exception("Organization must be selected!!", 1);
    }

    //$locname,$loclat,$loclon
    if (isset($_POST['locname']) && strlen($_POST['locname']) > 0) {
        $locname = trim(strip_tags($_POST['locname']));
    } else {
        throw new Exception("Location name cannot be empty!!", 1);
    }

    if (isset($_POST['loclat']) && strlen($_POST['loclat']) > 0) {
        $loclat = (double) $_POST['loclat'];
    } else {
        throw new Exception("Latitude cannot be empty!!", 1);
    }

    if (isset($_POST['loclon']) && strlen($_POST['loclon']) > 0) {
        $loclon = (double) $_POST['loclon'];
    } else {
        throw new Exception("Longitude cannot be empty!!", 1);
    }

    if($loclon>0){
        $unos = update_workinglocation($dbcon,  $locname,$loclat,$loclon,$orgno,$locno);
    
        if ($unos == 0) {
            throw new Exception("Could not update!", 1);
        } else {
            $response['error'] = false;
            $response['message'] = 'Updated successfully.';
        }
    }else{
        $inos = add_workinglocation($dbcon, $orgno, $locname,$loclat,$loclon);
    
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


//com_workinglocation(locno,orgno,locname,loclat,loclon,active)
function add_workinglocation($dbcon, $orgno, $locname,$loclat,$loclon)
{
    $sql = "INSERT INTO com_workinglocation(orgno,locname,loclat,loclon,active)
            VALUES(?,?,?,?,1)";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("isdd", $orgno, $locname,$loclat,$loclon);

    if ($stmt->execute()) {
        $result = $stmt->insert_id;
        $stmt->close();
        return $result;
    } else {
        return 0;
    }
}

function update_workinglocation($dbcon,  $locname,$loclat,$loclon,$orgno,$locno)
{
    $sql = "UPDATE com_workinglocation
            SET locname=?, loclat=?,loclon=?
            WHERE orgno=? AND locno=?";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("sddii", $locname,$loclat,$loclon,$orgno,$locno);

    if ($stmt->execute()) {
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    } else {
        return 0;
    }
}
