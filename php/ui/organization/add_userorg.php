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

    $uuid=strip_tags($_POST['uuid']);
    $ucatno=isset($_POST['ucatno'])?(int)$_POST['ucatno']:1;
    $supervisor=isset($_POST['supervisor']) && strlen($_POST['supervisor'])>0?(int)$_POST['supervisor']:NULL;
    $moduleno=isset($_POST['moduleno']) && strlen($_POST['moduleno'])>0?(int)$_POST['moduleno']:NULL;
    $designation=isset($_POST['designation']) && strlen($_POST['designation'])>0?strip_tags($_POST['designation']):NULL;
    $hourlyrate=isset($_POST['hourlyrate']) && strlen($_POST['hourlyrate'])>0 ?(double)$_POST['hourlyrate']:NULL;
    $monthlyrate=isset($_POST['monthlysalary'])  && strlen($_POST['monthlysalary'])>0?(double)$_POST['monthlysalary']:NULL;
    $dailyworkinghour=isset($_POST['dailyworkinghour'])?(int)$_POST['dailyworkinghour']:8;
    $timeflexibility=isset($_POST['timeflexibility'])?(int)$_POST['timeflexibility']:1;
    $permissionlevel=isset($_POST['permissionlevel']) && strlen($_POST['permissionlevel'])>0?(int)$_POST['permissionlevel']:NULL;
    $timezone=isset($_POST['timezone'])?strip_tags($_POST['timezone']):'Asia/Dhaka';
    $shiftno=isset($_POST['shiftno'])?(int)$_POST['shiftno']:1;
    $starttime=isset($_POST['starttime'])?strip_tags($_POST['starttime']):'9:00:00';
    $endtime=isset($_POST['endtime'])?strip_tags($_POST['endtime']):'18:00:00';
    // ==============

    if (isset($_POST['purchaseno']) && strlen($_POST['purchaseno']) > 0) {
        $purchaseno = (int)$_POST['purchaseno'];
    } else {
        throw new \Exception("Package must be selected!", 1);
    }

    $anos = add_userorg($dbcon, $orgno, $foruserno, 
                                $uuid,$ucatno,$supervisor,$moduleno,
                                $designation,$hourlyrate,$monthlyrate,$dailyworkinghour,
                                $timeflexibility,$permissionlevel,$timezone,$shiftno,
                                $starttime,$endtime
                                );
    if ($anos > 0) {
        $response['error'] = false;
        $response['message'] = "Added Successfully.";
    } else {
        throw new \Exception("Could not add!", 1);
    }

} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//hr_user (userno,username,firstname,lastname,affiliation,jobtitle,email,countrycode,primarycontact,passphrase,authkey,userstatusno,ucreatedatetime,updatetime)
//com_userorg (uono,orgno,userno,uuid,ucatno,supervisor,moduleno,designation,hourlyrate,monthlysalary,permissionlevel,
//              dailyworkinghour,timeflexibility,shiftno,starttime,endtime,timezone,isactive)
function add_userorg($dbcon, $orgno, $userno, 
                            $uuid,$ucatno,$supervisor,$moduleno,
                            $designation,$hourlyrate,$monthlyrate,$dailyworkinghour,
                            $timeflexibility,$permissionlevel,$timezone,$shiftno,
                            $starttime,$endtime
                            )
{
    $sql="INSERT INTO com_userorg (orgno,userno,uuid,ucatno,supervisor,moduleno,designation,hourlyrate,monthlysalary,
                  dailyworkinghour,timeflexibility,permissionlevel,timezone,shiftno,starttime,endtime,isactive)
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1)
            ";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("iisiiisddiiisiss", $orgno, $userno, 
                                        $uuid,$ucatno,$supervisor,$moduleno,
                                        $designation,$hourlyrate,$monthlyrate,$dailyworkinghour,
                                        $timeflexibility,$permissionlevel,$timezone,$shiftno,
                                        $starttime,$endtime );
    $stmt->execute();
    $result = $stmt->insert_id;
    $stmt->close();
    return $result;
}

