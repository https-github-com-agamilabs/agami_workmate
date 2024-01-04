<?php
    $base_path = dirname(dirname(dirname(dirname(__FILE__))));
    include_once  $base_path."/ui/login/check_session.php";

    if($ucatno != 19){
        $response['error'] = true;
        $response['message'] = "You don't have permission to approve! Contact Admin.";
        echo json_encode($response);
        exit();
    }


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

        if (isset($_POST['backlogno'])) {
            $backlogno = (int) $_POST['backlogno'];
        }else{
            throw new \Exception("You must select the task.", 1);
        }

        $result=approve_backlog($dbcon, $backlogno);
        if($result>0){
            $response['error'] = false;
            $response['message'] = "channel-backlog (Story) is Successfully Updated.";
        }else{
            throw new \Exception("Cannot Update channel-backlog (Story).", 1);
        }

    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();

    //asp_channelbacklog(backlogno,channelno,story,storytype,prioritylevelno,relativepriority,storyphaseno,parentbacklogno,approved,accessibility,lastupdatetime,userno)
    function approve_backlog($dbcon, $backlogno){

        $sql = "UPDATE asp_channelbacklog
                SET approved=abs(1-approved)
                WHERE backlogno=?
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $backlogno);
        $stmt->execute();
        $result=$stmt->affected_rows;
        $stmt->close();
        return $result;

    }