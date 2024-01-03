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

    $result = get_kpiscore($dbcon,$empno,$desigid,$paylevelno);
    $data_array = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $data_array[] = $row;
        }
    }
    $response['error'] = false;
    $response['data'] = $data_array;
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();


/*
    *   LOCAL FUNCTIONS
    */

// emp_kpiscore(kpiscoreno,empno,paylevelno,desigid,kpino,score,comment,createtime,lastupdatetime,editcount,createdby)
// hr_user(userno,username,firstname,lastname,affiliation,jobtitle,email,primarycontact,passphrase,ucatno,supervisor,permissionlevel,createtime,lastupdatetime,isactive)
// hr_designationsetting(desigid,desigtitle,isactive)
// emp_kpisetting(kpino,kpititle,measureunit,indicator)
//emp_designation(userno,desigid,paylevelno,joiningdate,enddate,isresigned)
function get_kpiscore($dbcon,$empno,$desigid,$paylevelno)
{
    $sql = "SELECT k.*,
                u.firstname,u.lastname,
                (SELECT desigtitle FROM hr_designationsetting WHERE desigid=k.desigid) as desigtitle,
                (SELECT kpititle FROM emp_kpisetting WHERE kpino=k.kpino) as kpititle
            FROM emp_kpiscore as k
                INNER JOIN hr_user as u ON u.userno=k.empno
            WHERE k.empno=?
                AND k.desigid=?
                AND k.paylevelno=?
            ORDER BY k.desigid DESC,k.paylevelno DESC,k.empno";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("iii", $empno,$desigid,$paylevelno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
