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

        //$userno=1;

        $results = get_today_task($dbcon);
        $results_array = array();
        if ($results->num_rows > 0) {
            while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
                $cblscheduleno=$row['cblscheduleno'];
                $rs_progress=get_progress_detail($dbcon, $cblscheduleno);
                $progress_array = array();
                if ($rs_progress->num_rows > 0) {
                    while ($prow = $rs_progress->fetch_array(MYSQLI_ASSOC)) {
                        $progress_array[]=$prow;
                    }
                }
                $row['progress'] = $progress_array;

                $rs_deadline=get_all_deadlines($dbcon, $cblscheduleno);
                $deadline_array = array();
                if ($rs_deadline->num_rows > 0) {
                    while ($drow = $rs_deadline->fetch_array(MYSQLI_ASSOC)) {
                        $deadline_array[]=$drow;
                    }
                }
                $row['deadlines'] = $deadline_array;

                $results_array[] = $row;
            }
        }

        $rs_scheduled = get_assigned_not_started_task($dbcon);
        if ($rs_scheduled->num_rows > 0) {
            while ($row = $rs_scheduled->fetch_array(MYSQLI_ASSOC)) {
                $row['progress'] = array();

                $cblscheduleno=$row['cblscheduleno'];
                $rs_deadline=get_all_deadlines($dbcon, $cblscheduleno);
                $deadline_array = array();
                if ($rs_deadline->num_rows > 0) {
                    while ($drow = $rs_deadline->fetch_array(MYSQLI_ASSOC)) {
                        $deadline_array[]=$drow;
                    }
                }
                $row['deadlines'] = $deadline_array;

                $results_array[] = $row;
            }

        }

        $response['results'] = $results_array;

        //count($results_array) >= $limit ? $response['more']=true : $response['more']=false;
    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();


    /*
    *   LOCAL FUNCTIONS
    */
    //wstatusno<3@progress on different-day
    //asp_channelbacklog(backlogno,channelno,story,storytype,prioritylevelno,relativepriority,storyphaseno,parentbacklogno,approved,accessibility,lastupdatetime,userno)
    //asp_cblschedule(cblscheduleno,backlogno,howto,assignedto, assigntime,scheduledate,userno)
    //asp_cblprogress(cblprogressno,cblscheduleno,progresstime,result,wstatusno,userno)
    function get_progress_detail($dbcon, $cblscheduleno){
        $sql = "SELECT cblprogressno,cblscheduleno,progresstime,result,
                    wstatusno,(SELECT statustitle FROM asp_workstatus WHERE wstatusno=p.wstatusno) as statustitle,
                    userno,(SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=p.userno) as entryby
                FROM asp_cblprogress as p
                WHERE cblscheduleno=?
                ORDER BY cblprogressno DESC
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $cblscheduleno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_all_deadlines($dbcon, $cblscheduleno){
        $sql = "SELECT dno,cblscheduleno,deadline
                FROM asp_deadlines as d
                WHERE cblscheduleno=?
                ORDER BY dno
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $cblscheduleno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_today_task($dbcon){
        date_default_timezone_set("Asia/Dhaka");
        $today = date("Y-m-d");
        $sql = "SELECT channelno,(SELECT channeltitle FROM msg_channel WHERE channelno=b.channelno) as channeltitle,
                        b.backlogno,story,storytype,storytypetitle,
                        prioritylevelno,(SELECT priorityleveltitle FROM asp_prioritylevel WHERE prioritylevelno=b.prioritylevelno) as priorityleveltitle,
                        storyphaseno,(SELECT storyphasetitle FROM asp_storyphase WHERE storyphaseno=b.storyphaseno) as storyphasetitle,
                        relativepriority,howto,assigntime,scheduledate,duration,
                        s.cblscheduleno,
                        s.assignedto, (SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=s.assignedto) as assignee,
                        b.userno, (SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=b.userno) as assignedby
                FROM
                    (SELECT cblscheduleno,backlogno,assignedto,howto,assigntime,scheduledate,
                            duration, userno
                    FROM asp_cblschedule as cs
                    WHERE (
                            (
                                ? BETWEEN scheduledate AND DATE_ADD(scheduledate, INTERVAL (duration-1) DAY)
                            ) OR (
                                AND cblscheduleno IN(
                                SELECT DISTINCT cblscheduleno
                                FROM asp_cblprogress as p
                                WHERE DATE(progresstime) = ?
                            )
                          )
                        AND cblscheduleno IN(
                                SELECT DISTINCT cblscheduleno
                                FROM asp_cblprogress
                                WHERE cblscheduleno NOT IN(
                                    SELECT DISTINCT cblscheduleno
                                    FROM asp_cblprogress
                                    WHERE wstatusno>2)
                                )
                    ) as s
                    INNER JOIN
                    (SELECT backlogno, channelno, story, 
                            storytype,(SELECT storytypetitle FROM asp_storytype WHERE storytypeno=b.storytype) as storytypetitle,
                            storyphaseno,prioritylevelno, relativepriority, userno
                    FROM asp_channelbacklog) as b
                    ON s.backlogno=b.backlogno
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ss", $today,$today);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_assigned_not_started_task($dbcon){
        date_default_timezone_set("Asia/Dhaka");
        $today = date("Y-m-d");
        $sql = "SELECT channelno,(SELECT channeltitle FROM msg_channel WHERE channelno=b.channelno) as channeltitle,
                        b.backlogno,story,storytype,storytypetitle,
                        prioritylevelno,(SELECT priorityleveltitle FROM asp_prioritylevel WHERE prioritylevelno=b.prioritylevelno) as priorityleveltitle,
                        storyphaseno,(SELECT storyphasetitle FROM asp_storyphase WHERE storyphaseno=b.storyphaseno) as storyphasetitle,
                        relativepriority,howto,assigntime,scheduledate,duration,
                        s.cblscheduleno,
                        s.assignedto, (SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=s.assignedto) as assignee,
                        b.userno, (SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=b.userno) as assignedby
                FROM
                    (SELECT cblscheduleno,backlogno,howto,assignedto,assigntime,scheduledate,
                            duration, userno
                    FROM asp_cblschedule as cs
                    WHERE (
                            ? BETWEEN scheduledate AND DATE_ADD(scheduledate, INTERVAL (duration-1) DAY)
                          )
                        AND cblscheduleno NOT IN(
                            SELECT DISTINCT cblscheduleno
                            FROM asp_cblprogress as p
                            )
                    ) as s
                    INNER JOIN
                    (SELECT backlogno, channelno, story, 
                            storytype,(SELECT storytypetitle FROM asp_storytype WHERE storytypeno=b.storytype) as storytypetitle,
                            storyphaseno,prioritylevelno, relativepriority, userno
                    FROM asp_channelbacklog) as b
                    ON s.backlogno=b.backlogno
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

