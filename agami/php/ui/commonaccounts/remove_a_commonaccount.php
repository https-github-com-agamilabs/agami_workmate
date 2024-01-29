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
        throw new Exception("You must select an account!", 1);
    }

    $result = delete_commonaccount($dbcon, $commontypeno,$accno);

    if ($result) {
        $response['error'] = false;
        $response['message'] = 'Removed successfully!';
    } else {
        $response['error'] = true;
        $response['message'] = 'Data Error! Check the data.';
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//ext_commonaccount(commontypeno, accno, accname, acctypeno, levelno, vtype, praccno)
function delete_commonaccount($dbcon, $commontypeno,$accno)
{
    $sql = "DELETE
            FROM ext_commonaccount
            WHERE accno=? AND commontypeno=?";

    $stmt=$dbcon->prepare($sql);
    $stmt->bind_param("ii",$accno,$commontypeno);
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

?>
