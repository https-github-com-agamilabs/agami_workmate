<?php
$base_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
include_once  $base_path . "/php/ui/login/check_session.php";

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

require_once $base_path . "/php/ui/dependency_checker.php";

try {

    $isactive = -1;
    if (isset($_POST['isactive']) && strlen($_POST['isactive']) > 0) {
        $isactive = (int)$_POST['isactive'];
    }

    $min_use = -1;
    if (isset($_POST['min_use']) && strlen($_POST['min_use']) > 0) {
        $min_use = (int)$_POST['min_use'];
    }

    $result = get_coupons($dbcon, $isactive, $min_use);

    $meta_array = array();
    if ($result->num_rows > 0) {
        $response['error'] = false;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
        }

        $response['data'] = $meta_array;
    } else {
        $response['error'] = true;
        $response['message'] = "No Coupon Found!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//pack_coupon(coupon,discount_fixed,discount_percentage,description,max_use,isactive,createdat,createdby)
function get_coupons($dbcon, $isactive, $min_use)
{

    $params = array();

    $params[] = &$min_use;
    $filter = " max_use >= ?";
    $types = 'i';

    if ($isactive > -1) {
        $params[] = &$isactive;
        $filter .= " AND isactive=?";
        $types .= 'i';
    }

    $sql = "SELECT coupon,discount_fixed,discount_percentage,description,
                max_use,(SELECT count(coupon) FROM pack_purchaseoffer WHERE coupon=c.coupon) as already_used_qty,
                isactive,createdat,
                createdby,firstname,lastname,countrycode,contactno
            FROM pack_coupon as c
                INNER JOIN hr_user as u ON c.createdby=u.userno
            WHERE $filter
            ORDER BY createdat DESC";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: " . $dbcon->error);
    }

    if (strlen($types) > 0) {
        call_user_func_array(array($stmt, "bind_param"), array_merge(array($types), $params));
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
?>