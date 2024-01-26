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

    if (isset($_POST['accyear']) && strlen($_POST['accyear']) > 0) {
        $accyear = trim(strip_tags($_POST['accyear']));
    }else{
        throw new Exception("Accounting-year cannot be empty!", 1);
    }

    if (isset($_POST['startdate']) && strlen($_POST['startdate']) > 0) {
        $startdate = trim(strip_tags($_POST['startdate']));
    }else{
        throw new Exception("Start-date of accounting-year cannot be empty!", 1);
    }

    if (isset($_POST['closingdate']) && strlen($_POST['closingdate']) > 0) {
        $closingdate = trim(strip_tags($_POST['closingdate']));
    }else{
        throw new Exception("Closing-date of accounting-year cannot be empty", 1);
    }

    $base_dir = dirname(dirname(dirname(__FILE__)));
    include_once($base_dir . "/utility/Validator.php");
    include_once($base_dir . "/utility/Utils.php");

    $current_accyear = check_existance_of_running_accountingyear($dbcon,$orgno,$startdate, $closingdate);

    if ($current_accyear == 1) {
        throw new Exception("Your organization has an overlapping accounting year already running.\n ", 1);
    }elseif ($current_accyear < 0) {
        throw new Exception("Fatal error! Please try later.", 1);
    }

    $validator = new Validator($dbcon);

    if ($validator->validateDate($startdate) == false) {
        throw new Exception("Starting date is not valid.", 1);
    }
    if ($validator->validateDate($closingdate) == false) {
        throw new Exception("Ending date is not valid.", 1);
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

//pack_appliedpackage(appliedno,purchaseno,item,orgno,assignedto, appliedat, appliedby)
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


//acc_accountingyear (orgno,accyear,startdate,closingdate,accyearstatus)
function check_existance_of_running_accountingyear($dbcon,$orgno,$startdate, $closingdate)
    {

        $sql = "SELECT accyear
                FROM acc_accountingyear
                WHERE orgno=?
                    AND (startdate BETWEEN ? AND ?)
                    AND (closingdate BETWEEN ? AND ?)
                    AND accyearstatus = 1";

        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("issss", $orgno,$startdate, $closingdate,$startdate, $closingdate);

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
