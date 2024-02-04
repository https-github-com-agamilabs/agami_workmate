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
    //$username,$firstname,$lastname,$email,$countrycode,$primarycontact,$passphrase,$authkey,$userstatusno
    if (isset($_POST['username']) && strlen($_POST['username']) > 0) {
        $username = trim(strip_tags($_POST['username']));
    } else {
        throw new Exception("User-name cannot be empty!!", 1);
    }

    if (isset($_POST['firstname']) && strlen($_POST['firstname']) > 0) {
        $firstname = trim(strip_tags($_POST['firstname']));
    } else {
        throw new Exception("Your first-name cannot be empty!!", 1);
    }

    $lastname = NULL;
    if (isset($_POST['lastname']) && strlen($_POST['lastname']) > 0) {
        $lastname = trim(strip_tags($_POST['lastname']));
    }

    $affiliation = NULL;
    if (isset($_POST['affiliation']) && strlen($_POST['affiliation']) > 0) {
        $affiliation = trim(strip_tags($_POST['affiliation']));
    }

    $jobtitle = NULL;
    if (isset($_POST['jobtitle']) && strlen($_POST['jobtitle']) > 0) {
        $jobtitle = trim(strip_tags($_POST['jobtitle']));
    }

    if (isset($_POST['email']) && strlen($_POST['email']) > 0) {
        $email = trim(strip_tags($_POST['email']));
    } else {
        throw new Exception("Email cannot be empty!!", 1);
    }

    $countrycode = '+880';
    if (isset($_POST['countrycode']) && strlen($_POST['countrycode']) > 0) {
        $countrycode = trim(strip_tags($_POST['countrycode']));
    }

    if (isset($_POST['primarycontact']) && strlen($_POST['primarycontact']) > 0) {
        $primarycontact = trim(strip_tags($_POST['primarycontact']));
    } else {
        throw new Exception("Your contact-no cannot be empty!!", 1);
    }

    if (isset($_POST['password']) && strlen($_POST['password']) > 0) {
        $password = trim(strip_tags($_POST['password']));
        $passphrase = password_hash($password, PASSWORD_DEFAULT);
    } else {
        throw new Exception("Check you credential and try again!!", 1);
    }

    $authkey = NULL;
    $userstatusno = 1;

    $userno = add_user($dbcon, $username, $firstname, $lastname,$affiliation,$jobtitle, $email, $countrycode, $primarycontact, $passphrase, $authkey, $userstatusno);
    if ($userno > 0) {
        $response['error'] = false;
        $response['message'] = "Your registration is successful. Please log in to enjoy services.";
    } else {
        throw new Exception("Could not process user data!", 1);
    }
} catch (\Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}


echo json_encode($response);
$dbcon->close();

// hr_user(userno,username,firstname,lastname,affiliation,jobtitle,email,primarycontact,passphrase,authkey,ucatno,supervisor,permissionlevel,createtime,lastupdatetime,isactive)
function add_user($dbcon, $username, $firstname, $lastname,$affiliation,$jobtitle, $email, $countrycode, $primarycontact, $passphrase, $authkey, $userstatusno)
{
    date_default_timezone_set("Asia/Dhaka");
    $createtime = date("Y-m-d H:i:s");

    $sql = "INSERT INTO hr_user(username,firstname,lastname,,affiliation,jobtitleemail,countrycode,primarycontact,passphrase,authkey,userstatusno,createtime,lastupdatetime,isactive)
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,1)";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ssssssssssiss", $username, $firstname, $lastname, $affiliation,$jobtitle, $email, $countrycode, $primarycontact, $passphrase, $authkey, $userstatusno, $createtime, $createtime);
    $stmt->execute();
    $result = $stmt->insert_id;
    $stmt->close();

    return $result;
}
?>