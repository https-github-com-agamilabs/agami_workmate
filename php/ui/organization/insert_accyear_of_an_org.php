<?php
$base_path = dirname(dirname(dirname(__FILE__)));
include_once($base_path . "/ui/login/check_session.php");

$response = array();
$response['error'] = false;
$response['message'] = '';
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = 'Invalid request method!';
    echo json_encode($response);
    exit();
}

require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try {
    if (isset($_POST['purchaseno']) && strlen($_POST['purchaseno']) > 0) {
        $purchaseno = (int) $_POST['purchaseno'];
    }else{
        throw new Exception("You must select a package!", 1);
    }

    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int) $_POST['orgno'];
    }else{
        if (isset($_SESSION['orgno']) && strlen($_SESSION['orgno']) > 0) {
            $orgno = (int) $_SESSION['orgno'];
        }else{
            throw new Exception("Organization must be selected!", 1);
        }
    }
 

    $isvalid = check_org_validity($dbcon,$orgno);

    if ($isvalid != 1) {
        throw new Exception("Your organization has no valid package. Please buy one to start.\n ", 1);
    }

    $dbcon->begin_transaction();
    $insert_accyear = insert_accyear_of_an_org($dbcon,$orgno, $accyear, $startdate, $closingdate);

    if ($insert_accyear == false) {
        $dbcon->rollback();
        $response['error'] = true;
        $response['message'] = 'Accounting year failed to start. You cannot use an existing accounting year. Please try again.';
    } else {
        $appliedno=insert_appliedpackage($dbcon,$purchaseno, $orgno, $accyear, $addedby);
        if($appliedno>0){
            $response['error'] = false;
            $response['message'] = 'Accounting year is set successfully.';
            $response['accyear'] = $accyear;
        }else{
            $dbcon->rollback();
            throw new Exception('Accounting year failed to start. Check your package and try again.',1);
        }
    }
    $dbcon->commit();
} catch (\Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}
echo json_encode($response);
$dbcon->close();

//pack_appliedpackage(appliedno,purchaseno,orgno,starttime,assignedto, duration,appliedat, appliedby)
function insert_appliedpackage($dbcon,$purchaseno, $orgno, $accyear, $appliedby){
    date_default_timezone_set("Asia/Dhaka");
    $appliedat = date("Y-m-d H:i:s");

    $sql = "INSERT INTO pack_appliedpackage(purchaseno,item,orgno,assignedto, appliedat, appliedby)
            VALUES(?,'ACCYEAR',?,?,?,?)";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("iissi", $purchaseno, $orgno, $accyear, $appliedat,$appliedby);
    $stmt->execute();
    $result=$stmt->affected_rows;
    $stmt->close();
    return $result;
}

//acc_accountingyear (orgno,accyear,startdate,closingdate,accyearstatus)
function insert_accyear_of_an_org($dbcon,$orgno, $accyear, $startdate, $closingdate)
{

    $sql = "INSERT INTO acc_accountingyear(orgno, accyear, startdate, closingdate)
            VALUES(?,?,?,?)";

    $stmt = $dbcon->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('isss', $orgno,$accyear, $startdate, $closingdate);
        if ($stmt->execute()) {
            $flag = $stmt->affected_rows;
            if ($flag > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}


//pack_appliedpackage(appliedno,purchaseno,orgno,starttime,assignedto, duration,appliedat, appliedby)
function check_org_validity($dbcon,$orgno){

    $sql = "SELECT appliedno
            FROM pack_appliedpackage
            WHERE orgno=?
                AND (CURRENT_DATE() BETWEEN DATE(starttime) AND DATE(DATE_ADD(starttime, INTERVAL duration DAY)))";

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
