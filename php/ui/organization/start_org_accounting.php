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
        if (isset($_SESSION['moduleno'])) {
            unset($_SESSION['moduleno']);
        }

        if (isset($_SESSION['accyear'])) {
            unset($_SESSION['accyear']);
        }

        $accyearInfo = is_valid_accyear($dbcon, $orgno, $accyear);

        if ($accyearInfo['error']) {
            $response['error'] = true;
            throw new Exception("You have no active accounting year.\n
                                        You must have a running accounting year for many operations. \n
                                        We recomend you to start a new accounting year as soon as possible.\n
                                        Please contact the organization admin.", 1);
        } else {
            $_SESSION['orgno'] = $userorg['orgno'];
            $_SESSION['org_picurl'] = $userorg['picurl'];
            $_SESSION['orgname'] = $userorg['orgname'];
            $_SESSION['orglocation'] = $userorg['street'] . ', ' . $userorg['city'] . ', ' . $userorg['country'];
            // $_SESSION['moduleno'] = $userorg['moduleno'];
            // $_SESSION['moduletitle'] = $userorg['moduletitle'];
            $_SESSION['accyear'] = $accyear;
            $_SESSION['accyear_startdate'] = $accyearInfo['data']['startdate'];
            $_SESSION['accyear_enddate'] = $accyearInfo['data']['closingdate'];

            $permitted_modules = get_userorgmodules($dbcon, $userorg['orgno'], $userno);
            if ($permitted_modules->num_rows > 0) {
                $meta_array = array();
                while ($row = $permitted_modules->fetch_array(MYSQLI_ASSOC)) {
                    $meta_array[] = $row;
                }
                $_SESSION['modules'] = json_encode($meta_array);
            }

            $rs_level = get_MaxAccountingLevel($dbcon, $orgno);
            if ($rs_level->num_rows > 0) {
                $_SESSION['MAX_ACCL'] = $rs_level->fetch_array(MYSQLI_ASSOC)['setlabel'];
            }

            $response['error'] = false;
            $response['redirecturl'] = "dashboard.php";
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
//acc_modules(moduleno,moduletitle)
function get_info_organization($dbcon, $userno, $orgno)
{

    $sql = "SELECT uo.orgno,o.orgname,o.street, o.city, o.country, o.picurl,
                    uo.moduleno,(SELECT moduletitle FROM acc_modules WHERE moduleno=uo.moduleno) as moduletitle
            FROM com_userorgmodules as uo
                INNER JOIN com_orgs as o ON uo.orgno=o.orgno
            WHERE uo.userno=?
                AND uo.orgno=?
                AND uo.verified=1";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ii", $userno, $orgno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

//acc_accountingyear (orgno,accyear,startdate,closingdate,accyearstatus)
function is_valid_accyear($dbcon, $orgno, $accyear)
{
    $sql = "SELECT *
            FROM acc_accountingyear
            WHERE orgno=? AND accyear=? AND accyearstatus=1";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("is", $orgno, $accyear);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmt->close();
        return array('data' => $result->fetch_array(MYSQLI_ASSOC), 'error' => false);
    } else {
        $stmt->close();
        return array('error' => true);
    }
}

function get_userorgmodules($dbcon, $orgno, $userno)
{
    $sql = "SELECT moduleno,(SELECT moduletitle FROM acc_modules WHERE moduleno=um.moduleno) as moduletitle
            FROM com_userorgmodules AS um
            WHERE um.orgno=? AND um.userno=? AND verified=1";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ii", $orgno, $userno);
    $stmt->execute();

    $result = $stmt->get_result();

    $stmt->close();

    return $result;
}

//com_orgsettings(orgno,setid, setlabel, fileurl)
function get_MaxAccountingLevel($dbcon, $orgno)
{
    $sql = "SELECT setlabel
            FROM com_orgsettings
            WHERE orgno=? AND setid='ACCL'";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $orgno);
    $stmt->execute();

    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
