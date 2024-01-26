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

    $tag = NULL;
    if (isset($_POST['tag']) && strlen($_POST['tag']) > 0) {
        $tag = trim(strip_tags($_POST['tag']));
    }

    $result = get_filtered_offer($dbcon, $tag);

    $meta_array = array();
    if ($result->num_rows > 0) {
        $response['error'] = false;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $ts_items=get_offeritems($dbcon, $row['offerno']);
            if ($ts_items->num_rows > 0) {
                while ($irow = $ts_items->fetch_array(MYSQLI_ASSOC)){
                    $row['items'][]=$irow;
                }
            }
            $meta_array[] = $row;
        }

        $response['data'] = $meta_array;
    } else {
        $response['error'] = true;
        $response['message'] = "No Item Found!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//pack_offer(offerno, offertitle, offerdetail, rate, tag, is_coupon_applicable, validuntil)
function get_filtered_offer($dbcon)
{

    $sql = "SELECT offerno, offertitle, offerdetail, rate, tag, is_coupon_applicable
            FROM pack_offer
            WHERE validuntil >= NOW()
            ORDER BY offerno";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: ".$dbcon->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

//pack_offeritems(offerno,item,qty)
//pack_items(item, itemtitle)
function get_offeritems($dbcon, $offerno)
{
    $sql = "SELECT item,(SELECT itemtitle FROM pack_items WHERE item=oi.item) as itemtitle,
                    qty
            FROM pack_offeritems as oi
            WHERE offerno=?";

    // var_dump($sql);

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: ".$dbcon->error);
    }

    $stmt->bind_param("i", $offerno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
    }

?>
