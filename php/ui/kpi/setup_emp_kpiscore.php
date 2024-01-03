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

    //kpiscoreno
    $kpiscoreno=-1;
    if (isset($_POST['kpiscoreno'])) {
        $kpiscoreno = (int) $_POST['kpiscoreno'];
    }

    if (isset($_POST['kpino'])) {
        $kpino = (int) $_POST['kpino'];
    } else {
        throw new \Exception("You must select a KPI!", 1);
    }

    //joiningdate,enddate,isresigned
    if (isset($_POST['score'])) {
        $score = (double) $_POST['score'];
    } else {
        throw new \Exception("Score cannot be empty!", 1);
    }

    $comment=NULL;
    if (isset($_POST['comment']) && strlen($_POST['comment'])>0) {
        $comment = trim(strip_tags($_POST['comment']));
    }

    if($kpiscoreno>0){
        $result = update_emp_kpiscore($dbcon,$kpino,$score,$comment,$userno,$kpiscoreno);
        if ($result > 0) {
            $response['error'] = false;
            $response['message'] = "Added Successfully";
        }else{
            throw new \Exception("Could not add!", 1);
        }
    }else{
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

        $result = add_emp_kpiscore($dbcon,$empno,$desigid,$paylevelno,$kpino,$score,$comment,$userno);
        if ($result > 0) {
            $response['error'] = false;
            $response['message'] = "Added Successfully";
        }else{
            throw new \Exception("Could not add!", 1);
        }
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
// hr_kpiscoreetting(desigid,desigtitle,isactive)

//emp_kpiscore(kpiscoreno,empno,paylevelno,desigid,kpino,score,comment,createtime,lastupdatetime,editcount,createdby)
function add_emp_kpiscore($dbcon,$empno,$desigid,$paylevelno,$kpino,$score,$comment,$createdby)
{
    date_default_timezone_set("Asia/Dhaka");
    $createtime = date("Y-m-d H:i:s");

    $sql = "INSERT INTO emp_kpiscore(empno,desigid,paylevelno,kpino,score,comment,createtime,lastupdatetime,createdby)
            VALUES(?,?,?,?,?,?,?,?,?)";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("iiiiisssi", $empno,$desigid,$paylevelno,$kpino,$score,$comment,$createtime,$createtime,$createdby);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}

function update_emp_kpiscore($dbcon,$kpino,$score,$comment,$createdby,$kpiscoreno)
{
    date_default_timezone_set("Asia/Dhaka");
    $lastupdatetime = date("Y-m-d H:i:s");

    $sql = "UPDATE emp_kpiscore
            SET kpino=?,score=?,comment=?,lastupdatetime=?,createdby=?
            WHERE kpiscoreno=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("iissii", $kpino,$score,$comment,$lastupdatetime,$createdby,$kpiscoreno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}
