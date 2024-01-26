<?php
$base_path = dirname(dirname(dirname(__FILE__)));
include_once($base_path . "/ui/login/check_session.php");


$response = array();
$response['error'] = false;
$response['message'] = '';
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = 'Invalid request method!';
    echo json_encode($response);
    exit();
}

require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try {
    //$offerno, $buyeruserno, $licensekey, $amount
    if (isset($_POST['offerno']) && strlen($_POST['offerno']) > 0) {
        $offerno = (int) $_POST['offerno'];
        $rs_offer = get_offer_info($dbcon, $offerno);
        if ($rs_offer->num_rows > 0) {
            $row = $rs_offer->fetch_array(MYSQLI_ASSOC);
            $rate = $row['rate'];
        } else {
            throw new Exception("Invalid Promo Code! Offer may have been already expired.", 1);
        }
    } else {
        throw new Exception("Offer must be selected", 1);
    }

    if (isset($_POST['coupon']) && strlen($_POST['coupon']) > 0) {
        $coupon = trim(strip_tags($_POST['coupon']));
        $rs_coupon = get_coupon_info($dbcon, $coupon);
        if ($rs_coupon->num_rows > 0) {
            $row = $rs_coupon->fetch_array(MYSQLI_ASSOC);
            if(isset($row['discount_fixed']) && $row['discount_fixed']>0){
                if(isset($row['discount_percentage']) && $row['discount_percentage']>0){
                    $discountp = $rate * $row['discount_percentage']/100;
                    $total_discount = ($discountp <= $row['discount_fixed']) ? $discountp : $row['discount_fixed'];
                }else{
                    $total_discount =$row['discount_fixed'];
                }
            }else{
                if(isset($row['discount_percentage']) && $row['discount_percentage']>0){
                    $total_discount = $rate * $row['discount_percentage']/100;
                }else{
                    $total_discount = 0.0;
                }
            }
        } else {
            $total_discount = 0.0;
        }
    } else {
        $coupon = NULL;
        $total_discount = 0.0;
    }

    if($rate <= $total_discount){
        $total_discount=0.0;
        throw new Exception("Invalid Promo Code!!", 1);
    }

    if (isset($_POST['visible_discount']) && strlen($_POST['visible_discount']) > 0) {
        $visible_discount = (float) $_POST['visible_discount'];
        if($total_discount!=$visible_discount){
            throw new Exception("Invalid Promo Code!!", 1);
        }
    }
    // include_once($base_path . "/utility/Utils.php");
    // $util = new Utils();
    $licensekey = null; //$util->generateLicenseKey(12);

    $buyeruserno = $userno;
    $txrefidbase = "U" . str_pad($buyeruserno, 5, "0", STR_PAD_LEFT) . "P" . str_pad($offerno, 3, "0", STR_PAD_LEFT);
    $txrefid = $txrefidbase . time();

    $inos = add_purchaseoffer($dbcon, $offerno, $buyeruserno, $txrefidbase, $txrefid, $licensekey, $rate, $total_discount, $coupon);

    if (!$inos) {
        throw new Exception("Could not apply package!", 1);
    } else {


        $params = "type=purchase_package";
        $params .= "&invoice_reference=" . $txrefid;
        $params .= "&amount=" . ($rate - $total_discount);
        $params .= "&env=dev";
        // $params .= "&redirecturl=".rawurlencode($redirecturl);

        $response['error'] = false;
        $response['paymenturl'] = $publicAccessUrl . "payment/bkash/index.php?" . $params;
        $response['message'] = 'Package applied successfully.';
    }
} catch (\Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();


//-- pack_purchaseoffer(purchaseno, offerno, buyeruserno, foruserno, licensekey, entryat, coupon, amount, discount,
//                  txrefidbase, txrefid, paymentID, pgno, ispaid, trxID, paidamount, paidat);
//pack_offer(offerno, offertitle, org_qty,user_qty,accyear_qty,rate, tag, is_coupon_applicable, validuntil);
function add_purchaseoffer($dbcon, $offerno, $buyeruserno,  $txrefidbase, $txrefid, $licensekey, $amount, $discount, $coupon)
{
    date_default_timezone_set("Asia/Dhaka");
    $entryat = date("Y-m-d H:i:s");

    $sql = "INSERT INTO pack_purchaseoffer(offerno, buyeruserno, foruserno, licensekey, txrefidbase, txrefid, amount, discount, coupon, entryat)
            VALUES (?,?,?,?,?,?,?,?,?,?)";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("iiisssddss", $offerno, $buyeruserno, $buyeruserno,$licensekey, $txrefidbase, $txrefid, $amount, $discount, $coupon, $entryat);

    if ($stmt->execute()) {
        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    } else {
        return false;
    }
}

function get_offer_info($dbcon, $offerno)
{
    $sql = "SELECT rate
            FROM pack_offer
            WHERE offerno=?
            LIMIT 1";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $offerno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

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
    $stmt->bind_param("ss", $coupon, $coupon);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}
