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

        $chatno=-1;
        if (isset($_POST['chatno'])) {
            $chatno = (int) $_POST['chatno'];
        }

        $parentchatno=NULL;
        if (isset($_POST['parentchatno'])) {
            $parentchatno = (int) $_POST['parentchatno'];
        }

        //$messenger,$channelno,$title,$message,$catno,$statusno,$chatflag

        if (isset($_SESSION['cogo_userno']) && strlen($_SESSION['cogo_userno'])>0) {
            $messenger = (int) $_SESSION['cogo_userno'];
        }else{
            throw new \Exception("You must login first!", 1);
        }

        if (isset($_POST['channelno']) && strlen($_POST['channelno'])>0) {
            $channelno = (int) $_POST['channelno'];
        }else{
            throw new \Exception("Target channel/project cannot be Empty!", 1);
        }

        if (isset($_POST['message']) && strlen($_POST['message'])>0) {
            $message = $_POST['message'];
        }else{
            throw new \Exception("Message Text cannot be empty!", 1);
        }

        if (isset($_POST['catno']) && strlen($_POST['catno'])>0) {
            $catno = (int) $_POST['catno'];
        }else{
            throw new \Exception("Category must be selected!", 1);
        }

        if (isset($_POST['statusno']) && strlen($_POST['statusno'])>0) {
            $statusno = (int) $_POST['statusno'];
        }else{
            throw new \Exception("Status must be selected!", 1);
        }

        $chatflag=0;
        if (isset($_POST['chatflag']) && strlen($_POST['chatflag'])>0) {
            $chatflag = (int) $_POST['chatflag'];
        }

        if($chatno>0){
            $nos=update_chat($dbcon, $messenger,$channelno,$message,$catno,$statusno,$chatflag,$chatno);
            if($nos>0){
                $response['error'] = false;
                $response['message'] = "Message Thread is Updated.";
            }else{
                $response['error'] = true;
                $response['message'] = "Cannot Update Message Thread!";
            }
        }else{
            $chatno=insert_chat($dbcon, $messenger,$channelno,$message,$catno,$statusno,$chatflag,$parentchatno);
            if($chatno>0){
                $response['error'] = false;
                $response['chatno'] = $chatno;
                if($parentchatno==NULL){
                    $response['message'] = "Message Thread is Added.";
                }else{
                    $response['message'] = "Thread is Replied.";
                }
            }else{
                $response['error'] = true;
                $response['message'] = "Cannot Add Message Thread!";
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

    //msg_chat(chatno,messenger,channelno,message,createtime,lastupdatetime,editcount,catno,statusno,chatflag)
    function insert_chat($dbcon, $messenger,$channelno,$message,$catno,$statusno,$chatflag,$parentchatno){
        date_default_timezone_set("Asia/Dhaka");
        $now = date("Y-m-d H:i:s");
        $sql = "INSERT INTO msg_chat(
                                messenger,channelno,`message`,catno,statusno,chatflag,parentchatno,createtime
                            )
                VALUES(?,?,?,?,?,?,?,?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iisiiiis",$messenger,$channelno,$message,$catno,$statusno,$chatflag,$parentchatno,$now);
        $stmt->execute();
        return $stmt->insert_id;


    }

    function update_chat($dbcon, $messenger,$channelno,$message,$catno,$statusno,$chatflag,$chatno){
        date_default_timezone_set("Asia/Dhaka");
        $now = date("Y-m-d H:i:s");
        $sql = "UPDATE msg_chat
                SET messenger=?,channelno=?,`message`=?,catno=?,statusno=?,chatflag=?,editcount=editcount+1, lastupdatetime=?
                WHERE chatno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iisiiisi", $messenger,$channelno,$message,$catno,$statusno,$chatflag,$now,$chatno);
        $stmt->execute();
        return $stmt->affected_rows;
    }
