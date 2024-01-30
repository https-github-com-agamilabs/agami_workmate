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

$userLoginResult = get_user_info($dbcon, $username);

if ($userLoginResult->num_rows == 1) {
    $row = $userLoginResult->fetch_array(MYSQLI_ASSOC);
    $userno = $row['userno'];
    $passphrase = $row['passphrase'];

    if (password_verify($password, $passphrase)) {
        session_start();

        $_SESSION['cogo_userno'] = $row['userno'];
        $_SESSION['cogo_firstname'] = $row['firstname'];
        $_SESSION['cogo_lastname'] = $row['lastname'];
        $_SESSION['cogo_photo_url'] = $row['photo_url'];
        $_SESSION['cogo_ucatno'] = $row['ucatno'];
        $_SESSION['cogo_ucattitle'] = $row['ucattitle'];
        $_SESSION['cogo_email'] = $row['email'];
        $_SESSION['cogo_designation'] = $row['jobtitle'];
        $_SESSION['cogo_permissionlevel'] = $row['permissionlevel'];

        $response['error'] = false;
        $response['ucatno'] = $row['ucatno'];
        $response['message'] = "Login successful!";

        if ($row['ucatno'] == 99) {
            $response['redirect'] = "/agami/dashboard.php";
        } else {
            $countorg=0;
            $rs_countorg = count_my_company($dbcon, $userno);
            if ($rs_countorg->num_rows > 0) {
                $countorg=$rs_countorg->num_rows;
                if ($countorg == 1) {
                    $userorg = $rs_countorg->fetch_array(MYSQLI_ASSOC)['countorg'];
                    $_SESSION['orgno'] = $userorg['orgno'];
                    $_SESSION['org_picurl'] = $userorg['picurl'];
                    $_SESSION['orgname'] = $userorg['orgname'];
                    $_SESSION['orglocation'] = $userorg['street'] . ', ' . $userorg['city'] . ', ' . $userorg['country'];
                    $response['redirect'] = "time_keeper.php";
                } else {
                    $response['redirect'] = "dashboard.php";
                }
            } else {
                $response['redirect'] = "dashboard.php";
            }
        }
    } else {
        $response['error'] = true;
        $response['message'] = "Invalid login credential!";
    }
} else {
    $response['error'] = true;
    $response['message'] = "User is not valid!";
}

echo json_encode($response);

$dbcon->close();

/**
 * Local Function
 */

function get_user_info($dbcon, $username)
{
    $sql = "SELECT userno,username,firstname,lastname,photo_url,
                affiliation,jobtitle,email,primarycontact,
                passphrase,createtime,lastupdatetime,
                ucatno, (SELECT ucattitle FROM hr_usercat WHERE ucatno=u.ucatno) as ucattitle,
                permissionlevel,isactive
            FROM hr_user as u
            WHERE isactive=1 AND username=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

//com_userorgmodules (uuid,orgno, userno, moduleno, isactive)
function count_my_company($dbcon, $userno)
{
    $sql = "SELECT uo.orgno, o.orgname,o.street, o.city, o.country, o.picurl,
                uo.moduleno,(SELECT moduletitle FROM com_modules WHERE moduleno=uo.moduleno) as moduletitle
            FROM com_userorgmodules as uo
                INNER JOIN com_orgs as o ON uo.orgno=o.orgno
            WHERE uo.isactive=1 AND uo.userno=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $userno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
?>