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

    $dbcon->begin_transaction();
    $dnos=del_basic_commonaccounts($dbcon,$commontypeno);

    $result = delete_commontype($dbcon, $commontypeno);

    if ($result<=0) {
        $dbcon->rollback();
        throw new Exception("Could not remove!", 1);
    }
    if($dbcon->commit()){
        $response['error'] = false;
        $response['message'] = "Removed Successfully";
    }else{
        $dbcon->rollback();
        throw new Exception("Could not remove!!", 1);
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//ext_commontype (commontypeno, commontypetitle, maxacclevel)
function delete_commontype($dbcon, $commontypeno)
{
    $sql = "DELETE
            FROM ext_commontype
            WHERE commontypeno=?";

    $stmt=$dbcon->prepare($sql);
    $stmt->bind_param("i",$commontypeno);
    $stmt->execute();
    $res = $stmt->affected_rows;
    $stmt->close();

    if($res>0)
    {
      return true;
    }
    else {
      return false;
    }
}

//ext_commonaccount(commontypeno, accno, accname, acctypeno, levelno, vtype, praccno)
function del_basic_commonaccounts($dbcon,$commontypeno){
    $sql="DELETE
            FROM ext_commonaccount
            WHERE commontypeno=? AND praccno IS NULL";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i",$commontypeno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}


?>
