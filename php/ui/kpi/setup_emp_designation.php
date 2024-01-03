<?php
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

    if (isset($_SESSION['cogo_userno'])) {
        $userno = (int) $_SESSION['cogo_userno'];
    } else {
        throw new \Exception("You must login first!", 1);
    }

    $ucatno = 0;
    if (isset($_SESSION['cogo_ucatno'])) {
        $ucatno = (int) $_SESSION['cogo_ucatno'];
    }

    //userno,desigid,paylevelno
    if (isset($_POST['empno'])) {
        $empno = (int) $_POST['empno'];
    } else {
        throw new \Exception("You must select an employee!", 1);
    }

    if (isset($_POST['desigid'])) {
        $desigid = (int) $_POST['desigid'];
    } else {
        throw new \Exception("You must select a designation!", 1);
    }

    if (isset($_POST['paylevelno'])) {
        $paylevelno = (int) $_POST['paylevelno'];
    } else {
        throw new \Exception("Pay-level cannot be empty!", 1);
    }

    //joiningdate,enddate,isresigned
    if (isset($_POST['joiningdate'])) {
        $joiningdate = trim(strip_tags($_POST['joiningdate']));
    } else {
        throw new \Exception("Joining-date cannot be empty!", 1);
    }

    $enddate=NULL;
    if (isset($_POST['enddate']) && strlen($_POST['enddate'])>0) {
        $enddate = trim(strip_tags($_POST['enddate']));
    }

    $dbcon->begin_transaction();
    if(is_other_current($dbcon,$empno,$desigid,$paylevelno)){
        end_other_current($dbcon,$empno,$desigid,$paylevelno);
    }

    $result = setup_emp_designations($dbcon,$empno,$desigid,$paylevelno,$joiningdate,$enddate);
    if ($result <= 0) {
        throw new \Exception("Could not save!", 1);
    }

    if($dbcon->commit()){
        $response['error'] = false;
        $response['message'] = "Saved Successfully";
    }else{
        $dbcon->rollback();
        throw new \Exception("Could not save!", 1);
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();


/*
    *   LOCAL FUNCTIONS
    */

// emp_designation(userno,desigid,paylevelno,joiningdate,enddate,isresigned)
// hr_user(userno,username,firstname,lastname,affiliation,jobtitle,email,primarycontact,passphrase,ucatno,supervisor,permissionlevel,createtime,lastupdatetime,isactive)
// hr_designationsetting(desigid,desigtitle,isactive)

function is_other_current($dbcon,$empno,$desigid,$paylevelno)
{
    $sql = "SELECT joiningdate,enddate
            FROM emp_designation
            WHERE enddate IS NULL
                AND userno=?
                AND desigid != ?
                AND paylevelno != ?
            ";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("iii", $empno,$desigid,$paylevelno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result->num_rows > 0;
}

function setup_emp_designations($dbcon,$empno,$desigid,$paylevelno,$joiningdate,$enddate)
{
    $sql = "INSERT INTO emp_designation(userno,desigid,paylevelno,joiningdate,enddate)
            VALUES(?,?,?,?,?)
            ON DUPLICATE KEY UPDATE enddate=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("iiisss", $empno,$desigid,$paylevelno,$joiningdate,$enddate,$enddate);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}

function end_other_current($dbcon,$empno,$desigid,$paylevelno)
{
    date_default_timezone_set("Asia/Dhaka");
    $enddate = date("Y-m-d");

    $sql = "UPDATE emp_designation
            SET enddate=?
            WHERE enddate IS NULL
                AND userno=?
                AND desigid != ?
                AND paylevelno != ?
            ";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("siii", $enddate,$empno,$desigid,$paylevelno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}