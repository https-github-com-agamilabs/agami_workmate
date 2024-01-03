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

        if (isset($_POST['cblscheduleno'])) {
            $cblscheduleno = (int) $_POST['cblscheduleno'];
        }else{
            throw new \Exception("You must select a schedule first!", 1);
        }

        //REMOVE SCHEDULE
        $result=remove_a_schedule($dbcon, $cblscheduleno);
        if($result>0){
            $response['error'] = false;
            $response['message'] ="Schedule is removed successfully";
        }else{
            throw new \Exception("Cannot remove schedule!", 1);
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

    //asp_cblschedule(cblscheduleno,backlogno,howto,assignedto, assigntime,scheduledate,userno)
    function remove_a_schedule($dbcon, $cblscheduleno){
        $sql = "DELETE
                FROM asp_cblschedule
                WHERE cblscheduleno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $cblscheduleno);
        $stmt->execute();
        $result=$stmt->affected_rows;
        $stmt->close();

        return $result;
    }
?>