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

    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int)$_POST['orgno'];
    } else {
        throw new \Exception("You must select an organization!", 1);
    }

    //$select = new Select($dbcon);
    $result = get_an_org($dbcon, $orgno);

    $meta_array = array();
    if ($result->num_rows > 0) {
        $response['error'] = false;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $meta_array = $row;
        }

        $response['data'] = $meta_array;
    } else {
        $response['error'] = true;
        $response['message'] = "Otganization Not Found!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//com_orgs (orgno, orgname, street, city, state, country, gpslat, gpslon, orgtypeid, privacy, picurl, primarycontact, orgnote, weekend1, weekend2, starttime, endtime, verifiedno)
function get_an_org($dbcon, $orgno)
{
    $sql = "SELECT orgno, orgname,
                    street, city, `state`, country,
                    gpslat, gpslon,
                    o.orgtypeid, orgtypename,
                    privacy, orgnote,
                    weekend1, weekend2, starttime, endtime,
                    iconurl, picurl,
                    primarycontact, verifiedno
            FROM com_orgs AS o
            INNER JOIN com_orgtype as t ON o.orgtypeid=t.orgtypeid
            WHERE orgno=?";

    // var_dump($sql);

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: " . $dbcon->error);
    }

    $stmt->bind_param("i", $orgno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
