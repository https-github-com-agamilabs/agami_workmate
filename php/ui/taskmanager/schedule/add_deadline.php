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

        //cblscheduleno,deadline
        //$userno=1;

        if (isset($_POST['cblscheduleno'])) {
            $cblscheduleno = (int) $_POST['cblscheduleno'];
        }else{
            throw new \Exception("Schedule cannot be empty!", 1);
        }

        if (isset($_POST['deadline'])) {
            $deadline = trim(strip_tags($_POST['deadline']));
        }else{
            throw new \Exception("Deadline cannot be empty!", 1);
        }

        $result=create_deadline($dbcon, $cblscheduleno,$deadline,$userno);
        if($result>0){
            $response['error'] = false;
            $response['message'] = "Deadline is Successfully Added.";
        }else{
            throw new \Exception("Cannot Add Deadline.", 1);
        }

    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();

    //asp_deadlines(dno,cblscheduleno,deadline,entrytime,userno)
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
