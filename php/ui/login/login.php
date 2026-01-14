<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

if (!isset($_POST['action']) || !isset($_POST['captchatoken'])) {
    $response['error'] = true;
    $response['message'] = "This site is protected. We could not validate your data!";
    echo json_encode($response);
    exit();
}

if (isset($_POST['username']) && strlen($_POST['username']) > 0) {
    $username = trim(strip_tags($_POST['username']));
} else {
    $response['error'] = true;
    $response['message'] = "User-name cannot be empty!";
    echo json_encode($response);
    exit();
}

if (isset($_POST['password']) && strlen($_POST['password']) > 0) {
    $password = trim(strip_tags($_POST['password']));
} else {
    $response['error'] = true;
    $response['message'] = "Invalid Credential!";
    echo json_encode($response);
    exit();
}


$captchatoken = trim(strip_tags($_POST['captchatoken']));
$secret = '6Le-0EQpAAAAAIsBTBogTi1ypk-XjeGPNPXPBplV';
$action = trim(strip_tags($_POST['action']));
// now you need do a POST requst to google recaptcha server.
// url: https://www.google.com/recaptcha/api/siteverify.
// with data secret:$secret and response:$token
$data = array(
    'secret' => $secret,
    'response' => $captchatoken
);

$verify = curl_init();
curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
curl_setopt($verify, CURLOPT_POST, true);
curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
$reCaptcharesponse = curl_exec($verify);

//echo $reCaptcharesponse;
$result = json_decode($reCaptcharesponse, true);

if (!$result) {
    $response['error'] = true;
    $response['message'] = "Please try again later.";
    echo json_encode($response);
    exit();
}

if ($result['score'] <= 0.09) {
    $response['error'] = true;
    $response['message'] = "Too many tries!";
    echo json_encode($response);
    exit();
}


if (strlen($username) <= 0) {
    $response['error'] = true;
    $response['message'] = "Invalid username!";
    echo json_encode($response);
    exit();
}

$base_path = dirname(dirname(dirname(__FILE__)));

require_once($base_path . "/db/Database.php");

$db = new Database();
$dbcon = $db->db_connect();

if (!$db->is_connected()) {
    $response['error'] = true;
    $response['message'] = "Database is not connected!";
    echo json_encode($response);
    exit();
}

try {
    $userLoginResult = get_user_info($dbcon, $username);

    if ($userLoginResult->num_rows == 1) {
        $row = $userLoginResult->fetch_array(MYSQLI_ASSOC);
        $userno = $row['userno'];
        $passphrase = $row['passphrase'];

        if (password_verify($password, $passphrase)) {
            session_start();
            session_regenerate_id(true);

            $_SESSION['wm_userno'] = $row['userno'];
            $_SESSION['wm_firstname'] = $row['firstname'];
            $_SESSION['wm_lastname'] = $row['lastname'];
            $_SESSION['wm_photo_url'] = $row['photo_url'];
            $_SESSION['wm_email'] = $row['email'];
            $_SESSION['wm_userstatusno'] = $row['userstatusno'];
            $response['error'] = false;
            $response['message'] = "Login successful!";

            //userstatus=1 means active user amd 9 means agamian to set package
            if ($row['userstatusno'] == 1) { 
                $countorg = 0;
                $rs_countorg = count_my_company($dbcon, $userno);
                
                if ($rs_countorg->num_rows == 1) {
                    $userorg = $rs_countorg->fetch_array(MYSQLI_ASSOC);
                    $orgno=$userorg['orgno'];

                    //check validity
                    $isvalid = check_org_validity($dbcon,$orgno);
                    //echo "$orgno,$isvalid";
                    if ($isvalid!=1) {
                        $response['redirecturl'] = "organizations.php";
                        $response['error'] = false;
                        $response['message'] = "Your orgnazation has no valid package yet! Buy and apply package to your organization.";
                        echo json_encode($response);
                        exit();
                    }

                    $_SESSION['wm_orgno'] = $userorg['orgno'];
                    $_SESSION['wm_org_picurl'] = $userorg['picurl'];
                    $_SESSION['wm_orgname'] = $userorg['orgname'];
                    $_SESSION['wm_orglocation'] = $userorg['street'] . ', ' . $userorg['city'] . ', ' . $userorg['country'];
                    $_SESSION['wm_timeflexibility'] = $userorg['timeflexibility'];
                    $_SESSION['wm_starttime'] = $userorg['starttime'];
                    $_SESSION['wm_endtime'] = $userorg['endtime'];
                    $_SESSION['wm_ucatno'] = $userorg['ucatno'];
                    $_SESSION['wm_ucattitle'] = $userorg['ucattitle'];
                    $_SESSION['wm_designation'] = $userorg['designation'];
                    $_SESSION['wm_permissionlevel'] = $userorg['permissionlevel'];
                    $_SESSION['wm_moduleno'] = $userorg['moduleno'];
                    $_SESSION['wm_moduletitle'] = $userorg['moduletitle'];
                    $response['redirecturl'] = "time_keeper.php";
                } else {
                    $response['redirecturl'] = "organizations.php";
                }
            } else if ($row['userstatusno'] == 9) {
                $response['redirecturl'] = "agami/dashboard.php";
            } else {
                throw new Exception("You don't have valid user status. Please contact AGAMiLabs Support Center.", 1);
            }
        } else {
            $response['error'] = true;
            $response['message'] = "Invalid login credential!";
        }
    } else {
        $response['error'] = true;
        $response['message'] = "User is not valid!";
    }
} catch (\Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}


echo json_encode($response);

try{
    require_once($base_path."/ui/push/ExternalAPI.php");
    $telegramBot = new TelegramBotAPI();
    $telegramBot->loginLog();
}catch(Exception $ex){

}

$dbcon->close();

/**
 * Local Function
 */

function get_user_info($dbcon, $username)
{
    $sql = "SELECT userno,username,firstname,lastname,photo_url,
                email,primarycontact,
                passphrase,createtime,lastupdatetime,isactive, userstatusno
            FROM hr_user as u
            WHERE isactive=1 AND username=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

//com_userorg (uono,orgno,userno,uuid,ucatno,supervisor,moduleno,designation,hourlyrate,monthlysalary,permissionlevel,dailyworkinghour,timeflexibility,starttime,endtime,isactive)
//com_orgs (orgno, orgname, street, city, state, country, gpslat, gpslon, orgtypeid, privacy, picurl, primarycontact, orgnote, weekend1, weekend2, starttime, endtime, verifiedno)
function count_my_company($dbcon, $userno)
{
    $sql = "SELECT uo.orgno,timeflexibility,uo.starttime,uo.endtime,
                    uo.ucatno, (SELECT ucattitle FROM hr_usercat WHERE ucatno=uo.ucatno) as ucattitle,
                    designation,permissionlevel,
                    o.orgname,o.street, o.city, o.country, o.picurl,
                    uo.ucatno, (SELECT ucattitle FROM hr_usercat WHERE ucatno=uo.ucatno) as ucattitle,
                    uo.moduleno,(SELECT moduletitle FROM com_modules WHERE moduleno=uo.moduleno) as moduletitle
            FROM com_userorg as uo
                INNER JOIN com_orgs as o ON uo.orgno=o.orgno
            WHERE uo.isactive=1 AND uo.userno=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $userno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

function check_org_validity($dbcon,$orgno){

    $sql = "SELECT appliedno,purchaseno,users,starttime,DATE(DATE_ADD(starttime, INTERVAL duration DAY)) as closingdate
            FROM pack_appliedpackage
            WHERE orgno=? AND (CURRENT_DATE() BETWEEN DATE(starttime) AND DATE(DATE_ADD(starttime, INTERVAL duration DAY)))";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $orgno);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $stmt->close();
            return 1;
        } else {
            $stmt->close();
            return 0;
        }
    } else {
        return -1;
    }
}

?>