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

$base_path = dirname(dirname(dirname(__FILE__)));
require_once dirname(dirname(__FILE__)) . "/dependency_checker.php";

try {

    if (isset($_POST['orgno']) && strlen($_POST['orgno']) > 0) {
        $orgno = (int) $_POST['orgno'];
    }else{
        throw new Exception("You must select an organization!", 1);
    }
    $result = get_accountingyear($dbcon,$orgno);

    $meta_array = array();
    if ($result->num_rows > 0) {
        $response['error'] = false;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $meta_array[] = $row;
        }

        $response['results'] = $meta_array;
    } else {
        $response['error'] = true;
        $response['message'] = "No User Module Found!";
    }
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//acc_accountingyear (orgno,accyear,startdate,closingdate,accyearstatus)
//acc_modules(moduleno,moduletitle)
////acc_transaction (transno,orgno,accyear,vouchertype,receiptno,ref,tdate,confirmed,addedby,entrydatetime,narration)
function get_accountingyear($dbcon,$orgno)
{
    $sql = "SELECT accyear,startdate,closingdate,accyearstatus,
                (SELECT transno FROM acc_transaction WHERE orgno=? AND accyear=ay.accyear AND ref='B/F_01') as init_transno
            FROM acc_accountingyear AS ay
            WHERE ay.orgno=?";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ii", $orgno,$orgno);
    $stmt->execute();

    $result = $stmt->get_result();

    $stmt->close();

    return $result;
}
?>
