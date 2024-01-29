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

    $discount_fixed=0.0;
    if (isset($_POST['discount_fixed']) && strlen($_POST['discount_fixed']) > 0) {
        $discount_fixed = (double)$_POST['discount_fixed'];
    }

    $discount_percentage=0.0;
    if (isset($_POST['discount_percentage']) && strlen($_POST['discount_percentage']) > 0) {
        $discount_percentage = (double)$_POST['discount_percentage'];
    }

    $description=NULL;
    if (isset($_POST['description']) && strlen($_POST['description']) > 0) {
        $description = trim(strip_tags($_POST['description']));
    }

    $max_use=1;
    if (isset($_POST['max_use']) && strlen($_POST['max_use']) > 0) {
        $max_use = (int)$_POST['max_use'];
    }

    $anos=add_coupon($dbcon, $coupon,$discount_fixed,$discount_percentage,$description,$max_use,$addedby);
    if($anos>0){
        $response['error'] = false;
        $response['message'] = "Added Successfully.";
    }else{
        throw new \Exception("Could not add!", 1);
    }

} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//pack_coupon(coupon,discount_fixed,discount_percentage,description,max_use,isactive,createdat,createdby)
function add_coupon($dbcon, $coupon,$discount_fixed,$discount_percentage,$description,$max_use,$createdby)
{

    date_default_timezone_set("Asia/Dhaka");
    $createdat = date("Y-m-d H:i:s");

    $sql = "INSERT INTO pack_coupon(coupon,discount_fixed,discount_percentage,description,max_use,isactive,createdat,createdby)
            VALUES (?, ?, ?, ?, ?, 0, ?, ?)";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: ".$dbcon->error);
    }

    $stmt->bind_param("sddsisi", $coupon,$discount_fixed,$discount_percentage,$description,$max_use,$createdat,$createdby);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}

?>
