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


        if (isset($_POST['userno'])) {
            $empno=(int) $_POST['userno'];
        } else {
            throw new \Exception("You must login first!", 1);
        }

        if (isset($_POST['workfor'])) {
            $workfor=(int) $_POST['workfor'];
        } else {
            throw new \Exception("Missing: whom you are working for!", 1);
        }

        $result=is_time_running($dbcon, $empno);
        if ($result->num_rows>0) {
            $timeno = $result->fetch_array(MYSQLI_ASSOC)['timeno'];
            $nos=end_workingtime($dbcon, $timeno);
            if ($nos>0) {
                $response['error'] = false;
                $response['message'] = "Time is Ended.";
            } else {
                $response['error'] = true;
                $response['message'] = "Cannot End Time!";
            }
        } else {
            // $rs=check_my_incomplete_task($dbcon, $empno);
            // if ($rs->num_rows<=0){
            //     $rs2=check_my_incomplete_not_started_task($dbcon, $empno);
            //     if ($rs2->num_rows<=0){
            //         throw new \Exception("If you/person have an incomplete task, update your task-status. If you don't have an incomplete task, take a task from 'Available Task'!", 1);
            //     }
            // }
            $userno=start_workingtime($dbcon, $empno,$workfor);
            if ($userno>0) {
                $response['error'] = false;
                $response['message'] = "Time is Started.";
            } else {
                $response['error'] = true;
                $response['message'] = "Cannot Start Time!";
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

    function is_time_running($dbcon, $empno)
    {
        $sql = "SELECT timeno
                FROM emp_workingtime
                WHERE empno=? AND (endtime is NULL)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $empno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    //emp_workingtime(timeno,empno,workfor,starttime,endtime,comment,isaccepted)
    function start_workingtime($dbcon, $empno,$workfor)
    {
        date_default_timezone_set("Asia/Dhaka");
        $now = date("Y-m-d H:i:s");
        $sql = "INSERT INTO emp_workingtime(
                                empno,workfor,starttime,endtime
                            )
                VALUES(?,?,?,NULL)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iis", $empno, $workfor,$now);
        $stmt->execute();
        return $stmt->insert_id;
    }

    //emp_workingtime(timeno,empno,workfor,starttime,endtime,comment,isaccepted)
    function end_workingtime($dbcon, $timeno)
    {
        date_default_timezone_set("Asia/Dhaka");
        $now = date("Y-m-d H:i:s");
        $sql = "UPDATE emp_workingtime
                SET endtime=?
                WHERE timeno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("si", $now, $timeno);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    function check_my_incomplete_task($dbcon, $userno){
        date_default_timezone_set("Asia/Dhaka");
        $today = date("Y-m-d");

        $sql = "SELECT backlogno
                FROM asp_cblschedule
                WHERE assignedto=?
                    AND cblscheduleno IN(
                    SELECT DISTINCT cblscheduleno
                    FROM asp_cblprogress
                    WHERE userno=?
                        AND ? <= DATE_ADD(DATE(progresstime),INTERVAL +1 DAY)
                        AND cblscheduleno NOT IN(
                        SELECT DISTINCT cblscheduleno
                        FROM asp_cblprogress
                        WHERE wstatusno>2)
                    )
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iis", $userno,$userno,$today);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function check_my_incomplete_not_started_task($dbcon, $userno){
        date_default_timezone_set("Asia/Dhaka");
        $today = date("Y-m-d");
        $sql = "SELECT cblscheduleno,backlogno,howto,assigntime,scheduledate,duration
                FROM asp_cblschedule
                WHERE assignedto=?
                    AND (
                            ? BETWEEN scheduledate AND DATE_ADD(scheduledate, INTERVAL (duration-1) DAY)
                        )
                    AND cblscheduleno NOT IN(
                    SELECT DISTINCT cblscheduleno
                    FROM asp_cblprogress as p
                    )

                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("is", $userno,$today);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }
