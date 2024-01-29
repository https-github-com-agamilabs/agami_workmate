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

    if (isset($_POST['orguser']) && strlen($_POST['orguser']) > 0) {
        $orguser = (int)$_POST['orguser'];
    } else {
        throw new \Exception("You must select a user!", 1);
    }

    if (isset($_POST['userstatusno']) && strlen($_POST['userstatusno']) > 0) {
        $userstatusno = (int)$_POST['userstatusno'];
    } else {
        throw new \Exception("You must select user-status to change!", 1);
    }

    $result = update_userstatus($dbcon, $orguser, $userstatusno);

    $meta_array = array();
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

//hr_user (userno,username,firstname,lastname,email,countrycode,primarycontact,passphrase,authkey,userstatusno,ucreatedatetime,reset_pass_count,updatetime)
function update_userstatus($dbcon, $orguser, $userstatusno)
{
    date_default_timezone_set("Asia/Dhaka");
    $updatetime = date("Y-m-d H:i:s");

    $sql = "UPDATE hr_user AS o
            SET userstatusno=?, updatetime=?
            WHERE userno=?";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: " . $dbcon->error);
    }

    $stmt->bind_param("isi", $userstatusno, $updatetime, $orguser);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}
