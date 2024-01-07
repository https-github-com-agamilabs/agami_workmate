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

        if (isset($_POST['backlogno'])) {
            $backlogno = (int) $_POST['backlogno'];
        }else{
            throw new \Exception("You must select a story first!", 1);
        }

        //REMOVE CHANNELBACKLOG
        $result=remove_a_backlog($dbcon, $backlogno);
        if($result>0){
            $response['error'] = false;
            $response['message'] ="Removed successfully";
        }else{
            throw new \Exception("Cannot Remove!", 1);
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
    function remove_a_backlog($dbcon, $backlogno){
        $sql = "DELETE
                FROM asp_channelbacklog
                WHERE backlogno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $backlogno);
        $stmt->execute();
        $result=$stmt->affected_rows;
        $stmt->close();

        return $result;
    }
?>