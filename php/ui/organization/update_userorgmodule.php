<?php
include_once  dirname(dirname(__FILE__)) . "/login/check_session.php";

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

$base_path = dirname(dirname(dirname(__FILE__)));
require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try {

    //orgno, userno, moduleno
    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int)$_POST['orgno'];
    } else {
        throw new \Exception("Organization must be selected!", 1);
    }

    if (isset($_POST['foruserno']) && strlen($_POST['foruserno']) > 0) {
        $foruserno = (int)$_POST['foruserno'];
    } else {
        throw new \Exception("User must be selected!", 1);
    }

    if (isset($_POST['new_moduleno']) && strlen($_POST['new_moduleno']) > 0) {
        $new_moduleno = (int)$_POST['new_moduleno'];
    } else {
        throw new \Exception("Module must be selected!", 1);
    }

    if (isset($_POST['old_moduleno']) && strlen($_POST['old_moduleno']) > 0) {
        $old_moduleno = (int)$_POST['old_moduleno'];
    } else {
        throw new \Exception("Module must be selected!", 1);
    }

    $anos = update_userorgmodule($dbcon, $orgno, $foruserno, $new_moduleno, $old_moduleno);
    if ($anos > 0) {
        $response['error'] = false;
        $response['message'] = "Updated Successfully.";
    } else {
        throw new \Exception("Could not update!", 1);
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//com_userorgmodules (orgno, userno, moduleno, verified)
function update_userorgmodule($dbcon, $orgno, $foruserno, $new_moduleno, $old_moduleno)
{

    $sql = "UPDATE com_userorgmodules
            SET moduleno=?, verified=0
            WHERE orgno=? AND userno=? AND moduleno=?";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: " . $dbcon->error);
    }

    $stmt->bind_param("iiii", $new_moduleno, $orgno, $foruserno, $old_moduleno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}
