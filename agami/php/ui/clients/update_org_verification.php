<?php
include_once  dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/php/ui/login/check_session.php";

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

try {
    $base_path = dirname(dirname(dirname(__FILE__)));

    // require_once($base_path."/admin/db/Database.php");
    // require_once($base_path . "/admin/operations/Select.php");

    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/php/ui/dependency_checker.php";

    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int)$_POST['orgno'];
    } else {
        throw new \Exception("You must select an organization!", 1);
    }

    if (isset($_POST['verifiedno']) && strlen($_POST['verifiedno']) > 0) {
        $verifiedno = (int)$_POST['verifiedno'];
    } else {
        throw new \Exception("You must select a verification status!", 1);
    }

    $result = update_org_verification($dbcon, $orgno, $verifiedno);

    if ($result > 0) {
        $response['error'] = false;
        $response['message'] = "Modified successfully!";
    } else {
        throw new \Exception("Could not modify!", 1);
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//com_orgs (orgno, orgname, street, city, state, country, gpslat, gpslon, orgtypeid, privacy, picurl, contactno, orgnote, weekend1, weekend2, starttime, endtime, verifiedno)
function update_org_verification($dbcon, $orgno, $verifiedno)
{
    // date_default_timezone_set("Asia/Dhaka");
    // $updatetime = date("Y-m-d H:i:s");

    $sql = "UPDATE com_orgs
            SET verifiedno=?
            WHERE orgno=?";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: " . $dbcon->error);
    }

    $stmt->bind_param("ii", $verifiedno, $orgno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}
