<?php
include_once  dirname(dirname(__FILE__)) . "/session/check_user_session.php";

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

    $result = get_commontype($dbcon);

    $meta_array = array();
    if ($result->num_rows > 0) {
        $response['error'] = false;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
        }
        $response['data'] = $meta_array;
    } else {
        $response['error'] = true;
        $response['message'] = "No Type Found!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//ext_commontype (commontypeno, commontypetitle)
function get_commontype($dbcon)
{
    $sql = "SELECT commontypeno, commontypetitle
            FROM ext_commontype
            ";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

?>
