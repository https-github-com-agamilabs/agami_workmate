<?php
    $base_path = dirname(dirname(dirname(dirname(__FILE__))));
    include_once  $base_path."/ui/login/check_session.php";

    $response = array();
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        $response['error'] = true;
        $response['message'] = "Invalid Request method";
        echo json_encode($response);
        exit();
    }

    require_once($base_path."/db/Database.php");
    $db = new Database();
    $dbcon=$db->db_connect();
    if (!$db->is_connected()) {
        $response['error'] = true;
        $response['message'] = "Database is not connected!";
        echo json_encode($response);
        exit();
    }

    try {

        if(!isset($_SESSION['wm_orgno'])){
            throw new \Exception("You must select an organization!", 1);
        }else{
            $orgno= (int) $_SESSION['wm_orgno'];
        }

        if (isset($_POST['backlogno'])) {
            $backlogno = (int) $_POST['backlogno'];
        }else{
            throw new \Exception("Story must be selected!", 1);
        }

        if (isset($_POST['tagto'])) {
            $tagto = (int) $_POST['tagto'];
        }else{
            throw new \Exception("Tag-to-whom cannot be empty!", 1);
        }

        $channelno=NULL;
        if (isset($_POST['channelno'])) {
            $channelno = (int) $_POST['channelno'];
        }

        $dbcon->begin_transaction();
        $channelurl=get_channelurl($dbcon,$backlogno);
        $result=add_tag($dbcon, $backlogno,$tagto,$channelurl,$userno);
        if($result>0){

            $wno=insert_watchlist($dbcon, $tagto,$backlogno, $channelurl, $orgno);

            if($dbcon->commit()){
                $response['error'] = false;
                $response['message'] = "Tag is Successfully Added.";
            }
        }else{
            $dbcon->rollback();
            throw new \Exception("Cannot add tag.", 1);
        }

    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();

    //asp_tags(tagno,backlogno,tagto,tagtime,tagby)
    function add_tag($dbcon, $backlogno,$tagto,$channelurl,$tagby){
        date_default_timezone_set("Asia/Dhaka");
        $tagtime = date("Y-m-d H:i:s");

        $sql = "INSERT INTO asp_tags(backlogno,tagto,channelurl,tagtime,tagby)
                VALUES(?,?,?,?,?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iissi", $backlogno,$tagto,$channelurl,$tagtime,$tagby);
        $stmt->execute();
        $result=$stmt->insert_id;
        $stmt->close();
        return $result;
    }

    // asp_watchlist(userno,backlogno,createtime)
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


