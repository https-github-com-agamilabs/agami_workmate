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
    $rs_org = get_orgs_of_an_user($dbcon, $userno);

    if ($rs_org->num_rows > 0) {
        $meta_array = array();
        $response['error'] = false;
        while ($row = $rs_org->fetch_array(MYSQLI_ASSOC)) {
            $orgno = $row['orgno'];

            $rs_userrole = get_org_userrole($dbcon, $orgno, $userno);
            $roleArray = array();
            if ($rs_userrole->num_rows > 0) {
                while ($urow = $rs_userrole->fetch_array(MYSQLI_ASSOC)) {
                    $roleArray[] = $urow;
                }
            }
            $row['modules'] = $roleArray;

            $rs_validity = get_org_validity($dbcon, $orgno);
            if ($rs_validity->num_rows > 0) {
                $vrow = $rs_validity->fetch_array(MYSQLI_ASSOC);
                $row['pack_schemeno'] = $vrow['purchaseno'];
                $row['pack_accyear'] = $vrow['assignedto'];
                $row['pack_appliedat'] = $vrow['appliedat'];
                $row['pack_validuntil'] = $vrow['closingdate'];
            } else {
                $row['pack_schemeno'] = NULL;
                $row['pack_accyear'] = NULL;
                $row['pack_appliedat'] = NULL;
                $row['pack_validuntil'] = NULL;
            }
            // $accyear = get_current_accyear($dbcon, $orgno);
            // if ($accyear == false) {
            //     $row['accyear'] = '';
            // } else {
            //     $row['accyear'] = $accyear;
            // }
            $meta_array[] = $row;
        }
        $response['results'] = $meta_array;
    } else {
        throw new \Exception("No Organization Found! Please create organization and activate it before start accounting.", 1);
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//com_userorgmodules(orgno,userno,moduleno,verified)
//com_modules(moduleno,moduletitle)
//com_orgtype (orgtypeid,orgtypename,typetag,iconurl)
//pack_appliedpackage(itemno,orgno,schemeno,appliedat,appliedby,validuntil)
function get_orgs_of_an_user($dbcon, $userno)
{
    $sql = "SELECT o.*, (SELECT count(accno) FROM acc_orgaccounthead WHERE orgno=o.orgno AND levelno>1) as headcount
            FROM com_orgs as o
            WHERE o.orgno IN(SELECT DISTINCT orgno
                             FROM com_userorgmodules
                             WHERE userno=?)";

    // $sql = "SELECT o.*,
    //             (SELECT orgtypename FROM com_orgtype WHERE orgtypeid in (SELECT orgtypeid FROM com_orgs as org WHERE uo.orgno = org.orgno)) as orgtypename,
    //             (SELECT privacytext FROM acc_orgprivacy WHERE id=o.privacy) as privacytext
    //         FROM com_userorgmodules as uo
    //             INNER JOIN (SELECT *
    //                         FROM com_orgs) as o ON o.orgno=uo.orgno
    //         WHERE userno=?";

    $stmt = $dbcon->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $userno);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        } else {
            $stmt->close();
            return false;
        }
    } else {
        return false;
    }
}

//com_userorgmodules(orgno,userno,moduleno,verified)
//com_modules(moduleno,moduletitle)
//com_orgtype (orgtypeid,orgtypename,typetag,iconurl)
//pack_appliedpackage(itemno,orgno,schemeno,appliedat,appliedby,validuntil)
function get_org_userrole($dbcon, $orgno, $userno)
{
    $sql = "SELECT moduleno
            FROM com_userorgmodules as uo
            WHERE uo.orgno=? AND uo.userno=? AND verified=1";

    $stmt = $dbcon->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ii", $orgno, $userno);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        } else {
            $stmt->close();
            return false;
        }
    } else {
        return false;
    }
}

//acc_accountingyear (orgno,accyear,startdate,closingdate,accyearstatus)
// function get_current_accyear($dbcon, $orgno)
// {
//     $sql = "SELECT accyear
//             FROM acc_accountingyear
//             WHERE startdate<=curdate() AND closingdate>=curdate()
//                 AND orgno=? AND accyearstatus=1";

//     $stmt = $dbcon->prepare($sql);
//     if (!$stmt) {
//         echo $dbcon->error;
//     }

//     $stmt->bind_param("i", $orgno);

//     if ($stmt->execute()) {
//         $result = $stmt->get_result();
//         if ($result->num_rows > 0) {
//             $row = $result->fetch_array(MYSQLI_ASSOC);
//             $stmt->close();
//             return $row["accyear"];
//         } else {
//             $stmt->close();
//             return false;
//         }
//     } else {
//         return false;
//     }
// }

//pack_appliedpackage(appliedno,purchaseno,item,orgno,assignedto, appliedat, appliedby)
//acc_accountingyear (orgno,accyear,startdate,closingdate,accyearstatus)
function get_org_validity($dbcon, $orgno)
{
    $sql = "SELECT ap.purchaseno,ap.orgno,ap.assignedto,ap.appliedat, ap.appliedby, ay.startdate,ay.closingdate
            FROM
                (SELECT purchaseno,orgno,assignedto,appliedat, appliedby
                FROM pack_appliedpackage
                WHERE orgno=? AND item='ACCYEAR') as ap
                INNER JOIN
                (SELECT *
                FROM acc_accountingyear
                WHERE orgno=? AND accyearstatus=1 AND (CURDATE() BETWEEN startdate AND closingdate)) as ay
                ON ay.accyear=ap.assignedto
            ";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("ii", $orgno, $orgno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}
