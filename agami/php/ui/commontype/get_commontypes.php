<?php
    include_once  dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/php/ui/login/check_session.php";

    $response = array();
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        $response['error'] = true;
        $response['message'] = "Invalid Request method";
        echo json_encode($response);
        exit();
    }

    $base_path = dirname(dirname(dirname(__FILE__)));
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/php/ui/dependency_checker.php";

try {

    $result = get_commontype($dbcon);

    $meta_array = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
        }

        $response['error'] = false;
        $response['data'] = $meta_array;
    } else {
        $response['error'] = true;
        $response['message'] = "No Account-type Found!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//ext_commontype (commontypeno, commontypetitle, maxacclevel)
function get_commontype($dbcon)
{
    $sql = "SELECT commontypeno, commontypetitle, maxacclevel
            FROM ext_commontype
            ORDER BY commontypetitle";
    $stmt = $dbcon->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
?>
