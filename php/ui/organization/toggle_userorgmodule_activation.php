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

    //orgno, userno, moduleno
    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int)$_POST['orgno'];
    }else{
        throw new \Exception("Organization must be selected!", 1);
    }

    if (isset($_POST['userno']) && strlen($_POST['userno']) > 0) {
        $userno = (int)$_POST['userno'];
    }else{
        throw new \Exception("User must be selected!", 1);
    }

    if (isset($_POST['moduleno']) && strlen($_POST['moduleno']) > 0) {
        $moduleno = (int)$_POST['moduleno'];
    }else{
        throw new \Exception("Module must be selected!", 1);
    }

    $anos=toggle_userorgmodule_activation($dbcon, $orgno, $userno, $moduleno);
    if($anos>0){
        $response['error'] = false;
        $response['message'] = "Updated Successfully.";
    }else{
        throw new \Exception("Could not update!", 1);
    }

} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//acc_userorgmodules (orgno, userno, moduleno, verified)
function toggle_userorgmodule_activation($dbcon, $orgno, $userno, $moduleno)
{

    $sql = "UPDATE acc_userorgmodules
            SET verified=abs(1-verified)
            WHERE orgno=? AND userno=? AND moduleno=?";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: ".$dbcon->error);
    }

    $stmt->bind_param("iii", $orgno, $userno, $moduleno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}

?>
