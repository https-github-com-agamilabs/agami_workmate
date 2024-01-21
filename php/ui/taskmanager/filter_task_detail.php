<?php
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    $base_path = dirname(dirname(dirname(__FILE__)));
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

        //USER MANAGEMENT
        $assignedto = -1;
        if (isset($_POST['assignedto']) && strlen($_POST['assignedto'])>0) {
            $assignedto = (int) $_POST['assignedto'];
        }

        if($assignedto<0 && $ucatno==19){
            $assignedto = 0;
        }

        //TIME MANAGEMENT
        // $startdate = NULL;
        // if (isset($_POST['startdate']) && strlen($_POST['startdate'])>0) {
        //     $startdate = trim(strip_tags($_POST['startdate']));
        // }

        // $enddate = NULL;
        // if (isset($_POST['enddate']) && strlen($_POST['enddate'])>0) {
        //     $enddate = trim(strip_tags($_POST['enddate']));
        // }else if($startdate != NULL){
        //     date_default_timezone_set("Asia/Dhaka");
        //     $enddate = date("Y-m-d");
        // }

        // STATUS MANAGEMENT
        $wstatusno = -1;
        if (isset($_POST['wstatusno'])) {
            $wstatusno = trim(strip_tags($_POST['wstatusno']));
        }

        // PAGE MANAGEMENT
        $pageno=1;
        if (isset($_POST['pageno'])) {
            $pageno = (int) $_POST['pageno'];
        }

        $limit=25;
        if (isset($_POST['limit'])) {
            $limit = (int) $_POST['limit'];
        }

        $results = get_task_info($dbcon, $assignedto,$wstatusno,$pageno,$limit,$userno);
        $results_array = array();
        if ($results->num_rows > 0) {
            while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
                $backlogno = $row['backlogno'];
                $storytype = $row['storytype'];

                if($storytype == 3){
                    $rs_cbschedule=get_task_schedule($dbcon, $backlogno);
                    $schedule_array = array();
                    if ($rs_cbschedule->num_rows > 0) {
                        while ($srow = $rs_cbschedule->fetch_array(MYSQLI_ASSOC)) {
                            $cblscheduleno=$srow['cblscheduleno'];
    
                            $rs_progress=get_progress_detail($dbcon, $cblscheduleno);
                            $progress_array = array();
                            if ($rs_progress->num_rows > 0) {
                                while ($prow = $rs_progress->fetch_array(MYSQLI_ASSOC)) {
                                    $progress_array[]=$prow;
                                }
                            }
                            $srow['progress'] = $progress_array;
    
                            $rs_deadline=get_all_deadlines($dbcon, $cblscheduleno);
                            $deadline_array = array();
                            if ($rs_deadline->num_rows > 0) {
                                while ($drow = $rs_deadline->fetch_array(MYSQLI_ASSOC)) {
                                    $deadline_array[]=$drow;
                                }
                            }
                            $srow['deadlines'] = $deadline_array;
    
                            $schedule_array[]=$srow;
                        }
                    }
                    $row['schedule'] = $schedule_array;    
                }

                $rs_comments=get_sub_comments($dbcon, $backlogno); 
                $comment_array = array();
                if ($rs_comments->num_rows > 0) {
                    while ($drow = $rs_comments->fetch_array(MYSQLI_ASSOC)) {
                        $comment_array[]=$drow;
                    }
                }
                $row['comments'] = $comment_array;
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
    //asp_channelbacklog(backlogno, channelno, story, points, prioritylevelno, relativepriority, storytypeno, lastupdatetime, userno)
    //asp_cblschedule(cblscheduleno,backlogno,howto,assignedto, assigntime,scheduledate,userno)
    //asp_cblprogress(cblprogressno,cblscheduleno,progresstime,result,wstatusno,percentile,userno)
    
    function get_task_info($dbcon, $assignedto, $wstatusno,$pageno,$limit,$login_userno){
        $startindex=($pageno-1)*$limit;

        $params = array();
        $types = "";

        
        /**
         * Schedule Filter
         */
        // $schedule_filter = " 1 ";
        // if($startdate != NULL){
        //     $schedule_filter .= " (
        //                         ? <= DATE_ADD(scheduledate, INTERVAL (duration-1) DAY) AND
        //                         ? >= scheduledate
        //                     )";
        //     $params[] = &$startdate;
        //     $types .= 's';
        //     $params[] = &$enddate;
        //     $types .= 's';
        // }

        /**
         * Progress_filter
         */
        $progress_filter = " 1 ";
        if($wstatusno<0){
            //No Operation, i.e. No Filter
        } else if($wstatusno<=2){
            $progress_filter .= " AND (cblscheduleno NOT IN(
                                SELECT DISTINCT cblscheduleno
                                FROM asp_cblprogress
                                )
                                OR
                                cblscheduleno IN(
                                SELECT DISTINCT cblscheduleno
                                FROM asp_cblprogress
                                WHERE wstatusno <=2
                                    AND cblprogressno IN
                                        (SELECT max(cblprogressno)
                                        FROM asp_cblprogress
                                        GROUP BY cblscheduleno)
                                )
                            )";
        }else{
            $progress_filter .= " AND cblscheduleno IN(
                        SELECT DISTINCT cblscheduleno
                        FROM asp_cblprogress
                        WHERE wstatusno = ?
                            AND cblprogressno IN
                                (SELECT max(cblprogressno)
                                FROM asp_cblprogress
                                GROUP BY cblscheduleno
                                )
                        )";

            $params[] = &$wstatusno;
            $types .= 'i';
        }

        $filter = " 1 ";
        if($assignedto>0){
            $filter .= " AND b.backlogno IN 
                            (SELECT DISTINCT backlogno
                            FROM asp_cblschedule as cs
                            WHERE $progress_filter
                                AND assignedto = ?) ";
            $params[] = &$assignedto;
            $types .= 'i';
        }else if($assignedto==0){
            //ADMIN, SO NO FILTER, ALL USERS WILL BE SHOWN
        }else{
            $filter .= "  AND b.backlogno IN 
                            (SELECT DISTINCT backlogno
                            FROM asp_cblschedule as cs
                            WHERE $progress_filter
                                AND assignedto IN (
                                    SELECT DISTINCT userno
                                    FROM hr_user
                                    WHERE supervisor=? OR userno=?
                                    )
                            ) ";
            $params[] = &$login_userno;
            $types .= 'i';
            $params[] = &$login_userno;
            $types .= 'i';
        }

        $sql = "SELECT channelno,(SELECT channeltitle FROM msg_channel WHERE channelno=b.channelno) as channeltitle,
                        b.backlogno,story,b.points,storytype, b.lastupdatetime as storytime, createwatchlisttime,
                        prioritylevelno,(SELECT priorityleveltitle FROM asp_prioritylevel WHERE prioritylevelno=b.prioritylevelno) as priorityleveltitle,
                        storyphaseno,(SELECT storyphasetitle FROM asp_storyphase WHERE storyphaseno=b.storyphaseno) as storyphasetitle,
                        relativepriority,
                        b.userno, CONCAT(firstname,' ',IFNULL(lastname,'')) as postedby, photo_url
                FROM asp_channelbacklog as b
                    INNER JOIN hr_user as u ON b.userno=u.userno
                    LEFT JOIN (
                        SELECT createtime as createwatchlisttime
                        FROM asp_watchlist 
                        WHERE userno=$login_userno) as w ON  b.backlogno=w.backlogno
                WHERE parentbacklogno IS NULL 
                    AND $filter
                ORDER BY prioritylevelno, relativepriority, b.backlogno DESC 
                LIMIT ?,?
                ";

        $params[] = &$startindex;
        $types .= 'i';

        $params[] = &$limit;
        $types .= 'i';

        if (!$stmt = $dbcon->prepare($sql))
            throw new Exception("Prepare statement failed: " . $dbcon->error);

        call_user_func_array(array($stmt, "bind_param"), array_merge(array($types), $params));

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    //asp_cblschedule(cblscheduleno,backlogno,howto,assignedto, assigntime,scheduledate,duration,userno)
    function get_task_schedule($dbcon, $backlogno){
        $sql = "SELECT  cblscheduleno,howto, assigntime,scheduledate,duration, 
                        s.assignedto, CONCAT(u.firstname,' ',IFNULL(u.lastname,'')) as assignee, photo_url
                FROM asp_cblschedule as s
                    INNER JOIN hr_user as u ON s.assignedto=u.userno
                WHERE backlogno=?
                ORDER BY cblscheduleno 
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $backlogno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_progress_detail($dbcon, $cblscheduleno){
        $sql = "SELECT cblprogressno,cblscheduleno,progresstime,result,percentile,
                    wstatusno,(SELECT statustitle FROM asp_workstatus WHERE wstatusno=p.wstatusno) as statustitle,
                    userno,(SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=p.userno) as entryby
                FROM asp_cblprogress as p
                WHERE cblscheduleno=?
                ORDER BY cblprogressno
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
    

    function get_sub_comments($dbcon, $backlogno){
        $sql = "SELECT backlogno, story, points,storyphaseno, storytype, b.lastupdatetime, 
                    b.userno, CONCAT(u.firstname,' ',IFNULL(u.lastname,'')) as commentedby, u.photo_url
                FROM asp_channelbacklog as b
                    INNER JOIN hr_user as u ON b.userno=u.userno
                WHERE parentbacklogno=?
                ORDER BY backlogno
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $backlogno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }
