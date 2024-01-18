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

        if ($ucatno<13) {
            throw new \Exception("Contact admin! You are not eligible to move the task.", 1);
        }

        if (isset($_POST['backlogno'])) {
            $backlogno = (int) $_POST['backlogno'];
        }else{
            throw new \Exception("You must select a story first!", 1);
        }

        if (isset($_POST['channelno'])) {
            $channelno = (int) $_POST['channelno'];
        }else{
            throw new \Exception("Destination channel cannot be empty!", 1);
        }

        //MOVE CHANNELBACKLOG
        $result=update_backlog_channel($dbcon, $backlogno, $channelno);
        if($result>0){
            $response['error'] = false;
            $response['message'] ="Moved successfully";
        }else{
            throw new \Exception("Cannot move!", 1);
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

    //asp_channelbacklog(backlogno,channelno,story,storytype,prioritylevelno,relativepriority,storyphaseno,parentbacklogno,approved,accessibility,lastupdatetime,userno)
    function update_backlog_channel($dbcon, $backlogno, $channelno){
        $sql = "UPDATE asp_channelbacklog
                SET channelno=?
                WHERE backlogno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ii", $channelno,$backlogno);
        $stmt->execute();
        $result=$stmt->affected_rows;
        $stmt->close();

        return $result;
    }
?>