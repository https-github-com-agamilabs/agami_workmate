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

    // require_once($base_path."/admin/db/Database.php");
    // require_once($base_path . "/admin/operations/Select.php");

    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/php/ui/dependency_checker.php";

    $userstatusno = -1;
    if (isset($_POST['userstatusno']) && strlen($_POST['userstatusno']) > 0) {
        $userstatusno = (int)$_POST['userstatusno'];
    }

    $pageno = 1;
    if (isset($_POST['pageno'])) {
        $pageno = (int)$_POST['pageno'];
    }

    $limit = 10;
    if (isset($_POST['limit'])) {
        $limit = (int)$_POST['limit'];
    }

    //$select = new Select($dbcon);
    $result = get_filtered_users($dbcon, $userstatusno, $pageno, $limit);

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

//hr_user (userno,username,firstname,lastname,email,countrycode,contactno,passphrase,authkey,userstatusno,ucreatedatetime,reset_pass_count,updatetime)
//hr_userstatus (userstatusno,userstatustitle)
function get_filtered_users($dbcon, $userstatusno, $pageno, $limit)
{
    $start = ($pageno - 1) * $limit;

    $params = array();
    $types = "";
    $filter = " ";

    if ($userstatusno >= 0) {
        $params[] = &$userstatusno;
        $filter .= " AND userstatusno=?";
        $types .= 'i';
    }

    $types .= 'ii';
    $params[] = &$start;
    $params[] = &$limit;

    $sql = "SELECT userno,username,firstname,lastname,email,countrycode,contactno,
                userstatusno, (SELECT userstatustitle FROM hr_userstatus WHERE userstatusno=u.userstatusno) as userstatustitle,
                ucreatedatetime,reset_pass_count,updatetime
            FROM hr_user AS u
            WHERE 1 $filter
            ORDER BY ucreatedatetime DESC
            LIMIT ?, ?";

    // var_dump($sql);

    if (!$stmt = $dbcon->prepare($sql)) {
        throw new Exception("Prepare statement failed: " . $dbcon->error);
    }

    // var_dump($stmt);
    if (strlen($types) > 0) {
        call_user_func_array(array($stmt, "bind_param"), array_merge(array($types), $params));
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
?>