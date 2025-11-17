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

        $channelno=-1;
        if (isset($_POST['channelno'])) {
            $channelno = (int) $_POST['channelno'];
        }

        if(!isset($_SESSION['wm_orgno'])){
            throw new \Exception("You must select an organization!", 1);
        }else{
            $orgno= (int) $_SESSION['wm_orgno'];
        }

        if (isset($_POST['channeltitle']) && strlen($_POST['channeltitle'])>0) {
            $channeltitle = trim(strip_tags($_POST['channeltitle']));
        }else{
            throw new \Exception("Channel/Project Title cannot be Empty!", 1);
        }

        $parentchannel=NULL;
        if (isset($_POST['parentchannel']) && strlen($_POST['parentchannel'])>0) {
            $parentchannel = (int) $_POST['parentchannel'];
        }

        $isactive=0;
        if (isset($_POST['isactive']) && strlen($_POST['isactive'])>0) {
            $isactive = (int) $_POST['isactive'];
        }

        if($channelno>0){
            $nos=update_channel($dbcon, $channeltitle, $isactive, $parentchannel, $channelno,$orgno);
            if($nos>0){
                $response['error'] = false;
                $response['message'] = "Channel/Project info is Updated.";
            }else{
                $response['error'] = true;
                $response['message'] = "Cannot Update channel/Project info.";
            }
        }else{
            $channelno=insert_channel($dbcon, $channeltitle, $isactive, $parentchannel,$orgno);
            if($channelno>0){
                $response['error'] = false;
                $response['channelno']=$channelno;
                $response['message'] = "Channel/Project info is Added.";
            }else{
                $response['error'] = true;
                $response['message'] = "Cannot Add Channel/Project info.";
            }
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

    function insert_channel($dbcon, $channeltitle, $isactive, $parentchannel,$orgno){

        $sql = "INSERT INTO msg_channel(channeltitle, isactive, parentchannel,orgno)
                VALUES(?,?,?,?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("siii", $channeltitle, $isactive, $parentchannel,$orgno);
        $stmt->execute();
        return $stmt->insert_id;


    }

    function update_channel($dbcon, $channeltitle, $isactive, $parentchannel, $channelno,$orgno){
        $sql = "UPDATE msg_channel
                SET channeltitle=?, isactive=?, parentchannel=?
                WHERE channelno=? AND orgno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("sisii", $channeltitle, $isactive, $parentchannel, $channelno,$orgno);
        $stmt->execute();
        return $stmt->affected_rows;
    }
