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

        if (isset($_SESSION['cogo_userno'])) {
            $empno=(int) $_SESSION['cogo_userno'];
        } else {
            throw new \Exception("You must login first!", 1);
        }

        $elapsedtime=get_my_current_workingtime($dbcon, $empno);
        $response['elapsedtime'] = $elapsedtime;
    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();


    /*
    *   LOCAL FUNCTIONS
    */

    //emp_workingtime(timeno, empno, starttime, endtime, comment, isaccepted)

    function get_my_current_workingtime($dbcon, $me)
    {
        date_default_timezone_set("Asia/Dhaka");
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT timeno,TIMESTAMPDIFF(SECOND,starttime,?) as elapsedtime
                FROM emp_workingtime as t
                WHERE t.empno=? AND (endtime is NULL)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("si", $now, $me);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows>0) {
            $elapsedtime=$result->fetch_array(MYSQLI_ASSOC)['elapsedtime'];
        } else {
            $elapsedtime=-1;
        }
        $stmt->close();

        return $elapsedtime;
    }
