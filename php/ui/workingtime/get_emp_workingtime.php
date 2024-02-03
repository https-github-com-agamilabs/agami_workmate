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

        if(!isset($_SESSION['cogo_orgno'])){
            throw new \Exception("You must select an organization!", 1);
        }else{
            $orgno= (int) $_SESSION['cogo_orgno'];
        }

        if (isset($_POST['startdate'])) {
            $startdate=trim(strip_tags($_POST['startdate']));
        } else {
            throw new \Exception("You must specify start date!", 1);
        }

        if (isset($_POST['enddate'])) {
            $enddate=trim(strip_tags($_POST['enddate']));
        } else {
            throw new \Exception("You must specify end date!", 1);
        }

        $elapsedtime=get_my_current_workingtime($dbcon, $empno);
        $response['elapsedtime'] = $elapsedtime;

        if ($ucatno>=19) {
            if (isset($_POST['workfor']) && strlen($_POST['workfor'])>0) {
                $workfor=(int) $_POST['workfor'];
                if($workfor>0)
                    $list = get_workfor_workingtime($dbcon, $workfor, $startdate, $enddate, $orgno);
            }else{
                $list=get_all_workingtime($dbcon, $startdate, $enddate, $orgno);
            }
        } else if($ucatno>=13){
            $list = get_workfor_workingtime($dbcon, $empno, $startdate, $enddate, $orgno);
        }else {
            $list = get_emp_workingtime($dbcon, $empno, $startdate, $enddate, $orgno);
        }

        if ($list->num_rows > 0) {
            $meta_array = array();
            while ($row = $list->fetch_array(MYSQLI_ASSOC)) {
                $meta_array[] = $row;
            }
            $response['error'] = false;
            $response['data'] = $meta_array;
        } else {
            $response['error'] = true;
            $response['message'] = "No Working Time Yet Found!";
        }
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
    function get_emp_workingtime($dbcon, $empno, $startdate, $enddate, $orgno)
    {
        date_default_timezone_set("Asia/Dhaka");
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT timeno,
                    empno, (SELECT concat(firstname, ' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=t.empno) as userfullname,
                    workfor, (SELECT concat(firstname, ' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=t.workfor) as workfor_name,
                    starttime, endtime, comment, isaccepted,
                    CASE
                        WHEN endtime IS NULL
                            THEN TIMESTAMPDIFF(SECOND,starttime,?)
                        ELSE TIMESTAMPDIFF(SECOND,starttime, endtime)
                    END as elapsedtime
                FROM emp_workingtime as t
                WHERE orgno=?
                    AND t.empno IN
                            (SELECT userno
                            FROM hr_user
                            WHERE isactive=1 AND (supervisor=? OR userno=?)
                            )
                    AND (date(starttime) BETWEEN ? AND ?)
                ORDER BY starttime DESC";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("siiiss", $now, $orgno, $empno, $empno, $startdate, $enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_all_workingtime($dbcon, $startdate, $enddate,$orgno)
    {
        date_default_timezone_set("Asia/Dhaka");
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT timeno,
                    empno, (SELECT concat(firstname, ' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=t.empno) as userfullname,
                    workfor, (SELECT concat(firstname, ' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=t.workfor) as workfor_name,
                    starttime, endtime, comment, isaccepted,
                    CASE
                        WHEN endtime IS NULL
                            THEN TIMESTAMPDIFF(SECOND,starttime,?)
                        ELSE TIMESTAMPDIFF(SECOND,starttime, endtime)
                    END as elapsedtime
                FROM emp_workingtime as t
                WHERE orgno=? AND date(starttime) BETWEEN ? AND ?
                ORDER BY starttime DESC";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("siss", $now, $orgno,$startdate, $enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_workfor_workingtime($dbcon, $workfor, $startdate, $enddate,$orgno)
    {
        date_default_timezone_set("Asia/Dhaka");
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT timeno,
                    empno, (SELECT concat(firstname, ' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=t.empno) as userfullname,
                    workfor, (SELECT concat(firstname, ' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=t.workfor) as workfor_name,
                    starttime, endtime, comment, isaccepted,
                    CASE
                        WHEN endtime IS NULL
                            THEN TIMESTAMPDIFF(SECOND,starttime,?)
                        ELSE TIMESTAMPDIFF(SECOND,starttime, endtime)
                    END as elapsedtime
                FROM emp_workingtime as t
                WHERE orgno=? AND workfor=? AND date(starttime) BETWEEN ? AND ?
                ORDER BY starttime DESC";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("siiss", $now, $orgno, $workfor, $startdate, $enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_my_current_workingtime($dbcon, $me, $orgno)
    {
        date_default_timezone_set("Asia/Dhaka");
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT timeno,TIMESTAMPDIFF(SECOND,starttime,?) as elapsedtime
                FROM emp_workingtime as t
                WHERE orgno=? AND t.empno=? AND (endtime is NULL)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("sii", $now, $orgno, $me);
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
