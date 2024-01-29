<?php
$base_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
include_once($base_path . "/php/ui/login/check_session.php");

$response = array();
if ($_SERVER['REQUEST_METHOD']!='POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

require_once $base_path . "/php/ui/dependency_checker.php";

try{
    if (isset($_POST['schemeno']) && strlen($_POST['schemeno'])>0){
        $schemeno = (int)$_POST['schemeno'];
    }else{
        throw new \Exception("You must select a payment-package!", 1);
    }

    $dnos = delete_entry($dbcon,$schemeno);
    if($dnos>0)
    {
        $response['error'] = false;
        $response['message'] = "Successfully deleted.";
    }else {
        throw new \Exception("Could not delete! Please try again.",1);
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

$dbcon->close();

//gen_rolepayscheme(schemeno, schemetitle, userroleno, minpay, rate, perunit, duration, isprepaid)
function delete_entry($dbcon,$schemeno)
{
    $sql = "DELETE
            FROM gen_rolepayscheme
            WHERE schemeno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $schemeno);
        $stmt->execute();
        $result = $stmt->affected_rows;
        $stmt->close();

        return $result;
}
 ?>
