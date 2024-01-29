<?php
$base_path = dirname(dirname(dirname(__FILE__)));
//include_once($base_path."/ui/login/check_session.php");

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method!";
    echo json_encode($response);
    exit();
}

require_once($base_path . "/db/Database.php");
$db = new Database();
$dbcon = $db->db_connect();
if (!$db->is_connected()) {
    $response['error'] = true;
    $response['message'] = "Database is not connected!";
    echo json_encode($response);
    exit();
}

$spno = -1;
if (isset($_POST['spno']) && strlen($_POST['spno']) > 0) {
    $spno = (int) $_POST['spno'];
}

$search_key = "";
if (isset($_POST['search_key']) && strlen($_POST['search_key']) > 0) {
    $search_key = trim(strip_tags($_POST['search_key']));
}

$pageno = 1;
if (isset($_POST['pageno']) && strlen($_POST['pageno']) > 0) {
    $pageno = (int) $_POST['pageno'];
}

$limit = 10;
if (isset($_POST['limit']) && strlen($_POST['limit']) > 0) {
    $limit = (int) $_POST['limit'];
}

if($spno>0){
    $result = get_doctor_info_by_specialty($dbcon, $spno, $search_key, $pageno, $limit);
}else{
    $result = get_doctor_info($dbcon, $search_key, $pageno, $limit);
}

if($result->num_rows>0){
    $resultArray = array();
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $resultArray[] = $row;
    }
    if (count($resultArray) >= $limit) {
        $response['pagination']['more'] = true;
    } else {
        $response['pagination']['more'] = false;
    }
    $response['error'] = false;
    $response['results'] = $resultArray;
}else{
    $response['error'] = true;
    $response['message'] = "No Data found!";
}

echo json_encode($response);
$dbcon->close();

//drrx_specialtycategory (spno,specialty,parentspno)
function get_doctor_info($dbcon, $search_key, $pageno, $limit)
{
    $start_index = ($pageno - 1) * $limit;
    $keys=explode(" ",$search_key);
    $search=implode("%",$keys);;
    $search = '%' . $search_key . '%';

    $sql = "SELECT  d.doctno,p.firstname, p.lastname,p.countrycode,p.primarycontact,
                    d.spno, c.specialty
            FROM drrx_doctorinfo as d
                INNER JOIN drrx_specialtycategory as c ON c.spno=d.spno
                INNER JOIN gen_peopleprimary as p ON d.peopleno=p.peopleno
            WHERE CONCAT(p.firstname,' ', IFNULL(p.lastname,''),' ',c.specialty) LIKE ?
            LIMIT ?,?";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("sii", $search, $start_index, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

function get_doctor_info_by_specialty($dbcon, $spno, $search_key, $pageno, $limit)
{
    $start_index = ($pageno - 1) * $limit;
    $keys=explode(" ",$search_key);
    $search=implode("%",$keys);;
    $search = '%' . $search_key . '%';

    $sql = "SELECT  d.doctno,p.firstname, p.lastname,p.countrycode,p.primarycontact,
                    d.spno, c.specialty
            FROM drrx_doctorinfo as d
                INNER JOIN drrx_specialtycategory as c ON c.spno=d.spno
                INNER JOIN gen_peopleprimary as p ON d.peopleno=p.peopleno
            WHERE d.spno=? AND CONCAT(p.firstname,' ', IFNULL(p.lastname,''),' ',c.specialty) LIKE ?
            LIMIT ?,?";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("isii", $spno,$search, $start_index, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

