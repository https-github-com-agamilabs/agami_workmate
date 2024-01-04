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

        //$backlogno, $channelno, $story, $points, $priorityno, $relativepriority, $storyphaseno, $userno
        $backlogno=-1;
        if (isset($_POST['backlogno'])) {
            $backlogno = (int) $_POST['backlogno'];
        }

        if (isset($_POST['channelno'])) {
            $channelno = (int) $_POST['channelno'];
        }else{
            throw new \Exception("channel cannot be empty!", 1);
        }

        if (isset($_POST['story'])) {
            $story = trim(strip_tags($_POST['story']));
        }else{
            throw new \Exception("channel-backlog (story) cannot be empty!", 1);
        }

        $points=10;
        if (isset($_POST['points'])) {
            $points = (int) $_POST['points'];
        }

        $prioritylevelno=1;
        if (isset($_POST['prioritylevelno'])) {
            $prioritylevelno = (int) $_POST['prioritylevelno'];
        }

        $relativepriority=99;
        if (isset($_POST['relativepriority'])) {
            $relativepriority = (int) $_POST['relativepriority'];
        }

        $storyphaseno=1;
        if (isset($_POST['storyphaseno'])) {
            $storyphaseno = (int) $_POST['storyphaseno'];
        }

        $parentbacklogno=NULL;
        if (isset($_POST['parentbacklogno'])) {
            $parentbacklogno = (int) $_POST['parentbacklogno'];
        }

        //admin == 19
        if($ucatno == 19)
            $approved = 1;
        else
            $approved = 0;

        if($backlogno>0){
            $result=update_channel($dbcon, $backlogno, $channelno, $story, $points, $prioritylevelno, $relativepriority, $storyphaseno, $userno);
            if($result>0){
                $response['error'] = false;
                $response['message'] = "channel-backlog (Story) is Successfully Updated.";
            }else{
                throw new \Exception("Cannot Update channel-backlog (Story).", 1);
            }
        }else{
            $result=create_channelbacklog($dbcon, $channelno, $story, $points, $prioritylevelno, $relativepriority, $storyphaseno, $parentbacklogno,$approved,$userno);
            if($result>0){
                $response['error'] = false;
                $response['message'] = "channel-backlog (story) is Successfully Added.";
            }else{
                throw new \Exception("Cannot Add channel-backlog (Story).", 1);
            }
        }

    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();

    //asp_channelbacklog(backlogno,channelno,story,storytype,prioritylevelno,relativepriority,storyphaseno,parentbacklogno,approved,accessibility,lastupdatetime,userno)
    function create_channelbacklog($dbcon, $channelno, $story, $storytype, $prioritylevelno, $relativepriority, $storyphaseno, $parentbacklogno,$approved,$userno){

        $sql = "INSERT INTO asp_channelbacklog(channelno, story, storytype, prioritylevelno, relativepriority, storyphaseno,  parentbacklogno, approved,userno)
                VALUES(?,?,?,?,?,?,?,?,?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("isiiiiiii", $channelno, $story, $storytype, $prioritylevelno, $relativepriority, $storyphaseno, $parentbacklogno, $approved, $userno);
        $stmt->execute();
        $result=$stmt->insert_id;
        $stmt->close();
        return $result;
    }

    function update_channel($dbcon, $backlogno, $channelno, $story, $storytype, $prioritylevelno, $relativepriority, $storyphaseno, $userno){

        $sql = "UPDATE asp_channelbacklog
                SET channelno=?, story=?, storytype=?, prioritylevelno=?, relativepriority=?, storyphaseno=?, userno=?
                WHERE backlogno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("isiiiiii", $channelno, $story, $storytype, $prioritylevelno, $relativepriority, $storyphaseno, $userno,$backlogno);
        $stmt->execute();
        $result=$stmt->affected_rows;
        $stmt->close();
        return $result;

    }