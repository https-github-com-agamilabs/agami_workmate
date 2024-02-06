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

    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int) $_POST['orgno'];
    } else {
        throw new Exception("Organization must be selected", 1);
    }

    $rs_packages = get_my_package_usability($dbcon,$userno,$orgno);

    if ($rs_packages->num_rows > 0) {
        $meta_array = array();
        while ($row = $rs_packages->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
        }
        $response['error'] = false;
        $response['results'] = $meta_array;
    } else {
        throw new \Exception("No Valid Package Found!", 1);
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//pack_purchaseoffer(purchaseno, offerno, buyeruserno, foruserno, licensekey, entryat, coupon, amount, discount,
//                  txrefidbase, txrefid, paymentID, pgno, ispaid, trxID, paidamount, paidat)

//pack_offer(offerno, offertitle, offerdetail,users, duration, rate, tag, is_coupon_applicable, validuntil)
//pack_offeritems(offerno,item,qty)
//pack_appliedpackage(appliedno,purchaseno,orgno,starttime, duration,appliedat, appliedby)
function get_my_package_usability($dbcon,$userno,$orgno)
{
    $sql = "SELECT po.purchaseno,
                    po.offerno,(SELECT offertitle FROM pack_offer WHERE offerno=po.offerno) as offertitle,
                    po.licensekey,
                    ap.users as max_user_qty, ap.starttime, ap.duration
            FROM pack_purchaseoffer as po
                LEFT JOIN (
                    SELECT purchaseno,starttime, duration
                    FROM pack_appliedpackage                
                    WHERE orgno=?) as ap ON po.purchaseno=ap.purchaseno
            WHERE po.foruserno=?
                AND (ap.starttime IS NULL OR (CURRENT_DATE() BETWEEN DATE(ap.starttime) AND DATE(DATE_ADD(ap.starttime, INTERVAL ap.duration DAY))))
            ";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ii", $userno,$orgno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}
