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

    $rs_packages = get_my_package_usability($dbcon,$userno);

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

//pack_offer(offerno, offertitle, offerdetail, rate, tag, is_coupon_applicable, validuntil)
//pack_offeritems(offerno,item,qty)
//pack_appliedpackage(appliedno,purchaseno,item,orgno,assignedto, appliedat, appliedby)
function get_my_package_usability($dbcon,$userno)
{
    $sql = "SELECT mp.purchaseno,mp.licensekey,
                    mp.offerno,(SELECT offertitle FROM pack_offer WHERE offerno=mp.offerno) as offertitle,
                    mp.item, mp.package_qty, IFNULL(mu.used_qty,0) as used_qty
            FROM
                (SELECT po.purchaseno,po.offerno,po.licensekey,oi.item,oi.qty as package_qty
                FROM pack_purchaseoffer as po
                    INNER JOIN pack_offeritems oi ON oi.offerno=po.offerno
                WHERE foruserno=?
                ) as mp
                LEFT JOIN
                (SELECT purchaseno,'ORGUSER' as item, count(assignedto) as  used_qty
                FROM pack_appliedpackage                
                WHERE appliedby=?
                GROUP BY purchaseno,item
                ) as mu
                ON mp.purchaseno=mu.purchaseno AND mp.item=mu.item AND mp.package_qty>mu.used_qty
            ";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ii", $userno,$userno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}
