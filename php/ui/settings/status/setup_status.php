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

        $statusno=-1;
        if (isset($_POST['statusno'])) {
            $statusno = (int) $_POST['statusno'];
        }

        if (isset($_POST['statustitle']) && strlen($_POST['statustitle'])>0) {
            $statustitle = trim(strip_tags($_POST['statustitle']));
        }else{
            throw new \Exception("Status Title cannot be Empty!", 1);
        }

        if($statusno>0){
            $nos=update_status($dbcon, $statustitle, $statusno);
            if($nos>0){
                $response['error'] = false;
                $response['message'] = "Status info is Updated.";
            }else{
                $response['error'] = true;
                $response['message'] = "Cannot Update Status info.";
            }
        }else{
            $statusno=insert_status($dbcon, $cattitle);
            if($statusno>0){
                $response['error'] = false;
                $response['message'] = "Status info is Added.";
            }else{
                $response['error'] = true;
                $response['message'] = "Cannot Add Status info.";
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

    function insert_status($dbcon, $statustitle){

        $sql = "INSERT INTO msg_status(statustitle)
                VALUES(?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("s", $statustitle);
        $stmt->execute();
        return $stmt->insert_id;


    }

    function update_status($dbcon, $statustitle, $statusno){
        $sql = "UPDATE msg_status
                SET statustitle=?
                WHERE statusno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("si", $statustitle, $statusno);
        $stmt->execute();
        return $stmt->affected_rows;
    }
