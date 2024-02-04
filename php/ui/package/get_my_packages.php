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

    $rs_my_purchaseoffer = get_my_purchaseoffer($dbcon, $userno);
    $meta_array = array();
    if ($rs_my_purchaseoffer->num_rows > 0) {
        while ($row = $rs_my_purchaseoffer->fetch_array(MYSQLI_ASSOC)) {
            $purchaseno = $row['purchaseno'];
            $rs_applied = get_appliedpackage_info($dbcon, $purchaseno);

            $appliedArray = array();
            if ($rs_applied->num_rows > 0) {
                while ($arow = $rs_applied->fetch_array(MYSQLI_ASSOC)) {
                    $appliedArray[] = $arow;
                }
            }
            $row['appliedwith'] = $appliedArray;
            $meta_array[] = $row;
        }

        $response['error'] = false;
        $response['data'] = $meta_array;
    } else {
        $response['error'] = true;
        $response['message'] = "No Package Found!";
    }
} catch (\Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();


//pack_purchaseoffer(purchaseno, offerno, buyeruserno, foruserno, licensekey, entryat, coupon, amount, discount,
//                  txrefidbase, txrefid, paymentID, pgno, ispaid, trxID, paidamount, paidat)
//pack_offer(offerno, offertitle, offerdetail,org_qty,user_qty,accyear_qty,rate, tag, is_coupon_applicable, validuntil);
function get_my_purchaseoffer($dbcon, $foruserno)
{

    $sql = "SELECT po.purchaseno,po.offerno,
                    po.buyeruserno,(SELECT CONCAT(firstname, ' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=po.buyeruserno) as buyername,
                    foruserno,(SELECT CONCAT(firstname, ' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=po.buyeruserno) as ownername,
                    licensekey,amount,po.discount,paidamount,paidat,
                    offertitle,offerdetail,rate
            FROM pack_purchaseoffer as po
                INNER JOIN pack_offer as o ON o.offerno=po.offerno
            WHERE po.foruserno=? AND licensekey IS NOT NULL";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $foruserno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

//pack_appliedpackage(appliedno,purchaseno,item,orgno,assignedto, appliedat, appliedby)
function get_appliedpackage_info($dbcon, $purchaseno)
{
    $sql = "SELECT purchaseno,item,
                orgno,(SELECT orgname FROM com_orgs WHERE orgno=ap.orgno) as orgname,
                assignedto,
                appliedat, appliedby
            FROM pack_appliedpackage as ap
            WHERE purchaseno=?";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $purchaseno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}
