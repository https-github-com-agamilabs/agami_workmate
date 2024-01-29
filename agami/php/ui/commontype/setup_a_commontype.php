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

    $commontypeno = -1;
    if (isset($_POST['commontypeno']) && strlen($_POST['commontypeno'])>0){
        $commontypeno = (int) $_POST['commontypeno'];
    }

    if (isset($_POST['commontypetitle']) && strlen($_POST['commontypetitle'])>0){
        $commontypetitle = trim(strip_tags($_POST['commontypetitle']));
    }else{
        throw new Exception("Account-type cannot be empty!", 1);
    }

    $maxacclevel=4;
    if (isset($_POST['maxacclevel']) && strlen($_POST['maxacclevel']) > 0) {
        $maxacclevel = (int) $_POST['maxacclevel'];
    }

    $dbcon->begin_transaction();
    $result = setup_commontype($dbcon, $commontypetitle, $maxacclevel, $commontypeno);

    if ($result > 0 && $commontypeno < 0) {
        $inos=insert_basic_commonaccounts($dbcon,$result);
        if($inos>0){
            $response['error'] = false;
            $response['message'] = 'Insertion successful!';
        }else{
            $dbcon->rollback();
            throw new Exception("Could not initiate! Check the data and try again.", 1);
        }
    } else if ($result > 0 && $commontypeno > 0) {
        $response['error'] = false;
        $response['message'] = 'Update successful!';
    } else if ($result <= 0) {
        $dbcon->rollback();
        $response['error'] = true;
        $response['message'] = 'Could not save! Check the data and try again.';
    }

    $dbcon->commit();
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//ext_commontype (commontypeno, commontypetitle, maxacclevel)
function setup_commontype($dbcon, $commontypetitle, $maxacclevel, $commontypeno)
{

    if ($commontypeno <= 0) {
        $sql = "INSERT INTO ext_commontype(commontypetitle, maxacclevel)
                VALUES(?,?)";

        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param('si', $commontypetitle, $maxacclevel);
        $stmt->execute();
        $result = $stmt->insert_id;
    } else {
        $sql = "UPDATE ext_commontype
                SET commontypetitle=?, maxacclevel=?
                WHERE commontypeno=?";

        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param('sii', $commontypetitle, $maxacclevel, $commontypeno);
        $stmt->execute();
        $result = $stmt->affected_rows;
    }

    $stmt->close();
    return $result;
}

//ext_commonaccount(commontypeno, accno, accname, acctypeno, levelno, vtype, praccno)
function insert_basic_commonaccounts($dbcon,$commontypeno){
    $sql="INSERT INTO ext_commonaccount(commontypeno, accno, accname, acctypeno, levelno, vtype, sysacc) VALUES
            ($commontypeno, 10000, 'ASSETS', 1000,1,0,1),
            ($commontypeno, 20000, 'LIABILITIES', 2000,1,0,1),
            ($commontypeno, 30000, 'EXPENSES', 3000,1,0,1),
            ($commontypeno, 40000, 'REVENUES', 4000,1,0,1);";

    $stmt = $dbcon->prepare($sql);
    // var_dump($this->dbcon->error);

    $stmt = $dbcon->prepare($sql);
    //$stmt->bind_param('iiii', $commontypeno, $commontypeno, $commontypeno,$commontypeno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}
?>
