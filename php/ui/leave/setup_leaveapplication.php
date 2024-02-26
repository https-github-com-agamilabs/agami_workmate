<?php
date_default_timezone_set("Asia/Dhaka");
include_once  dirname(dirname(__FILE__)) . "/login/check_session.php";

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

try {

    $base_path = dirname(dirname(dirname(__FILE__)));
    require_once($base_path . "/db/Database.php");

    $db = new Database();
    $dbcon = $db->db_connect();
    if (!$db->is_connected()) {
        throw new \Exception("Database is not connected!", 1);
    }

    $dbcon->begin_transaction();

    if(isset($_SESSION['wm_orgno'])){
        $orgno=(int) $_SESSION['wm_orgno'];
    }else{
        throw new \Exception("You must select an organization!", 1);
    }

    if (isset($_SESSION['wm_userno'])) {
        $userno = (int) $_SESSION['wm_userno'];
    } else {
        throw new \Exception("You must login first!", 1);
    }

    if (isset($_POST['lappno'])) {
        $lappno = (int) $_POST['lappno'];
    } else {
        $lappno = -1;
    }

    if (isset($_POST['leavetypeno'])) {
        $leavetypeno = (int) $_POST['leavetypeno'];
    } else {
        throw new \Exception("You must login first!", 1);
    }

    if (isset($_POST['reasontext'])) {
        $reasontext = trim(strip_tags($_POST['reasontext']));
    } else {
        $reasontext = NULL;
    }

    // $leavestatusno = 1;
    // if (isset($_POST['leavestatusno'])) {
    //     $leavestatusno = (int) $_POST['leavestatusno'];
    // } else {
    //     throw new \Exception("Leave Status is not set!", 1);
    // }

    $loginuserinfo = get_user_info($dbcon, $userno);
    if ($loginuserinfo->num_rows != 1) {
        throw new \Exception("User not found!", 1);
    }

    $loginuserinfo = $loginuserinfo->fetch_array(MYSQLI_ASSOC);


    if ($lappno > 0) {

        $leavestatusno = 1;
        if (isset($_POST['leavestatusno'])) {
            $leavestatusno = (int) $_POST['leavestatusno'];
        } else {
            throw new \Exception("Leave Status is not set!", 1);
        }

        $application = get_leaveapplication($dbcon, $lappno);
        if ($application->num_rows != 1) {
            throw new \Exception("Application not found!", 1);
        }
        $application = $application->fetch_array(MYSQLI_ASSOC);

        $actiontakenby = $userno;
        // lappno, empno, leavetypeno, reasontext, leavestatusno, createdatetime, updatetime
        $nos = update_leaveapplication($dbcon, $leavetypeno, $reasontext, $leavestatusno, $actiontakenby, $orgno, $lappno);
        if ($nos < 0) {
            throw new \Exception("Failed! Could not Update.", 1);
        }

        if ($dbcon->commit()) {


            $updateby = $loginuserinfo['fullname'];

            $emp_name = $application['fullname'];
            $jobtitle = $application['jobtitle'];
            $leavedates_str = $application['leavedays'];

            $payload = array();
            $payload['to'] = "agamilabs@gmail.com"; // mandatory
            $payload['cc'] = "khanam.toslima@gmail.com"; // optional, mandatory for keeping record
            $payload['subject'] = "Leave Application Updated"; // optional
            $payload['message'] = get_leaveapplication_update_body($lappno, $emp_name, $jobtitle, $leavedates_str, $reasontext, $updateby); // optional
            $payload['from'] = "info@agamilabs.com"; // mandatory
            $emailed = sendMail($payload);


            $response['error'] = false;
            $response['message'] = "Application Update Successfully.";
            $response['email'] = $emailed;
        } else {
            throw new \Exception("Failed! Please try again.", 1);
        }
    } else {
        $leavestatusno = 1;
        if (isset($_POST['leavestatusno'])) {
            $leavestatusno = (int) $_POST['leavestatusno'];
        } else {
            // throw new \Exception("Leave Status is not set!", 1);
        }
        if (isset($_POST['leavedates'])) {
            $leavedates = json_decode($_POST['leavedates'], true);
        } else {
            throw new \Exception("Leave Status is not set!", 1);
        }

        $lappno = insert_leaveapplication($dbcon, $orgno, $userno, $leavetypeno, $reasontext, $leavestatusno);
        if ($lappno <= 0) {
            throw new \Exception("Failed! Could not Insert.", 1);
        }

        // dont delete dates before today
        $r = delete_leavedates($dbcon, $lappno);

        for ($i = 0; $i < count($leavedates); $i++) {
            $ldr = insert_leavedates($dbcon, $lappno, $leavedates[$i]);
            if ($ldr <= 0) {
                throw new \Exception("Failed! Please try again.", 1);
            }
        }

        if ($dbcon->commit()) {

            $emp_name = $loginuserinfo['fullname'];
            $jobtitle = $loginuserinfo['jobtitle'];
            $leavedates_str = implode(", ", $leavedates);

            $payload = array();
            $payload['to'] = "agamilabs@gmail.com"; // mandatory
            $payload['cc'] = "khanam.toslima@gmail.com"; // optional, mandatory for keeping record
            $payload['subject'] = "Leave Application Submitted"; // optional
            $payload['message'] = get_leaveapplication_body($emp_name, $jobtitle, $leavedates_str, $reasontext); // optional
            $payload['from'] = "info@agamilabs.com"; // mandatory
            $emailed = sendMail($payload);

            $response['error'] = false;
            $response['message'] = "Application Submitted Successfully.";
            $response['email'] = $emailed;
        } else {
            throw new \Exception("Failed! Please try again.", 1);
        }
    }
} catch (Exception $e) {
    $dbcon->rollback();
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
if (isset($dbcon)) {
    $dbcon->close();
}



/**
 * Local Function
 */

function get_user_info($dbcon, $userno)
{
    $sql = "SELECT userno,username,firstname,lastname,
                    CONCAT(firstname, ' ', lastname) as fullname,
                    affiliation,jobtitle,email,primarycontact,
                    passphrase,createtime,lastupdatetime,
                    ucatno, (SELECT ucattitle FROM hr_usercat WHERE ucatno=u.ucatno) as ucattitle,
                    isactive
               FROM hr_user as u
               WHERE isactive=1 AND userno=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $userno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

//emp_leaveapplication(lappno,orgno,empno,leavetypeno,reasontext,leavestatusno,actiontakenby,createdatetime,updatetime)
function update_leaveapplication($dbcon, $leavetypeno, $reasontext, $leavestatusno, $actiontakenby, $orgno, $lappno)
{
    $sql = "UPDATE emp_leaveapplication
            SET leavetypeno=?, reasontext=?, leavestatusno=?, actiontakenby=?
            WHERE orgno=? AND lappno=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("isiiii", $leavetypeno, $reasontext, $leavestatusno, $actiontakenby, $orgno, $lappno);
    $stmt->execute();
    return $stmt->affected_rows;
}

//emp_leaveapplication(lappno,orgno,empno,leavetypeno,reasontext,leavestatusno,actiontakenby,createdatetime,updatetime)
function insert_leaveapplication($dbcon, $orgno, $userno, $leavetypeno, $reasontext, $leavestatusno)
{
    $sql = "INSERT INTO emp_leaveapplication
            (orgno,empno, leavetypeno, reasontext, leavestatusno)
            VALUES (?,?,?,?,?)";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("iiisi", $orgno,$userno, $leavetypeno, $reasontext, $leavestatusno);
    $stmt->execute();
    return $stmt->insert_id;
}


//emp_leaveapplication(lappno,orgno,empno,leavetypeno,reasontext,leavestatusno,actiontakenby,createdatetime,updatetime)
function get_leaveapplication($dbcon, $lappno)
{
    $sql = "SELECT
            *,
            (SELECT CONCAT(firstname, ' ', lastname) as fullname FROM hr_user WHERE userno=la.empno) as fullname,
            (SELECT jobtitle FROM hr_user WHERE userno=la.empno) as jobtitle,
            (SELECT GROUP_CONCAT(leavedate)
                FROM emp_leavedates
                WHERE lappno=la.lappno) as leavedays
            FROM emp_leaveapplication as la
            WHERE lappno=?";
    $stmt = $dbcon->prepare($sql);
    if ($dbcon->error) {
        echo $dbcon->error;
    }
    $stmt->bind_param("i", $lappno);
    $stmt->execute();
    return $stmt->get_result();
}

// emp_leavedates(lappno, leavedate)
function insert_leavedates($dbcon, $lappno, $leavedate)
{
    $sql = "INSERT INTO emp_leavedates
            (lappno, leavedate)
            VALUES (?,?)";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("is", $lappno, $leavedate);
    $stmt->execute();
    if ($stmt->affected_rows <= 0) {
        echo $stmt->error;
    }
    return $stmt->affected_rows;
}

function delete_leavedates($dbcon, $lappno)
{
    $today = date('Y-m-d');
    $sql = "DELETE FROM emp_leavedates
            WHERElappno=? AND leavedate > ?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("is", $lappno, $today);
    $stmt->execute();
    return $stmt->affected_rows;
}


function validateEmail($email)
{
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        return false;
    }

    return true;
}


function sendMail($payload)
{
    //$from = "ictcell@example.com"; // hosting server email acc

    if (isset($payload['from']) && validateEmail($payload['from'])) {
        $from = $payload['from'];
    } else {
        return false;
    }

    $to = "";
    if (isset($payload['to']) && validateEmail($payload['to'])) {
        $to = $payload['to'];
    } else {
        return false;
    }

    $cc = "";
    if (isset($payload['cc']) && validateEmail($payload['cc'])) {
        $cc = $payload['cc'];
    }

    $subject = "MAIL FROM Workmate";
    if (isset($payload['subject'])) {
        $subject = $payload['subject'];
    }

    $message = "MAIL FROM Workmate";
    if (isset($payload['message'])) {
        $message = $payload['message'];
    }

    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    // More headers
    $headers .= 'From: ' . $from . "\r\n";
    if (strlen($cc) > 0 && isset($payload['cc'])) {
        $headers .= 'Cc: ' . $cc . "\r\n";
    }

    // require 'restrict_emails.php';
    // if (!is_email_allowed($to)) {
    //     return false;
    // }

    return mail($to, $subject, $message, $headers);
}

function get_leaveapplication_body($emp_name, $jobtitle, $leavedates, $reasontext)
{
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";


    return '<table cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <td>
                    Dear,<br/>
                    A leave application was received from one of your employees.<br/>
                    Employee Name: <b>' . $emp_name . '</b>.<br/>
                    Employee ID: ' . $jobtitle . '<br/>
                    Reason: ' . $reasontext . '<br/>
                    Applied Time: ' . date('Y-m-d H:i:s') . '
                    
                    Leave Dates: ' . $leavedates . '<br/><br/>

                    <br/><br/>
                    Please click on the bellow <b>\'Take Action\'</b> button to take necessary action.
                    <br/>
                    Thank you.
                  </td>
                </tr>
                <tr>
                  <td align="center">
                    <table cellpadding="0" cellspacing="0" width="200" border="0" style="border-collapse: separate !important;">
                      <tr>
                        <td style="background-color:#56CFD2;color:#FFFFFF;font-size:18px;padding:10px 10px;border-radius:3px;font-family:Arial, Helvetica, sans-serif;text-align:center;border:solid 1px #FFFFFF">
                          <a target="_blank" href="' . $actual_link . '" style="color:#FFFFFF;text-decoration:none;">Take Action</a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>';
}



function get_leaveapplication_update_body($lappno, $emp_name, $jobtitle, $leavedates, $reasontext, $updateby)
{
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";


    return '<table cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <td>
                    Dear,<br/>
                    A previously submitted leave application from one of your employees was updated by ' . $updateby . '.<br/>
                    Employee Name: <b>' . $emp_name . '</b>.<br/>
                    Employee ID: ' . $jobtitle . '<br/>
                    Reason: ' . $reasontext . '<br/>
                    Applied Time: ' . date('Y-m-d H:i:s') . '<br/><br/>
                    
                    Leave Dates: ' . $leavedates . '<br/><br/>

                    Please click on the bellow <b>\'Take Action\'</b> button to take necessary action.
                    <br/>
                    Thank you.
                  </td>
                </tr>
                <tr>
                  <td align="center">
                    <table cellpadding="0" cellspacing="0" width="200" border="0" style="border-collapse: separate !important;">
                      <tr>
                        <td style="background-color:#56CFD2;color:#FFFFFF;font-size:18px;padding:10px 10px;border-radius:3px;font-family:Arial, Helvetica, sans-serif;text-align:center;border:solid 1px #FFFFFF">
                          <a target="_blank" href="' . $actual_link . '" style="color:#FFFFFF;text-decoration:none;">Take Action</a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>';
}