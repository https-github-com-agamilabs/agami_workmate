<?php
include_once  dirname(dirname(__FILE__)) . "/login/check_session.php";

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

$base_path = dirname(dirname(dirname(__FILE__)));
require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try {

    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int) $_POST['orgno'];
    }else{
        throw new Exception("You must select an organization!", 1);
    }

    if (isset($_POST['commontypeno']) && strlen($_POST['commontypeno']) > 0) {
        $commontypeno = (int) $_POST['commontypeno'];
    }else{
        throw new Exception("You must select a type!", 1);
    }

    $dbcon->begin_transaction();
    $dnos=del_orgaccounthead($dbcon,$orgno);
    $result = init_orgaccounthead($dbcon,$orgno,$commontypeno);
    if ($result > 0) {
        update_toplevel_orgaccounthead($dbcon,$orgno);
        if($dbcon->commit()){
            $response['error'] = false;
            $response['message'] = $result." Account-heads are saved successfully.";
        }else{
            $dbcon->rollback();
            $response['error'] = true;
            $response['message'] = "No Account Saved!";
        }
    } else {
        $dbcon->rollback();
        $response['error'] = true;
        $response['message'] = "No Account Saved!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//acc_orgaccounthead (orgno,accno, accname, levelno,praccno, acctypeno,isactive,sysacc)
//ext_commonaccount(commontypeno, accno, accname, acctypeno, levelno, vtype, praccno)
function init_orgaccounthead($dbcon,$orgno,$commontypeno)
{
    $sql = "INSERT INTO acc_orgaccounthead (orgno,accno, accname, levelno, praccno, acctypeno,vtype,isactive,sysacc)
            SELECT ? as orgno,accno, accname, levelno, praccno, acctypeno, vtype, 1 as isactive, 0 as sysacc
            FROM ext_commonaccount
            WHERE commontypeno=?
            ORDER BY levelno ASC
            ";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("ii", $orgno,$commontypeno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();
    return $result;
}

function del_orgaccounthead($dbcon,$orgno)
{
    $sql = "DELETE
            FROM acc_orgaccounthead
            WHERE orgno=?
            ";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("i", $orgno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();
    return $result;
}

function update_toplevel_orgaccounthead($dbcon,$orgno)
{
    $sql = "UPDATE acc_orgaccounthead
            SET sysacc=1
            WHERE orgno=? AND levelno=1
            ";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("i", $orgno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();
    return $result;
}

?>
