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
    if (isset($_POST['kpiscoreno'])) {
        $kpiscoreno = (int) $_POST['kpiscoreno'];
    } else {
        throw new \Exception("You must select a KPI-Score!", 1);
    }

    $result = del_an_emp_kpiscore($dbcon,$kpiscoreno);
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

//emp_kpiscore(kpiscoreno,empno,paylevelno,desigid,kpino,score,comment,createtime,lastupdatetime,editcount,createdby)
function del_an_emp_kpiscore($dbcon,$kpiscoreno)
{
    $sql = "DELETE
            FROM emp_kpiscore
            WHERE kpiscoreno=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $kpiscoreno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}
