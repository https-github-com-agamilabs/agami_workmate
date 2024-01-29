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

    if (isset($_POST['offerno']) && strlen($_POST['offerno']) > 0) {
        $offerno = (int)$_POST['offerno'];
    }else{
        throw new \Exception("You must select an offer!", 1);
    }

    $dbcon->begin_transaction();
    $dnos_item=del_offeritems($dbcon, $offerno);
    $dnos_offer=del_offer($dbcon, $offerno);
    if($dnos>0){
        if($dbcon->commit()){
            $response['error'] = false;
            $response['message'] = "Removed Successfully.";
        }else{
            $dbcon->rollback();
            throw new \Exception("Could not remove!", 1);
        }
    }else{
        $dbcon->rollback();
        throw new \Exception("Could not remove!", 1);
    }

} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();


//pack_offer(offerno, offertitle, rate, validuntil)
function del_offer($dbcon, $offerno)
{

    $sql = "DELETE
            FROM pack_offer
            WHERE offerno=?";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: ".$dbcon->error);
    }

    $stmt->bind_param("i", $offerno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}

//pack_offeritems(offerno,item,qty)
function del_offeritems($dbcon, $offerno)
{

    $sql = "DELETE
            FROM pack_offeritems
            WHERE offerno=?";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: ".$dbcon->error);
    }

    $stmt->bind_param("i", $offerno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}

?>
