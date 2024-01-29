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

    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/php/ui/dependency_checker.php";

    $result = get_userstatus($dbcon);

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

//hr_userstatus (userstatusno,userstatustitle)
function get_userstatus($dbcon)
{
    $sql = "SELECT userstatusno,userstatustitle
            FROM hr_userstatus
            ORDER BY userstatusno";

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: ".$dbcon->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
    }
?>
