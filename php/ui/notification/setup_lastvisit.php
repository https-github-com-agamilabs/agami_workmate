<?php
    include_once  dirname(dirname(__FILE__))."/login/check_session.php";

    $response = array();
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        $response['error'] = true;
        $response['message'] = "Invalid Request method";
        echo json_encode($response);
        exit();
    }

    try {

        $base_path = dirname(dirname(dirname(__FILE__)));
        require_once($base_path."/db/Database.php");

        $db = new Database();
        $dbcon=$db->db_connect();
        if (!$db->is_connected()) {
            throw new \Exception("Database is not connected!", 1);
        }

        if (isset($_POST['channelno']) && strlen($_POST['channelno'])>0) {
            $channelno = (int) $_POST['channelno'];
        } else{
            throw new \Exception("You must select a channel!", 1);
        }

        if(isset($_SESSION['wm_userno'])){
            $userno=(int) $_SESSION['wm_userno'];
        }else{
            throw new \Exception("You must login first!", 1);
        }

        $nos = setup_lastvisit($dbcon,$userno,$channelno);
        if($nos>0){
            $response['error'] = false;
            $response['message'] = "Updated Successfully.";
        }else{
            throw new \Exception("Failed! Could not Update Time.", 1);
        }
    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();

    /**
     * Local Function
     */

    //msg_lastvisit(userno,channelno,lastvisittime)
    function setup_lastvisit($dbcon,$userno,$channelno){
        date_default_timezone_set("Asia/Dhaka");
        $now = date("Y-m-d H:i:s");

        $sql = "INSERT INTO msg_lastvisit(userno,channelno,lastvisittime)
                VALUES (?,?,?)
                ON DUPLICATE KEY UPDATE lastvisittime=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iiss", $userno,$channelno,$now,$now);
        $stmt->execute();
        return $stmt->affected_rows;
    }
