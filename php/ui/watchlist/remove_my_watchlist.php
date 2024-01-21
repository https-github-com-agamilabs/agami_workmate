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

    if (isset($_POST['backlogno'])) {
        $backlogno = (int) $_POST['backlogno'];
    } else {
        throw new \Exception("Task is not set!", 1);
    }
    
    
    $rs_watchlist = remove_watchlist($dbcon, $userno,$backlogno);

    if ($rs_watchlist->num_rows > 0) {
        $response['error'] = false;
        $response['message'] = "Removed Successfully.";
    }else{
        throw new \Exception("Could not remove from watch-list!");
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
function remove_watchlist($dbcon, $userno,$backlogno)
{
    $sql = "DELETE 
            FROM asp_watchlist
            WHERE userno=? AND backlogno=?";
    $stmt = $dbcon->prepare($sql);
    if ($dbcon->error) {
        echo $dbcon->error;
    }
    $stmt->bind_param("ii", $userno,$backlogno);
    $stmt->execute();
    return $stmt->affected_rows;
}
