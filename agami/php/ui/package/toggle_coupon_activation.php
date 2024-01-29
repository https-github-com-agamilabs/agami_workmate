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

    //$coupon,$discount_fixed,$discount_percentage,$description,$max_use,$createdby
    if (isset($_POST['coupon']) && strlen($_POST['coupon']) > 0) {
        $coupon = trim(strip_tags($_POST['coupon']));
    }else{
        throw new \Exception("Coupon-id cannot be empty!", 1);
    }

    $anos=toggle_coupon_activation($dbcon, $coupon);
    if($anos>0){
        $response['error'] = false;
        $response['message'] = "Updated Successfully.";
    }else{
        throw new \Exception("Could not update!", 1);
    }

} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//pack_coupon(coupon,discount_fixed,discount_percentage,description,max_use,isactive,createdat,createdby)
function toggle_coupon_activation($dbcon, $coupon)
{

    $sql = "UPDATE pack_coupon
            SET isactive=abs(1-isactive)
            WHERE coupon=?";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: ".$dbcon->error);
    }

    $stmt->bind_param("s", $coupon);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}

?>
