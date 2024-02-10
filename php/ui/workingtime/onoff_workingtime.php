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

        if(!isset($_SESSION['wm_orgno'])){
            throw new \Exception("You must select an organization!", 1);
        }else{
            $orgno= (int) $_SESSION['wm_orgno'];
        }

        if (isset($_POST['userno'])) {
            $empno=(int) $_POST['userno'];
        } else {
            throw new \Exception("You must login first!", 1);
        }

        $result=is_time_running($dbcon, $empno, $orgno);
        if ($result->num_rows>0) {
            $timeno = $result->fetch_array(MYSQLI_ASSOC)['timeno'];
            $nos=end_workingtime($dbcon, $timeno, $orgno);
            if ($nos>0) {
                $response['error'] = false;
                $response['message'] = "Time is Ended.";
            } else {
                $response['error'] = true;
                $response['message'] = "Cannot End Time!";
            }
        } else {
            $workfor=NULL;
            if (isset($_POST['workfor']) && strlen($_POST['workfor'])>0) {
                $workfor=(int) $_POST['workfor'];
            }

            if(isset($_POST['loclat']) && isset($_POST['loclon'])){
                $loclat = (double) $_POST['loclat'];
                $loclon = (double) $_POST['loclon'];

                $distance=0;
                $rs_workplace=get_user_wherework($dbcon, $orgno,$empno);
                if($rs_workplace->num_rows>0){
                    $row = $rs_workplace->fetch_array(MYSQLI_ASSOC);
                    $target_loclat=(double)$row['loclat'];
                    $target_loclon=(double)$row['loclon'];
                    $distance=getDistanceFromCoordinates($loclat, $loclon, $target_loclat, $target_loclon)
                }
            }else{
                $distance=0;
            }

            if($distance<100){
                $userno=start_workingtime($dbcon, $empno,$workfor,$orgno,$loclat,$loclon);
                if ($userno>0) {
                    $response['error'] = false;
                    if($workfor){
                        $response['message'] = "Time is Started for client.";
                    }else{
                        $response['message'] = "Time is Started for AGAMiLabs.";
                    }
                } else {
                    $response['error'] = true;
                    $response['message'] = "Cannot Start Time!";
                }
            }else{
                throw new \Exception("You are away of your assigned working place. \n Please go to your assigned working area and try again!", 1);
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

    function is_time_running($dbcon, $empno, $orgno)
    {
        $sql = "SELECT timeno
                FROM emp_workingtime
                WHERE orgno=? AND empno=? AND (endtime is NULL)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ii", $orgno, $empno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    //emp_workingtime(timeno,empno,workfor,starttime,endtime,comment,isaccepted)
    function start_workingtime($dbcon, $empno,$workfor, $orgno,$loclat,$loclon)
    {
        date_default_timezone_set("Asia/Dhaka");
        $now = date("Y-m-d H:i:s");
        $sql = "INSERT INTO emp_workingtime(
                                orgno,empno,workfor,starttime,endtime,loclat,loclon
                            )
                VALUES(?, ?,?,?,NULL,?,?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iiisdd", $orgno, $empno, $workfor,$now,$loclat,$loclon);
        $stmt->execute();
        return $stmt->insert_id;
    }

    //emp_workingtime(timeno,empno,workfor,starttime,endtime,comment,isaccepted)
    function end_workingtime($dbcon, $timeno, $orgno)
    {
        date_default_timezone_set("Asia/Dhaka");
        $now = date("Y-m-d H:i:s");
        $sql = "UPDATE emp_workingtime
                SET endtime=?
                WHERE timeno=? AND orgno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("sii", $now, $timeno, $orgno);
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

    //com_userattlocset (attlocno,orgno,userno, loclat, loclon,starttime,endtime)
    function get_user_wherework($dbcon, $orgno,$userno)
    {
        $sql = "SELECT loclat, loclon,starttime,endtime
                FROM com_userattlocset
                WHERE orgno=? 
                    AND userno =?
                    AND (CURRENT_DATE() BETWEEN DATE(starttime) AND DATE(DATE_ADD(starttime, INTERVAL duration DAY)))
                ";

        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ii", $orgno,$userno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    function getDistanceFromCoordinates($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
      {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);
      
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
      
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
          cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
      }

