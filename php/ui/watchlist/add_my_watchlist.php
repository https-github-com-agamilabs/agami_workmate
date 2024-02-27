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

    if(!isset($_SESSION['wm_orgno'])){
        throw new \Exception("You must select an organization!", 1);
    }else{
        $orgno= (int) $_SESSION['wm_orgno'];
    }

    if (isset($_POST['backlogno'])) {
        $backlogno = (int) $_POST['backlogno'];
    } else {
        throw new \Exception("Task is not set!", 1);
    }
    $channelurl=get_channelurl($dbcon,$backlogno);
    $rs_watchlist = insert_watchlist($dbcon, $userno,$backlogno, $channelurl, $orgno);

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

 function get_channelurl($dbcon,$backlogno){
    $sql = "SELECT channelno
            FROM asp_channelbacklog
            WHERE backlogno=?";
    $stmt = $dbcon->prepare($sql);
    if ($dbcon->error) {
        echo $dbcon->error;
    }
    $stmt->bind_param("i", $backlogno);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $channelno=NULL;
    if($result->num_rows>0){
        $channelno=$result->fetch_array(MYSQLI_ASSOC)['channelno'];
        $channelurl="https://workmate.agamilab.com/story.php?channelno=".$channelno;
    }else{
        $channelurl=NULL;
    }
    $stmt->close();

    return $channelurl;
}


// asp_watchlist(userno,backlogno,channelurl,createtime)
function insert_watchlist($dbcon, $userno,$backlogno, $channelurl, $orgno)
{
    date_default_timezone_set("Asia/Dhaka");
    $createtime = date('Y-m-d H:i:s');

    $sql = "INSERT INTO asp_watchlist(orgno,userno,backlogno,channelurl,createtime)
            VALUES(?,?,?,?,?)";
    $stmt = $dbcon->prepare($sql);
    if ($dbcon->error) {
        echo $dbcon->error;
    }
    $stmt->bind_param("iiiss", $orgno,$userno,$backlogno, $channelurl, $createtime);
    $stmt->execute();
    return $stmt->affected_rows;
}
