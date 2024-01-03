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

    if (isset($_POST['empno'])) {
        $empno = (int) $_POST['empno'];
    } else {
        throw new \Exception("You must select an employee!", 1);
    }

    $result = get_emp_current_designations($dbcon,$empno);

    if ($result->num_rows > 0) {
        $response['data'] = $result->fetch_array(MYSQLI_ASSOC);
    }else{
        throw new \Exception("No current designation!", 1);
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

// emp_designation(userno,desigid,paylevelno,joiningdate,enddate)
// hr_user(userno,username,firstname,lastname,affiliation,jobtitle,email,primarycontact,passphrase,ucatno,supervisor,permissionlevel,createtime,lastupdatetime,isactive)
// hr_designationsetting(desigid,desigtitle,isactive)
function get_emp_current_designations($dbcon,$empno)
{
    $sql = "SELECT userno,
                desigid, (SELECT desigtitle FROM hr_designationsetting WHERE desigid=d.desigid) as desigtitle,
                paylevelno
            FROM emp_designation as d
            WHERE enddate IS NULL
                AND userno = ?
            ORDER BY joiningdate DESC
            LIMIT 1";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $empno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
