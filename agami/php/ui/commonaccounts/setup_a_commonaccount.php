<?php
include_once  dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/php/ui/login/check_session.php";

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

$base_path = dirname(dirname(dirname(__FILE__)));
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/php/ui/dependency_checker.php";

try {

    if (isset($_POST['commontypeno']) && strlen($_POST['commontypeno'])>0){
        $commontypeno = (int) $_POST['commontypeno'];
    }else{
        throw new Exception("Account-type cannot be empty!", 1);
    }

    if (isset($_POST['accno']) && strlen($_POST['accno'])>0){
        $accno = (int) $_POST['accno'];
    }else{
        throw new Exception("Account No cannot be empty!", 1);
    }

    if (isset($_POST['accname']) && strlen($_POST['accname'])>0){
        $accname = trim(strip_tags($_POST['accname']));
    }else{
        throw new Exception("Account Name cannot be empty!", 1);
    }

    $acchints=NULL;
    if (isset($_POST['acchints']) && strlen($_POST['acchints'])>0){
        $acchints = trim(strip_tags($_POST['acchints']));
    }

    $praccno = NULL;
    if (isset($_POST['praccno']) && strlen($_POST['praccno'])>0){
        $praccno = (int) $_POST['praccno'];
    }

    $vtype=0;
    if (isset($_POST['vtype']) && strlen($_POST['vtype']) > 0) {
        $vtype = trim(strip_tags($_POST['vtype']));
    }

    $sysacc=0;
    if (isset($_POST['sysacc']) && strlen($_POST['sysacc']) > 0) {
        $sysacc = (int) $_POST['sysacc'];
    }

    if($praccno && substr($accno,0,1) != substr($praccno,0,1)){
        throw new Exception("Account No and parent should be of same type!", 1);
    }

    $flag = -1;
    if (isset($_POST['flag']) && strlen($_POST['flag'])>0){
        $flag = (int) $_POST['flag'];
    }

    if(!isset($praccno)){
        $levelno = 1;
        $acctypeno = substr($accno,0,1)*1000;
    }else{
        $levelno=get_commonaccount_level($dbcon,$commontypeno, $praccno)+1;
        $acctypeno = substr($praccno,0,1)*1000;
    }

    $result = setup_commonaccount($dbcon, $commontypeno, $accno, $accname, $acchints, $levelno,$praccno, $acctypeno,$vtype,$sysacc,$flag);

    if ($result > 0 && $flag < 0) {
        $response['error'] = false;
        $response['message'] = 'Insertion successful!';
    } else if ($result > 0 && $flag > 0) {
        $response['error'] = false;
        $response['message'] = 'Update successful!';
    } else if ($result <= 0) {
        $response['error'] = true;
        $response['message'] = 'Data Error! Check the data.';
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//ext_commonaccount(commontypeno, accno, accname, acchints, levelno, praccno, acctypeno, isactive, sysacc, vtype)
function setup_commonaccount($dbcon, $commontypeno, $accno, $accname, $acchints, $levelno, $praccno, $acctypeno, $vtype=0, $sysacc=0,$oldaccno)
{

    if ($oldaccno < 0) {
        $sql = "INSERT INTO ext_commonaccount(commontypeno,accno, accname, acchints, levelno, praccno,acctypeno,vtype,sysacc)
                VALUES(?,?,?,?,?,?,?,?,?)";

        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param('iissiiiii', $commontypeno, $accno, $accname, $acchints, $levelno,$praccno,$acctypeno,$vtype,$sysacc);
    } else if ($oldaccno > 0) {
        $sql = "UPDATE ext_commonaccount
                SET accno=?,accname=?, acchints=?, levelno=?, praccno=?, acctypeno=?,vtype=?, sysacc=?
                WHERE accno=? AND commontypeno=?";

        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param('issiiiiiii', $accno, $accname, $acchints, $levelno, $praccno, $acctypeno, $vtype, $sysacc, $oldaccno, $commontypeno);
    }
    // var_dump($this->dbcon->error);

    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();
    return $result;
}

function get_commonaccount_level($dbcon,$commontypeno, $accno)
{
    $sql = "SELECT levelno
            FROM ext_commonaccount as os
            WHERE commontypeno=? AND accno=?
            ";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ii", $commontypeno,$accno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $levelno=0;
    if($result->num_rows>0){
        $levelno=$result->fetch_array(MYSQLI_ASSOC)['levelno'];
    }
    return $levelno;
}
?>
