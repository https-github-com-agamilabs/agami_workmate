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

        if (isset($_POST['tagno'])) {
            $tagno = (int) $_POST['tagno'];
        }else{
            throw new \Exception("You must select a tag first!", 1);
        }

        //REMOVE SCHEDULE
        $result=remove_a_tag($dbcon, $tagno);
        if($result>0){
            $response['error'] = false;
            $response['message'] ="The tag is removed successfully";
        }else{
            throw new \Exception("Cannot remove the tag!", 1);
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

    //asp_tags(tagno,backlogno,tagto,tagtime,tagby)
    function remove_a_tag($dbcon, $tagno){
        $sql = "DELETE
                FROM asp_tags
                WHERE tagno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $tagno);
        $stmt->execute();
        $result=$stmt->affected_rows;
        $stmt->close();

        return $result;
    }
?>