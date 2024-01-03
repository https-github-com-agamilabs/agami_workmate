<?php
    include_once  dirname(dirname(dirname(__FILE__)))."/login/check_session.php";

    $response = array();
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        $response['error'] = true;
        $response['message'] = "Invalid Request method";
        echo json_encode($response);
        exit();
    }

    try {

        $base_path = dirname(dirname(dirname(dirname(__FILE__))));
        require_once($base_path."/db/Database.php");

        $db = new Database();
        $dbcon=$db->db_connect();
        if (!$db->is_connected()) {
            throw new \Exception("Database is not connected!", 1);
        }

        if (isset($_POST['channelno'])) {
            $channelno = (int) $_POST['channelno'];
        } else{
            throw new \Exception("No Channel/Project Selected!", 1);
        }

        if (isset($_POST['userno'])) {
            $userno = (int) $_POST['userno'];
        } else{
            throw new \Exception("No Member Selected!", 1);
        }

        $channel = del_channelmember($dbcon,$channelno,$userno);

        if ($channel > 0) {
            $response['error'] = false;
            $response['message'] = "Member from a Channel/Project info is Removed.";
        } else {
            $response['error'] = true;
            $response['message'] = "Cannot Remove Member from a Channel/Project!";
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
    function del_channelmember($dbcon,$channelno,$userno){
        $sql = "DELETE
                FROM msg_channelmember
                WHERE channelno=? AND userno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ii", $channelno,$userno);
        $stmt->execute();
        return $stmt->affected_rows;
    }
