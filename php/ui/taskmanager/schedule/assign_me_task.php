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

        //cblscheduleno,backlogno,howto,takentime,scheduledate,userno
        //$userno=1;

        if (isset($_POST['backlogno'])) {
            $backlogno = (int) $_POST['backlogno'];
        }else{
            throw new \Exception("Backlog cannot be empty!", 1);
        }

        $howto = NULL;
        if (isset($_POST['howto'])) {
            $howto = trim($_POST['howto']);
        }

        //SELF
        $assignedto = $userno;

        if (isset($_POST['scheduledate'])) {
            $scheduledate = trim(strip_tags($_POST['scheduledate']));
        }else{
            if($cblscheduleno<0)
                throw new \Exception("Deadline cannot be empty!", 1);
        }

        $duration = 1.0;
        if (isset($_POST['duration'])) {
            $duration = (double) $_POST['duration'];
        }

        $dbcon->begin_transaction();
        $result=create_schedule($dbcon, $backlogno,$howto,$assignedto,$scheduledate,$duration,$userno);
        if($result>0){
            $deadline=date('Y-m-d', strtotime($scheduledate. ' + '.($duration-1).' days'));
            $dno=create_deadline($dbcon, $result,$deadline,$userno);
            if($dno<=0){
                $dbcon->rollback();
                throw new \Exception("Deadline Error! Cannot Update Schedule.", 1);
            }
            $response['error'] = false;
            $response['message'] = "Schedule is Successfully Added.";
        }else{
            $dbcon->rollback();
            throw new \Exception("Cannot Add Schedule.", 1);
        }
        $dbcon->commit();

    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();

    //asp_cblschedule(cblscheduleno,backlogno,howto,assignedto, assigntime,scheduledate,userno)
    function create_schedule($dbcon, $backlogno,$howto,$assignedto,$scheduledate,$duration,$userno){
        date_default_timezone_set("Asia/Dhaka");
        $assigntime = date("Y-m-d H:i:s");

        $sql = "INSERT INTO asp_cblschedule(backlogno,howto,assignedto,assigntime,scheduledate,duration,userno)
                VALUES(?,?,?,?,?,?,?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("isissdi", $backlogno,$howto,$assignedto,
                                    $assigntime,$scheduledate,$duration,$userno);
        $stmt->execute();
        $result=$stmt->insert_id;
        $stmt->close();
        return $result;
    }

    function create_deadline($dbcon, $cblscheduleno,$deadline,$userno){
        date_default_timezone_set("Asia/Dhaka");
        $entrytime = date("Y-m-d H:i:s");

        $sql = "INSERT INTO asp_deadlines(cblscheduleno,deadline,entrytime,userno)
                VALUES(?,?,?,?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("issi", $cblscheduleno,$deadline,$entrytime,$userno);
        $stmt->execute();
        $result=$stmt->insert_id;
        $stmt->close();
        return $result;
    }

    function delete_deadline($dbcon, $cblscheduleno){
        $sql = "DELETE
                FROM asp_deadlines
                WHERE cblscheduleno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $cblscheduleno);
        $stmt->execute();
        $result=$stmt->affected_rows;
        $stmt->close();
        return $result;
    }

