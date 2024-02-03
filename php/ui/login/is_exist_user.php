<?php

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method!";
    echo json_encode($response);
    exit();
}

require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try {
    //$primarycontact,$email
    if (isset($_POST['primarycontact']) && strlen($_POST['primarycontact']) > 0) {
        $primarycontact = trim(strip_tags($_POST['primarycontact']));
    } else {
        throw new Exception("Contact no cannot be empty!!", 1);
    }

    $result = is_exist_user_by_primarycontact($dbcon, $primarycontact);
    if ($result > 0) {
        throw new Exception("This contact number is already registered! Please login with your username or recover your password by clicking 'Forget Password'.", 1);
    } else {
        $response['message'] = "You may proceed with this contact number.";
        $response['error'] = false;
    }
} catch (\Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}


echo json_encode($response);
$dbcon->close();

// hr_user(userno, username, firstname, lastname, affiliation, jobtitle, email, primarycontact, passphrase, ucatno, supervisor, permissionlevel, createtime, lastupdatetime, isactive)
function is_exist_user_by_primarycontact($dbcon, $primarycontact)
{
    $sql = "SELECT userno
            FROM hr_user
            WHERE userstatusno>=1 AND primarycontact=?";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("s", $primarycontact);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0)
        return 1;
    else
        return 0;
}
?>