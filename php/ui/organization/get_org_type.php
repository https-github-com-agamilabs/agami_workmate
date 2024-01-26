<?php
include_once  dirname(dirname(__FILE__)) . "/session/check_user_session.php";
?>
<?php
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
    require_once($base_path . "/admin/operations/Select.php");

    // $db = new Database();
    // $dbcon=$db->db_connect();
    // if(!$db->is_connected()){
    //     $response['error'] = true;
    //     $response['message'] = "Database is not connected!";
    //     echo json_encode($response);
    // 	exit();
    // }
    require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";


    $select = new Select($dbcon);
    $result = $select->get_org_types();

    $meta_array = array();
    if ($result->num_rows > 0) {
        $response['error'] = false;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
        }

        $response['data'] = $meta_array;
    } else {
        $response['error'] = true;
        $response['message'] = "Null results!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

$dbcon->close();
?>
