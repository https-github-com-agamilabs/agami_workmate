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
            throw new \Exception("Story must be selected!", 1);
        }

        if (isset($_POST['tagto'])) {
            $tagto = (int) $_POST['tagto'];
        }else{
            throw new \Exception("Tag-to-whom cannot be empty!", 1);
        }

        $result=add_tag($dbcon, $backlogno,$tagto,$userno);
        if($result>0){
            $response['error'] = false;
            $response['message'] = "Tag is Successfully Added.";
        }else{
            throw new \Exception("Cannot add tag.", 1);
        }

    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();

    //asp_tags(tagno,backlogno,tagto,tagtime,tagby)
    function add_tag($dbcon, $backlogno,$tagto,$tagby){
        date_default_timezone_set("Asia/Dhaka");
        $tagtime = date("Y-m-d H:i:s");

        $sql = "INSERT INTO asp_tags(backlogno,tagto,tagtime,tagby)
                VALUES(?,?,?,?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iisi", $backlogno,$tagto,$tagtime,$tagby);
        $stmt->execute();
        $result=$stmt->insert_id;
        $stmt->close();
        return $result;
    }
