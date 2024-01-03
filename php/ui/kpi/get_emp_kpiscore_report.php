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

    if($ucatno == 19){
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
    }else{
        $empno=$userno;

        $rs_curr_desig=get_emp_current_designations($dbcon,$empno);
        if ($rs_curr_desig->num_rows > 0) {
            $row = $rs_curr_desig->fetch_array(MYSQLI_ASSOC);
            $desigid=$row['desigid'];
            $paylevelno=$row['paylevelno'];
        }
    }


    $rs_emp = get_emp_info($dbcon,$empno,$desigid,$paylevelno);
    if ($rs_emp->num_rows > 0) {
        $response['error'] = false;
        $response['info']=$rs_emp->fetch_array(MYSQLI_ASSOC);

        $rs_score = get_kpiscore($dbcon,$empno,$desigid,$paylevelno);
        $score_array = array();
        if ($rs_score->num_rows > 0) {
            while ($row = $rs_score->fetch_array(MYSQLI_ASSOC)) {
                $score_array[] = $row;
            }
        }
        $response['scores'] = $score_array;
    }else{
        throw new \Exception("No Data Found!", 1);
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

// emp_kpiscore(kpiscoreno,empno,paylevelno,desigid,kpino,score,comment,createtime,lastupdatetime,editcount,createdby)
// emp_kpisetting(kpino,kpititle,measureunit,indicator)
// emp_designation(userno,desigid,paylevelno,joiningdate,enddate,isresigned)
// emp_kpitarget(desigid,paylevelno,kpino,milestone,nscore)
function get_kpiscore($dbcon,$empno,$desigid,$paylevelno)
{
    $sql = "SELECT k.*,
                (SELECT desigtitle FROM hr_designationsetting WHERE desigid=k.desigid) as desigtitle,
                s.kpititle,s.measureunit,
                t.milestone,t.nscore
            FROM emp_kpiscore as k
                INNER JOIN emp_kpitarget as t ON t.desigid=k.desigid AND t.paylevelno=k.paylevelno AND t.kpino=k.kpino
                INNER JOIN emp_kpisetting as s ON s.kpino=k.kpino
            WHERE k.empno=?
                AND k.desigid=?
                AND k.paylevelno=?
            ORDER BY k.kpino";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("iii", $empno,$desigid,$paylevelno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

// emp_designation(userno,desigid,paylevelno,joiningdate,enddate)
// hr_user(userno,username,firstname,lastname,affiliation,jobtitle,email,primarycontact,passphrase,ucatno,supervisor,permissionlevel,createtime,lastupdatetime,isactive)
// hr_designationsetting(desigid,desigtitle,isactive)
function get_emp_info($dbcon,$empno,$desigid,$paylevelno)
{
    $sql = "SELECT ed.*,u.firstname,u.lastname,
                (SELECT desigtitle FROM hr_designationsetting WHERE desigid=ed.desigid) as desigtitle
            FROM emp_designation as ed
                INNER JOIN hr_user as u ON u.userno=ed.userno
            WHERE ed.userno=?
                AND ed.desigid=?
                AND ed.paylevelno=?
            LIMIT 1";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("iii", $empno,$desigid,$paylevelno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

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

