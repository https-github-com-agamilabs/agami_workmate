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

    //orgno, userno
    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int)$_POST['orgno'];
    } else {
        throw new \Exception("Organization must be selected!", 1);
    }

    if (isset($_POST['userno']) && strlen($_POST['userno']) > 0) {
        $foruserno = (int) $_POST['userno'];
    } else {
        throw new \Exception("User must be selected!", 1);
    }

    // ==============

    if (isset($_POST['purchaseno']) && strlen($_POST['purchaseno']) > 0) {
        $purchaseno = (int)$_POST['purchaseno'];
    } else {
        throw new \Exception("Package must be selected!", 1);
    }

    $dbcon->begin_transaction();
    $anos = add_userorg($dbcon, $orgno, $userno, $_POST);
    if ($anos > 0) {
        $appliedno = insert_appliedpackage($dbcon, $purchaseno, $orgno, $username, $userno);
        if ($appliedno > 0) {
            $response['error'] = false;
            $response['message'] = "Added Successfully.";
        } else {
            $dbcon->rollback();
            throw new Exception('User-module failed! Check your package and try again.', 1);
        }
    } else {
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

//hr_user (userno,username,firstname,lastname,email,countrycode,primarycontact,passphrase,authkey,userstatusno,ucreatedatetime,updatetime)
//com_userorg (uono,orgno,userno,uuid,ucatno,supervisor,moduleno,jobtitle,hourlyrate,monthlysalary,permissionlevel,
//              dailyworkinghour,timeflexibility,shiftno,starttime,endtime,timezone,isactive)
function add_userorg($dbcon, $orgno, $userno, $data)
{
    $sql="INSERT INTO com_userorg (orgno,userno,uuid,ucatno,supervisor,moduleno,jobtitle,hourlyrate,monthlysalary,
                  dailyworkinghour,timeflexibility,permissionlevel,timezone,shiftno,starttime,endtime,isactive)
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1)
            ";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("iisiiisddiiisiss", $orgno, $userno, 
                                strip_tags($data['uuid']),
                                (int)$data['ucatno'],
                                (int)$data['supervisor'],#
                                (int)$data['moduleno'],
                                strip_tags($data['jobtitle']),
                                (double)$data['hourlyrate'],
                                (double)$data['monthlysalary'],
                                (int)$data['dailyworkinghour'],
                                (int)$data['timeflexibility'],
                                (int)$data['permissionlevel'],
                                strip_tags($data['timezone']),
                                (int)$data['shiftno'],
                                strip_tags($data['starttime']),
                                strip_tags($data['endtime'])
                            );
    $stmt->execute();
    $result = $stmt->insert_id;
    $stmt->close();
    return $result;
}


//pack_appliedpackage(appliedno,purchaseno,item,orgno,assignedto, appliedat, appliedby)
function insert_appliedpackage($dbcon, $purchaseno, $orgno, $foruserno, $appliedby)
{
    date_default_timezone_set("Asia/Dhaka");
    $appliedat = date("Y-m-d H:i:s");

    $sql = "INSERT INTO pack_appliedpackage(purchaseno,item,orgno,assignedto, appliedat, appliedby)
            VALUES(?,'ORGUSER',?,?,?,?)";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("iissi", $purchaseno, $orgno, $foruserno, $appliedat, $appliedby);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $stmt->close();
    return $result;
}
