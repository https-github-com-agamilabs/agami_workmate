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

        if(!isset($_SESSION['wm_orgno'])){
            throw new \Exception("You must select an organization!", 1);
        }else{
            $orgno= (int) $_SESSION['wm_orgno'];
        }

        if (isset($_POST['channelno'])) {
            $channelno = (int) $_POST['channelno'];
        } else{
            throw new \Exception("No Channel/Project Selected!", 1);
        }

        $channel = del_channel($dbcon,$channelno,$orgno);

        if ($channel > 0) {
            $response['error'] = false;
            $response['message'] = "Channel/Project info is Removed.";
        } else {
            $response['error'] = true;
            $response['message'] = "Cannot Delete Channel/Project Info!";
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
    function del_channel($dbcon,$channelno,$orgno){
        $sql = "DELETE
                FROM msg_channel
                WHERE channelno=? AND orgno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ii", $channelno,$orgno);
        $stmt->execute();
        return $stmt->affected_rows;
    }
