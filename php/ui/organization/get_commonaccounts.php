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

    if (isset($_POST['commontypeno']) && strlen($_POST['commontypeno']) > 0) {
        $commontypeno = (int) $_POST['commontypeno'];
    }else{
        throw new Exception("You must select a type!", 1);
    }

    $result = get_commonaccounts($dbcon,$commontypeno);

    $meta_array = array();
    if ($result->num_rows > 0) {
        $response['error'] = false;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
        }
        $response['data'] = $meta_array;
    } else {
        $response['error'] = true;
        $response['message'] = "No Account Found!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//ext_commonaccount(commontypeno, accno, accname, acctypeno, levelno, vtype, praccno)
function get_commonaccounts($dbcon,$commontypeno)
{
    $sql = "SELECT accno, accname,levelno, vtype, praccno
            FROM ext_commonaccount
            WHERE commontypeno=? AND levelno>1
            ORDER BY accno
            ";

    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }

    $stmt->bind_param("i", $commontypeno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

?>
