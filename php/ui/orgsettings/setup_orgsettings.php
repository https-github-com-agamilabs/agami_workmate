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
    //orgno,setid, setlabel, fileurl
    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int) $_POST['orgno'];
    } else {
        throw new Exception("Organization must be selected!!", 1);
    }

    if (isset($_POST['setid']) && strlen($_POST['setid']) > 0) {
        $setid = trim(strip_tags($_POST['setid']));
    } else {
        throw new Exception("Setting must be selected!!", 1);
    }

    $setlabel = NULL;
    if (isset($_POST['setlabel']) && strlen($_POST['setlabel']) > 0) {
        $setlabel = trim(strip_tags($_POST['setlabel']));
    }

    $fileurl = NULL;
    if (isset($_POST['fileurl']) && strlen($_POST['fileurl']) > 0) {
        $fileurl = trim(strip_tags($_POST['fileurl']));
    }

    $inos = setup_orgsettings($dbcon, $orgno, $setid, $setlabel, $fileurl);

    if ($inos == 0) {
        throw new Exception("Could not save!", 1);
    } else {
        $response['error'] = false;
        $response['message'] = 'Saved successfully.';
    }
} catch (\Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();


//com_orgsettings(orgno,setid, setlabel, fileurl)
function setup_orgsettings($dbcon, $orgno, $setid, $setlabel, $fileurl)
{
    $sql = "INSERT INTO com_orgsettings(orgno,setid, setlabel, fileurl)
            VALUES(?,?,?,?)
            ON DUPLICATE KEY UPDATE setlabel=?, fileurl=?";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("isssss", $orgno, $setid, $setlabel, $fileurl, $setlabel, $fileurl);

    if ($stmt->execute()) {
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    } else {
        return 0;
    }
}
