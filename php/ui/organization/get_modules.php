<?php
include_once  dirname(dirname(__FILE__)) . "/login/check_session.php";

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

$base_path = dirname(dirname(dirname(__FILE__)));
require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try {

    $result = get_modules($dbcon);
    $meta_array = array();
    if ($result->num_rows > 0) {
        $response['error'] = false;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
        }

        $response['results'] = $meta_array;
    } else {
        $response['error'] = true;
        $response['message'] = "No Module Found!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//com_modules(moduleno,moduletitle)
function get_modules($dbcon)
{
    $sql = "SELECT moduleno,moduletitle
            FROM com_modules";

    $stmt = $dbcon->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
