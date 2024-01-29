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

    $offerno = -1;
    if (isset($_POST['offerno']) && strlen($_POST['offerno']) > 0) {
        $offerno = (int)$_POST['offerno'];
    }

    if (isset($_POST['offertitle']) && strlen($_POST['offertitle']) > 0) {
        $offertitle = trim(strip_tags($_POST['offertitle']));
    }else{
        throw new \Exception("Title cannot be empty!", 1);
    }

    $offerdetail=NULL;
    if (isset($_POST['offerdetail']) && strlen($_POST['offerdetail']) > 0) {
        $offerdetail = trim(strip_tags($_POST['offerdetail']));
    }

    if (isset($_POST['rate']) && strlen($_POST['rate']) > 0) {
        $rate = (double)$_POST['rate'];
    }else{
        throw new \Exception("Rate cannot be empty!", 1);
    }

    $tag=NULL;
    if (isset($_POST['tag']) && strlen($_POST['tag']) > 0) {
        $tag = trim(strip_tags($_POST['tag']));
    }

    $is_coupon_applicable = 0;
    if (isset($_POST['is_coupon_applicable']) && strlen($_POST['is_coupon_applicable']) > 0) {
        $is_coupon_applicable = (int)$_POST['is_coupon_applicable'];
    }

    $validuntil=NULL;
    if (isset($_POST['validuntil']) && strlen($_POST['validuntil']) > 0) {
        $validuntil = trim(strip_tags($_POST['validuntil']));
    }

    if (isset($_POST['offeritems']) && strlen($_POST['offeritems']) > 0) {
        $offeritems = json_decode(trim(strip_tags($_POST['offeritems'])),true);
    }else{
        throw new \Exception("There must be at least ONE item!", 1);
    }

    $dbcon->begin_transaction();
    if($offerno>0){
        $unos=update_offer($dbcon, $offertitle, $offerdetail, $rate, $tag, $is_coupon_applicable, $validuntil,$offerno);
        del_offeritems($dbcon, $offerno);
    }else{
        $offerno=add_offer($dbcon, $offertitle, $offerdetail, $rate, $tag, $is_coupon_applicable,$validuntil);
        if($offerno <= 0){
            $dbcon->rollback();
            throw new \Exception("Could not add!", 1);
        }
    }

    foreach($offeritems as $ITEM){
        add_offeritems($dbcon, $offerno,$ITEM['item'],(int)$ITEM['qty']);
    }

    if($dbcon->commit()){
        $response['error'] = false;
        $response['message'] = "Saved Successfully.";
    }else{
        $dbcon->rollback();
        throw new \Exception("Could not save!", 1);
    }

} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//pack_offer(offerno, offertitle, offerdetail, rate, tag, is_coupon_applicable, validuntil)
function add_offer($dbcon, $offertitle, $offerdetail, $rate, $tag, $is_coupon_applicable,$validuntil)
{

    $sql = "INSERT INTO pack_offer(offertitle, offerdetail, rate, tag, is_coupon_applicable, validuntil)
            VALUES (?, ?, ?, ?, ?, ?)";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: ".$dbcon->error);
    }

    $stmt->bind_param("ssdsis", $offertitle, $offerdetail,$rate, $tag, $is_coupon_applicable,$validuntil);
    $stmt->execute();
    $result = $stmt->insert_id;
    $stmt->close();

    return $result;
}

//pack_offer(offerno, offertitle, offerdetail, rate, tag, is_coupon_applicable, validuntil)
function update_offer($dbcon, $offertitle, $offerdetail, $rate, $tag, $is_coupon_applicable, $validuntil,$offerno)
{

    $sql = "UPDATE pack_offer
            SET offertitle=?, offerdetail=?, rate=?, tag=?, is_coupon_applicable=?, validuntil=?
            WHERE offerno=?";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: ".$dbcon->error);
    }

    $stmt->bind_param("ssdsisi", $offertitle, $offerdetail,$rate, $tag, $is_coupon_applicable,$validuntil,$offerno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}

//pack_offeritems(offerno,item,qty)
function del_offeritems($dbcon, $offerno){
    $sql = "DELETE
            FROM pack_offeritems
            WHERE offerno=?";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: ".$dbcon->error);
    }

    $stmt->bind_param("i", $offerno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}

//pack_offeritems(offerno,item,qty)
function add_offeritems($dbcon, $offerno,$item,$qty){
    $sql = "INSERT INTO pack_offeritems(offerno,item,qty)
            VALUES(?,?,?)";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: ".$dbcon->error);
    }

    $stmt->bind_param("isi", $offerno,$item,$qty);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}
?>
