<?php

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method!";
    echo json_encode($response);
    exit();
}

require_once "recaptcha.php";
is_recaptcha_pass();

require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";
require_once(dirname(dirname(dirname(__FILE__))) . "/utility/Utils.php");
$utils = new Utils();

try {
    if (isset($_POST['email']) && strlen($_POST['email']) > 0) {
        $email = trim(strip_tags($_POST['email']));
    } else {
        throw new Exception("Email cannot be empty!!", 1);
    }

    $rs_user = get_user_info($dbcon, $email);
    if ($rs_user->num_rows > 0) {
        $user = $rs_user->fetch_array(MYSQLI_ASSOC);

        $RESET_METHOD = "OTP";

        if ($RESET_METHOD == "URL") {
            $authkey_base = $user['username'] . $utils->generateRandomString(20) . time();
            $authkey = md5($authkey_base);
            $unos = update_authkey($dbcon, $authkey, $user['userno']);
            if ($unos > 0) {
                //SEND EMAIL

                $resetPassUrl = "https://$host/reset_password.php?";

                // $apikey='jewelTipSed';
                $urlToAppend = "username=" . $user['username'] . "&resetkey=" . $authkey;

                $resetPassUrl .= $urlToAppend;

                $payload = array();
                $payload['to'] = $email; // mandatory
                $payload['cc'] = ""; // optional, mandatory for keeping record    // Need to change this
                $payload['subject'] = "[Action Required] Password reset requested | AGAMi SmartAccounting"; // optional
                $payload['message'] = get_body($resetPassUrl); // optional
                $payload['from'] = "noreply@$host"; // mandatory   // Need to change this

                $mailRes = sendMail($payload);

                if ($mailRes) {
                    $response['error'] = false;
                    // $response['resetPassUrl'] = $resetPassUrl;
                    $response['message'] = "Password reset link has been sent to your email. Please check your email.";
                } else {
                    $response['error'] = true;
                    $response['message'] = "Password reset link can not be sent through email!";
                }
            } else {
                throw new Exception("Unable to send reset email now!", 1);
            }
        } else if ($RESET_METHOD == "OTP") {
            $otp_base = strtoupper($utils->generateRandomString(8));
            $otp_hash = password_hash($otp_base, PASSWORD_DEFAULT);
            $unos = update_authkey($dbcon, $otp_hash, $user['userno']);
            if ($unos > 0) {
                //SEND EMAIL

                $payload = array();
                $payload['to'] = $email; // mandatory
                $payload['cc'] = ""; // optional, mandatory for keeping record    // Need to change this
                $payload['subject'] = "[Important] Temporary password requested | AGAMi SmartAccounting"; // optional
                $payload['message'] = get_otp_body($host, $otp_base); // optional
                $payload['from'] = "noreply@$host"; // mandatory   // Need to change this

                $mailRes = sendMail($payload);

                if ($mailRes) {
                    $response['error'] = false;
                    // $response['OTP'] = $otp_base;
                    $response['message'] = "A temporary password has been sent to your email. Please check your email.";
                } else {
                    $response['error'] = true;
                    // $response['OTP'] = $otp_base;
                    // $response['payload'] = $payload;
                    $response['message'] = "Couldn't send temporary password through email!";
                }
            } else {
                throw new Exception("Unable to send email now!", 1);
            }
        } else {
            throw new Exception("Reset method is not available now!", 1);
        }
    } else {
        throw new Exception("Your email is not registered. Thank you!", 1);
    }
} catch (\Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}


echo json_encode($response);
$dbcon->close();

//hr_user (userno,username,firstname,lastname,email,countrycode,contactno,passphrase,authkey,userstatusno,ucreatedatetime,reset_pass_count,updatetime)
function get_user_info($dbcon, $email)
{
    $sql = "SELECT userno, username, firstname,lastname
            FROM hr_user
            WHERE userstatusno>=1 AND email=?
            LIMIT 1";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

function update_authkey($dbcon, $authkey, $userno)
{
    date_default_timezone_set("Asia/Dhaka");
    $updatetime = date("Y-m-d H:i:s");

    $sql = "UPDATE gen_users
            SET reset_pass_count=reset_pass_count+1,authkey=?,updatetime=?
            WHERE userstatusno>=1 AND userno=?";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ssi", $authkey, $updatetime, $userno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
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
    $from = "info@agamilabs.com"; // hosting server email acc   // Need to change this
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

    $subject = "INVITATION FROM AGAMi SmartAccounting WEBPORTAL FOR RESETTING PASSWORD";
    if (isset($payload['subject'])) {
        $subject = $payload['subject'];
    }

    $message = "MAIL FROM SmartAccounting WEBPORTAL";
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

    if (mail($to, $subject, $message, $headers)) {
        return true;
    } else {
        return false;
    }
}

function get_body($resetPassUrl)
{
    return '<table cellpadding="0" cellspacing="0" border="0" width="100%">
      <tr>
        <td>Hello,

          <br/>
          Your password reset link is given from
          <b>
            SmartAccounting Admin
          </b>
          . Please click the
          <b>
            \'RESET PASSWORD\'
          </b>
           button to navigate to the password reset page.
           <br/>
           Thank you.
        </td>
      </tr>
      <tr>
        <td align="center">
          <table cellpadding="0" cellspacing="0" width="200" border="0" style="border-collapse: separate !important;">
            <tr>
              <td style="background-color:#56CFD2;color:#FFFFFF;font-size:18px;padding:10px 10px;border-radius:3px;font-family:Arial, Helvetica, sans-serif;text-align:center;border:solid 1px #FFFFFF">
                <a target="_blank" href="' . $resetPassUrl . '" style="color:#FFFFFF;text-decoration:none;">RESET PASSWORD</a>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>';
}


function get_otp_body($host, $otp)
{
    return "<!doctype html>
    <html lang=\"en-US\">
    
    <head>
        <meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\" />
        <title>Reset Password Email Template</title>
        <meta name=\"description\" content=\"Reset Password Email Template.\">
        <style type=\"text/css\">
            a:hover {text-decoration: underline !important;}
        </style>
    </head>
    
    <body marginheight=\"0\" topmargin=\"0\" marginwidth=\"0\" style=\"margin: 0px; background-color: #f2f3f8;\" leftmargin=\"0\">
        <!--100% body table-->
        <table cellspacing=\"0\" border=\"0\" cellpadding=\"0\" width=\"100%\" bgcolor=\"#f2f3f8\"
            style=\"@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;\">
            <tr>
                <td>
                    <table style=\"background-color: #f2f3f8; max-width:670px;  margin:0 auto;\" width=\"100%\" border=\"0\"
                        align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
                        <tr>
                            <td style=\"height:80px;\">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style=\"text-align:center;\">
                              <a href=\"https://$host\" title=\"logo\" target=\"_blank\">
                                <img width=\"100\" src=\"https://$host/assets/image/logo.png\" title=\"logo\" alt=\"logo\">
                              </a>
                            </td>
                        </tr>
                        <tr>
                            <td style=\"height:20px;\">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\"
                                    style=\"max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);\">
                                    <tr>
                                        <td style=\"height:40px;\">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style=\"padding:0 35px;\">
                                            <h1 style=\"color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;\">
                                                You have requested to reset your password
                                            </h1>
                                            <span
                                                style=\"display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;\">
                                            </span>
                                            <p style=\"color:#455056; font-size:15px;line-height:24px; margin:0;\">
                                                A temporary password has been generated for you. 
                                                To login into your account, use the following temporary password. 
                                                Be sure to change it upon login.
                                            </p>
                                            <a href=\"javascript:void(0);\" style=\"font-family:monospace; background:purple;text-decoration:none !important; font-weight:500; margin-top:35px; color:#fff;text-transform:uppercase; font-size:18px;padding:10px 24px;display:inline-block;border-radius:50px;\">
                                                $otp
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style=\"height:40px;\">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        <tr>
                            <td style=\"height:20px;\">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style=\"text-align:center;\">
                                <p style=\"font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;\">&copy; <strong>www.$host</strong></p>
                            </td>
                        </tr>
                        <tr>
                            <td style=\"height:80px;\">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!--/100% body table-->
    </body>
    
    </html>";
}
