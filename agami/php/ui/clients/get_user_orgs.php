<?php
include_once  dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/php/ui/login/check_session.php";

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

try {
    $base_path = dirname(dirname(dirname(__FILE__)));

    // require_once($base_path."/admin/db/Database.php");
    // require_once($base_path . "/admin/operations/Select.php");

    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/php/ui/dependency_checker.php";

    if (isset($_POST['userno']) && strlen($_POST['userno']) > 0) {
        $userno = (int)$_POST['userno'];
    } else {
        throw new \Exception("You must select a user!", 1);
    }

    //$select = new Select($dbcon);
    $result = get_user_orgs($dbcon, $userno);

    $meta_array = array();
    if ($result->num_rows > 0) {
        $response['error'] = false;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
        }

        $response['data'] = $meta_array;
    } else {
        $response['error'] = true;
        $response['message'] = "User organization not found!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//acc_userorgmodules(userno,orgno,moduleno,verified)
//com_orgs (orgno, orgname, street, city, state, country, gpslat, gpslon, orgtypeid, privacy, picurl, contactno, orgnote, weekend1, weekend2, starttime, endtime, verifiedno)
//acc_modules(moduleno,moduletitle)
function get_user_orgs($dbcon, $userno)
{
    $sql = "SELECT om.orgno, orgname, street, city, `state`, country, picurl, contactno, orgnote,o.verifiedno as company_verified,
                    moduleno, (SELECT moduletitle FROM acc_modules WHERE moduleno=om.moduleno) as moduletitle,
                    verified as module_verified
            FROM acc_userorgmodules AS om
                INNER JOIN com_orgs as o ON om.orgno=o.orgno
            WHERE om.userno=?";

    // var_dump($sql);

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: " . $dbcon->error);
    }

    $stmt->bind_param("i", $userno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
