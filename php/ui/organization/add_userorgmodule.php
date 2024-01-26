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

    //orgno, userno, moduleno
    if (isset($_POST['purchaseno']) && strlen($_POST['purchaseno']) > 0) {
        $purchaseno = (int)$_POST['purchaseno'];
    }else{
        throw new \Exception("Package must be selected!", 1);
    }

    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int)$_POST['orgno'];
    }else{
        throw new \Exception("Organization must be selected!", 1);
    }

    if (isset($_POST['username']) && strlen($_POST['username']) > 0) {
        $username = strip_tags($_POST['username']);
    }else{
        throw new \Exception("User must be selected!", 1);
    }

    if (isset($_POST['moduleno']) && strlen($_POST['moduleno']) > 0) {
        $moduleno = (int)$_POST['moduleno'];
    }else{
        throw new \Exception("Module must be selected!", 1);
    }

    $result=get_userno($dbcon, $username);
    if($result->num_rows>0){
        $foruserno = $result->fetch_array(MYSQLI_ASSOC)['userno'];
    }else{
        throw new Exception('Invalid User!',1);
    }

    $dbcon->begin_transaction();
    $anos=add_userorgmodule($dbcon, $orgno, $foruserno, $moduleno);
    if($anos>0){
        $appliedno=insert_appliedpackage($dbcon,$purchaseno, $orgno, $username, $addedby);
        if($appliedno>0){
            $response['error'] = false;
            $response['message'] = "Added Successfully.";
        }else{
            $dbcon->rollback();
            throw new Exception('User-module failed! Check your package and try again.',1);
        }
    }else{
        $dbcon->rollback();
        throw new \Exception("Could not add!", 1);
    }

    $dbcon->commit();

} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//gen_users (userno,username,firstname,lastname,email,countrycode,contactno,passphrase,authkey,userstatusno,ucreatedatetime,reset_pass_count,updatetime)
function get_userno($dbcon, $username)
{

    $sql = "SELECT userno
            FROM gen_users
            WHERE username=?";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: ".$dbcon->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

//acc_userorgmodules (orgno, userno, moduleno, verified)
function add_userorgmodule($dbcon, $orgno, $foruserno, $moduleno)
{

    $sql = "INSERT INTO acc_userorgmodules(orgno, userno, moduleno)
            VALUES(?, ?, ?)";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: ".$dbcon->error);
    }

    $stmt->bind_param("iii", $orgno, $foruserno, $moduleno);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}

//pack_appliedpackage(appliedno,purchaseno,item,orgno,assignedto, appliedat, appliedby)
function insert_appliedpackage($dbcon,$purchaseno, $orgno, $foruserno, $appliedby){
    date_default_timezone_set("Asia/Dhaka");
    $appliedat = date("Y-m-d H:i:s");

    $sql = "INSERT INTO pack_appliedpackage(purchaseno,item,orgno,assignedto, appliedat, appliedby)
            VALUES(?,'ORGUSER',?,?,?,?)";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("iissi", $purchaseno, $orgno, $foruserno, $appliedat,$appliedby);
    $stmt->execute();
    $result=$stmt->affected_rows;
    $stmt->close();
    return $result;
}
?>
