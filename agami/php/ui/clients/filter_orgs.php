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


    $orgtypeid = -1;
    if (isset($_POST['orgtypeid']) && strlen($_POST['orgtypeid']) > 0) {
        $orgtypeid = (int)$_POST['orgtypeid'];
    }

    $verifiedno = -9;
    if (isset($_POST['verifiedno']) && strlen($_POST['verifiedno']) > 0) {
        $verifiedno = (int)$_POST['verifiedno'];
    }

    $city = '';
    if (isset($_POST['city']) && strlen($_POST['city']) > 0) {
        $city = trim(strip_tags($_POST['city']));
    }

    //$select = new Select($dbcon);
    $result = get_filtered_org($dbcon, $orgtypeid, $verifiedno, $city);

    $meta_array = array();
    if ($result->num_rows > 0) {
        $response['error'] = false;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
        }

        $response['data'] = $meta_array;
    } else {
        $response['error'] = true;
        $response['message'] = "No organization found!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//com_orgs (orgno, orgname, street, city, state, country, gpslat, gpslon, orgtypeid, privacy, picurl, contactno, orgnote, weekend1, weekend2, starttime, endtime, verifiedno)
//com_orgtype (orgtypeid,orgtypename,typetag,iconurl)
function get_filtered_org($dbcon, $orgtypeid, $verifiedno, $city)
{
    $params = array();
    $types = "";
    $filter = " ";

    if ($orgtypeid > 0) {
        $params[] = &$orgtypeid;
        $filter .= " AND o.orgtypeid=?";
        $types .= 'i';
    }

    if ($verifiedno > -2) {
        $params[] = &$verifiedno;
        $filter .= " AND verifiedno=?";
        $types .= 'i';
    }

    if (strlen($city) > 0) {
        $city = '%' . $city . '%';
        $params[] = &$city;
        $filter .= " AND city LIKE ?";
        $types .= 's';
    }

    $sql = "SELECT orgno, orgname,
                        city, country,
                        o.orgtypeid, orgtypename,
                        iconurl, picurl,
                        contactno, verifiedno
                FROM com_orgs AS o
                    INNER JOIN com_orgtype as t ON o.orgtypeid=t.orgtypeid
                WHERE 1 $filter
                ORDER BY verifiedno,orgno";

    // var_dump($sql);

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: " . $dbcon->error);
    }

    // var_dump($stmt);
    if (strlen($types) > 0) {
        call_user_func_array(array($stmt, "bind_param"), array_merge(array($types), $params));
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
