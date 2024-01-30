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

if (isset($_POST['accyear']) && strlen($_POST['accyear']) > 0) {
    $accyear = strip_tags($_POST['accyear']);
} else {
    throw new Exception("Accounting-year must be selected!", 1);
}

require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try {
    $rs_userorg = get_info_organization($dbcon, $userno, $orgno);
    if ($rs_userorg->num_rows > 0) {
        $userorg = $rs_userorg->fetch_array(MYSQLI_ASSOC);
        if (isset($_SESSION['orgno'])) {
            unset($_SESSION['orgno']);
        }
        
        $isvalid = check_org_validity($dbcon,$orgno);

        if ($isvalid!=1) {
            $response['error'] = true;
            throw new Exception("Your organization is not active now.\n
                                        Please renew online OR\n
                                        Please contact the organization admin.", 1);
        } else {
            $_SESSION['orgno'] = $userorg['orgno'];
            $_SESSION['org_picurl'] = $userorg['picurl'];
            $_SESSION['orgname'] = $userorg['orgname'];
            $_SESSION['orglocation'] = $userorg['street'] . ', ' . $userorg['city'] . ', ' . $userorg['country'];
            // $_SESSION['moduleno'] = $userorg['moduleno'];
            // $_SESSION['moduletitle'] = $userorg['moduletitle'];
            
            $permitted_modules = get_userorgmodules($dbcon, $userorg['orgno'], $userno);
            if ($permitted_modules->num_rows > 0) {
                $meta_array = array();
                while ($row = $permitted_modules->fetch_array(MYSQLI_ASSOC)) {
                    $meta_array[] = $row;
                }
                $_SESSION['modules'] = json_encode($meta_array);
            }

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
//com_userorgmodules(orgno,userno,moduleno,verified)
//com_modules(moduleno,moduletitle)
function get_info_organization($dbcon, $userno, $orgno)
{

    $sql = "SELECT uo.orgno,o.orgname,o.street, o.city, o.country, o.picurl,
                    uo.moduleno,(SELECT moduletitle FROM com_modules WHERE moduleno=uo.moduleno) as moduletitle
            FROM com_userorgmodules as uo
                INNER JOIN com_orgs as o ON uo.orgno=o.orgno
            WHERE uo.userno=?
                AND uo.orgno=?
                AND uo.isactive=1";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ii", $userno, $orgno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

//pack_appliedpackage(appliedno,purchaseno,orgno,starttime,assignedto, duration,appliedat, appliedby)
function check_org_validity($dbcon,$orgno){

    $sql = "SELECT appliedno,purchaseno,assignedto,starttime,DATE(DATE_ADD(starttime, INTERVAL duration DAY)) as closingdate
            FROM pack_appliedpackage
            WHERE orgno=?
                AND (CURRENT_DATE() BETWEEN DATE(starttime) AND DATE(DATE_ADD(starttime, INTERVAL duration DAY)))";

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


function get_userorgmodules($dbcon, $orgno, $userno)
{
    $sql = "SELECT moduleno,(SELECT moduletitle FROM com_modules WHERE moduleno=um.moduleno) as moduletitle
            FROM com_userorgmodules AS um
            WHERE um.orgno=? AND um.userno=? AND isactive=1";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ii", $orgno, $userno);
    $stmt->execute();

    $result = $stmt->get_result();

    $stmt->close();

    return $result;
}

