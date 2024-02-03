<?php
$base_path = dirname(dirname(dirname(__FILE__)));
include_once  $base_path."/ui/login/check_session.php";

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

try {

    require_once($base_path . "/db/Database.php");

    $db = new Database();
    $dbcon = $db->db_connect();
    if (!$db->is_connected()) {
        throw new \Exception("Database is not connected!", 1);
    }

    if(!isset($_SESSION['cogo_orgno'])){
        throw new \Exception("You must select an organization!", 1);
    }else{
        $orgno= (int) $_SESSION['cogo_orgno'];
    }

    if (isset($_POST['backlogno'])) {
        $backlogno = (int) $_POST['backlogno'];
    } else {
        throw new \Exception("Task is not set!", 1);
    }
    
    
    $rs_watchlist = insert_watchlist($dbcon, $userno,$backlogno, $orgno);

    if ($rs_watchlist > 0) {
        $response['error'] = false;
        $response['message'] = "Added Successfully.";
    }else{
        throw new \Exception("Could not add to watch-list!");
    }
    
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
if (isset($dbcon)) {
    $dbcon->close();
}

/**
 * Local Function
 */


// asp_watchlist(userno,backlogno,createtime)
function insert_watchlist($dbcon, $userno,$backlogno, $orgno)
{
    date_default_timezone_set("Asia/Dhaka");
    $createtime = date('Y-m-d H:i:s');

    $sql = "INSERT INTO asp_watchlist(orgno,userno,backlogno,createtime)
            VALUES(?,?,?,?)";
    $stmt = $dbcon->prepare($sql);
    if ($dbcon->error) {
        echo $dbcon->error;
    }
    $stmt->bind_param("iiis", $orgno,$userno,$backlogno, $createtime);
    $stmt->execute();
    return $stmt->affected_rows;
}
