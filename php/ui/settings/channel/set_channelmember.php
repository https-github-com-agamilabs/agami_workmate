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

        $rs_subchannels = get_subchannels($dbcon, $channelno);
        if($rs_subchannels->num_rows>0){
            $dbcon->begin_transaction();
            $count=0;
            while ($scrow = $rs_subchannels->fetch_array(MYSQLI_ASSOC)) {
                $subchannelno=$scrow['channelno'];
                if(!is_exists_channelmember($dbcon, $subchannelno,$userno)){
                    $nos=insert_channelmember($dbcon, $subchannelno, $userno);
                    if($nos>0){
                        $count++;
                    }
                }
            }
            $dbcon->commit();
            if($rs_subchannels->num_rows == $count){
                $response['error'] = false;
                $response['message'] = "Member is Added to all sub-channels/project.";
            }else if($count>0){
                $response['error'] = false;
                $response['message'] = "Member is dded to ".$count." of ".$rs_subchannels->num_rows." sub-channels/project.";
            }else{
                $response['error'] = true;
                $response['message'] = "Cannot a member to any sub-channels/project!";
            }
        }else{
            $nos=insert_channelmember($dbcon, $channelno, $userno);
            if($nos>0){
                $response['error'] = false;
                $response['message'] = "Member is Added to a Channel/Project.";
            }else{
                $response['error'] = true;
                $response['message'] = "Cannot Add Member to a Channel/Project!";
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

    //msg_channel(channelno,channeltitle,parentchannel)
    function get_subchannels($dbcon, $channelno){

        $sql = "SELECT channelno
                FROM msg_channel
                WHERE parentchannel=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $channelno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    //msg_channelmember(channelno, userno, entrytime)
    function is_exists_channelmember($dbcon, $channelno,$userno){

        $sql = "SELECT entrytime
                FROM msg_channelmember
                WHERE channelno=? AND userno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ii", $channelno,$userno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->num_rows;
    }

    function insert_channelmember($dbcon, $channelno, $userno){

        $sql = "INSERT INTO msg_channelmember(channelno, userno)
                VALUES(?,?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ii", $channelno, $userno);
        $stmt->execute();
        return $stmt->affected_rows;
    }
?>