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

    if (isset($_SESSION['wm_userno'])) {
        $userno = (int) $_SESSION['wm_userno'];
    } else {
        throw new \Exception("You must login first!", 1);
    }

    $ucatno = 0;
    if (isset($_SESSION['wm_ucatno'])) {
        $ucatno = (int) $_SESSION['wm_ucatno'];
    }

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

    $result = del_an_emp_info($dbcon,$empno,$desigid,$paylevelno);
    if ($result > 0) {
        $response['error'] = false;
        $response['message'] = "Deleted Successfully.";
    }else{
        $response['error'] = true;
        $response['message'] = "Could not deleted!";
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
function del_an_emp_info($dbcon,$empno,$desigid,$paylevelno)
{
    $sql = "DELETE
            FROM emp_designation
            WHERE userno=?
                AND desigid=?
                AND paylevelno=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("iii", $empno,$desigid,$paylevelno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}
