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

    $praccno=NULL;
    if (isset($_POST['praccno']) && strlen($_POST['praccno'])>0) {
        $praccno = (int) $_POST['praccno'];
    }

    $result = get_parented_accounts($dbcon,$commontypeno,$praccno);

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
function get_parented_accounts($dbcon,$commontypeno,$praccno)
{
    if($praccno==NULL){
        $sql = "SELECT oa.*,
                    (SELECT accname as praccname FROM ext_commonaccount WHERE commontypeno=oa.commontypeno AND accno=oa.praccno) AS praccname
                FROM ext_commonaccount AS oa
                WHERE oa.commontypeno=? AND praccno IS NULL";
                $stmt = $dbcon->prepare($sql);
                $stmt->bind_param("i", $commontypeno);
    }else{
        $sql = "SELECT oa.*,
                    (SELECT accname as praccname FROM ext_commonaccount WHERE commontypeno=oa.commontypeno AND accno=oa.praccno) AS praccname
                FROM ext_commonaccount AS oa
                WHERE oa.commontypeno=? AND praccno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ii", $commontypeno,$praccno);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $stmt->close();

    return $result;
}
?>
