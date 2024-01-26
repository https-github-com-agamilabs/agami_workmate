<?php
$base_path = dirname(dirname(dirname(__FILE__)));
include_once($base_path . "/ui/login/check_session.php");

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";


try {

    if (isset($_POST['coupon']) && strlen($_POST['coupon']) > 0) {
        $coupon = trim(strip_tags($_POST['coupon']));
        $rs_coupon = get_coupon_info($dbcon, $coupon);
        if ($rs_coupon->num_rows > 0) {
            $row = $rs_coupon->fetch_array(MYSQLI_ASSOC);
            $response['results'] = $row;
        }else{
            $response['results']['discount_fixed'] =0.0;
            $response['results']['discount_percentage'] =0.0;
            throw new Exception("The code you entered is invalid!", 1);
        }
    }else{
        $response['results']['discount_fixed'] =0.0;
        $response['results']['discount_percentage'] =0.0;
        throw new Exception("Promo code cannot be empty!", 1);
    }

} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//pack_coupon(coupon,discount_fixed,discount_percentage,description,max_use,isactive,createdat,createdby)
function get_coupon_info($dbcon, $coupon)
{
    $sql = "SELECT discount_fixed, discount_percentage
            FROM pack_coupon
            WHERE isactive=1 AND coupon=?
                AND max_use > (SELECT count(coupon)
                                FROM pack_purchaseoffer
                                WHERE coupon=? AND ispaid>0)
            LIMIT 1";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ss", $coupon,$coupon);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

