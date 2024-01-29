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

    if (isset($_POST['commontypeno']) && strlen($_POST['commontypeno'])>0){
        $commontypeno = (int) $_POST['commontypeno'];
    }else{
        throw new Exception("Account-type cannot be empty!", 1);
    }

    $result = get_accounts($dbcon,$commontypeno);

    $meta_array = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
        }

        $response['error'] = false;
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
function get_accounts($dbcon,$commontypeno)
{
    $sql = "SELECT c.*, p.accname as praccname
            FROM ext_commonaccount as c
                LEFT JOIN ext_commonaccount as p ON c.praccno=p.accno
            WHERE c.commontypeno=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $commontypeno);

    $stmt->execute();
    $result = $stmt->get_result();

    $stmt->close();

    return $result;
}
?>
