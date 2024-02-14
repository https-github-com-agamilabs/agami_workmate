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

if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
    $orgno = (int) $_POST['orgno'];
} else {
    throw new Exception("Organization must be selected", 1);
}

require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try {
    $rs_userorg = get_info_organization($dbcon, $userno, $orgno);
    if ($rs_userorg->num_rows > 0) {
        $userorg = $rs_userorg->fetch_array(MYSQLI_ASSOC);
        if (isset($_SESSION['wm_orgno'])) {
            unset($_SESSION['wm_orgno']);
        }
        
        $isvalid = check_org_validity($dbcon,$orgno);

        if ($isvalid!=1) {
            $response['error'] = true;
            throw new Exception("Your organization is not active now.\n
                                        Please renew online OR\n
                                        Please contact the organization admin.", 1);
        } else {
            $_SESSION['wm_orgno'] = $userorg['orgno'];
            $_SESSION['wm_org_picurl'] = $userorg['picurl'];
            $_SESSION['wm_orgname'] = $userorg['orgname'];
            $_SESSION['wm_orglocation'] = $userorg['street'] . ', ' . $userorg['city'] . ', ' . $userorg['country'];
            $_SESSION['wm_timeflexibility'] = $userorg['timeflexibility'];
            $_SESSION['wm_starttime'] = $userorg['starttime'];
            $_SESSION['wm_endtime'] = $userorg['endtime'];
            $_SESSION['wm_ucatno'] = $userorg['ucatno'];
            $_SESSION['wm_ucattitle'] = $userorg['ucattitle'];
            $_SESSION['wm_designation'] = $userorg['designation'];
            $_SESSION['wm_permissionlevel'] = $userorg['permissionlevel'];
            $_SESSION['wm_moduleno'] = $userorg['moduleno'];
            $_SESSION['wm_moduletitle'] = $userorg['moduletitle'];

            $response['error'] = false;
            $response['redirecturl'] = "time_keeper.php";
        }
    } else {
        throw new Exception("You have no organization or yet not verified.", 1);
    }
} catch (\Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//com_orgs (orgno, orgname, street, city, state, country, gpslat, gpslon, orgtypeid, privacy, picurl, contactno, orgnote, weekend1, weekend2, starttime, endtime, verifiedno)
//com_userorg (uono,orgno,userno,uuid,ucatno,supervisor,moduleno,designation,hourlyrate,monthlysalary,permissionlevel,dailyworkinghour,timeflexibility,shiftno,starttime,endtime,isactive)
//com_modules(moduleno,moduletitle)
//hr_usercat(ucatno, ucattitle)
function get_info_organization($dbcon, $userno, $orgno)
{

    $sql = "SELECT 
                uo.uuid,
                uo.ucatno,(SELECT ucattitle FROM hr_usercat WHERE ucatno=uo.ucatno) as ucattitle,
                uo.moduleno,(SELECT moduletitle FROM com_modules WHERE moduleno=uo.moduleno) as moduletitle,
                uo.designation,uo.hourlyrate,uo.monthlysalary,uo.permissionlevel,uo.dailyworkinghour,
                uo.timeflexibility,
                uo.shiftno,
                uo.starttime,uo.endtime,
                o.orgno, o.orgname, o.street, o.city, o.state, o.country, o.gpslat, o.gpslon, 
                o.orgtypeid, 
                o.privacy, 
                o.picurl, o.primarycontact, o.orgnote, o.weekend1, o.weekend2, o.starttime, o.endtime, verifiedno
            FROM com_orgs as o
                INNER JOIN (SELECT *
                            FROM com_userorg
                            WHERE isactive=1 AND userno=? AND orgno=?) as uo
                ON o.orgno=uo.orgno";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ii", $userno, $orgno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

//pack_appliedpackage(appliedno,purchaseno,orgno,starttime, duration,appliedat, appliedby)
function check_org_validity($dbcon,$orgno){

    $sql = "SELECT appliedno,purchaseno,users,starttime,DATE(DATE_ADD(starttime, INTERVAL duration DAY)) as closingdate
            FROM pack_appliedpackage
            WHERE orgno=? AND (CURRENT_DATE() BETWEEN DATE(starttime) AND DATE(DATE_ADD(starttime, INTERVAL duration DAY)))";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $orgno);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $stmt->close();
            return 1;
        } else {
            $stmt->close();
            return 0;
        }
    } else {
        return -1;
    }
}

