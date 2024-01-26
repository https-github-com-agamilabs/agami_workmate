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
    }else{
        throw new Exception("Organization must be selected", 1);
    }

    $dbcon->begin_transaction();
    $duorg=del_userorgs($dbcon, $orgno, $userno);
    if ($duorg > 0) {
        $dnos = del_org($dbcon, $orgno);
        if ($dnos > 0 && $dbcon->commit()) {
            $response['error'] = false;
            $response['message'] = "Removed Successfully.";
        } else {
            $dbcon->rollback();
            throw new \Exception("Could not remove organization!", 1);
        }
    }else{
        $dbcon->rollback();
        throw new \Exception("You are not eligible to delete!", 1);
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

function del_org($dbcon, $orgno)
{
    $sql = "DELETE
            FROM acc_orgs
            WHERE orgno=?";

    $stmt = $dbcon->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $orgno);
        if ($stmt->execute()) {
            $result = $stmt->affected_rows;
            $stmt->close();
            return $result;
        } else {
            $stmt->close();
            return 0;
        }
    } else {
        return 0;
    }
}

//acc_userorgmodules(orgno,userno,moduleno,verified)
function del_userorgs($dbcon, $orgno, $userno)
{
    $sql = "DELETE
            FROM acc_userorgmodules
            WHERE orgno=? AND userno=?";

    $stmt = $dbcon->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('ii', $orgno, $userno);
        if ($stmt->execute()) {
            $result = $stmt->affected_rows;
            $stmt->close();
            return $result;
        } else {
            $stmt->close();
            return 0;
        }
    } else {
        return 0;
    }
}

