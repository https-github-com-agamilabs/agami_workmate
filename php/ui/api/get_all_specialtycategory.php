<?php
$base_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
//include_once($base_path . "/php/ui/login/check_session.php");

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method!";
    echo json_encode($response);
    exit();
}

require_once($base_path . "/php/db/Database.php");
$db = new Database();
$dbcon = $db->db_connect();
if (!$db->is_connected()) {
    $response['error'] = true;
    $response['message'] = "Database is not connected!";
    echo json_encode($response);
    exit();
}

$resultArray = array();
$result = get_specialtycategory($dbcon);
if($result->num_rows>0){
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $resultArray[] = $row;
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
function get_specialtycategory($dbcon)
{
    $sql = "SELECT  spno,specialty,parentspno
            FROM drrx_specialtycategory
            ";

    $stmt = $dbcon->prepare($sql);
    //$stmt->bind_param("i", $medhistno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
