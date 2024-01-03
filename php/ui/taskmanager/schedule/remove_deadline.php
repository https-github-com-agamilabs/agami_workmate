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

        if (isset($_POST['dno'])) {
            $dno = (int) $_POST['dno'];
        }else{
            throw new \Exception("You must select a deadline first!", 1);
        }

        //REMOVE SCHEDULE
        $result=remove_a_deadline($dbcon, $dno);
        if($result>0){
            $response['error'] = false;
            $response['message'] ="Deadline is removed successfully";
        }else{
            throw new \Exception("Cannot remove deadline!", 1);
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

    //asp_deadlines(dno,cblscheduleno,deadline,entrytime,userno)
    function remove_a_deadline($dbcon, $dno){
        $sql = "DELETE
                FROM asp_deadlines
                WHERE dno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $dno);
        $stmt->execute();
        $result=$stmt->affected_rows;
        $stmt->close();

        return $result;
    }
?>