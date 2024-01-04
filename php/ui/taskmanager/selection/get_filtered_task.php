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

        //USER MANAGEMENT
        $assignedto = -1;
        if (isset($_POST['assignedto']) && strlen($_POST['assignedto'])>0) {
            $assignedto = (int) $_POST['assignedto'];
        }

        if($assignedto<0 && $ucatno==19){
            $assignedto = 0;
        }

        //TIME MANAGEMENT
        $startdate = NULL;
        if (isset($_POST['startdate']) && strlen($_POST['startdate'])>0) {
            $startdate = trim(strip_tags($_POST['startdate']));
        }

        if (isset($_POST['enddate']) && strlen($_POST['enddate'])>0) {
            $enddate = trim(strip_tags($_POST['enddate']));
        }else if($startdate != NULL){
            date_default_timezone_set("Asia/Dhaka");
            $enddate = date("Y-m-d");
        }else{
            $enddate = NULL;
        }

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

        $results = get_filtered_task($dbcon,$assignedto, $startdate,$enddate,$wstatusno,$pageno,$limit,$userno);
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

        $response['error'] = false;
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


    function get_filtered_task($dbcon,$assignedto, $startdate,$enddate,$wstatusno,$pageno,$limit,$login_userno){
        $startindex=($pageno-1)*$limit;

        $params = array();
        $types = "";

        $filter = " ";
        if($assignedto>0){
            $filter .= " AND assignedto = ? ";
            $params[] = &$assignedto;
            $types .= 'i';
        }else if($assignedto==0){
            //ADMIN, SO NO FILTER, ALL USERS WILL BE SHOWN
        }else{
            $filter .= " AND assignedto IN (
                            SELECT DISTINCT userno
                            FROM hr_user
                            WHERE supervisor=? OR userno=?
                            ) ";
            $params[] = &$login_userno;
            $types .= 'i';
            $params[] = &$login_userno;
            $types .= 'i';
        }

        if($startdate != NULL){
            $filter .= " AND
                            (
                                ? <= DATE_ADD(scheduledate, INTERVAL (duration-1) DAY) AND
                                ? >= scheduledate
                            )";
            $params[] = &$startdate;
            $types .= 's';
            $params[] = &$enddate;
            $types .= 's';
        }

        if($wstatusno<0){
            //No Operation, i.e. No Filter
        } else if($wstatusno<=2){
            $filter .= "AND (cblscheduleno NOT IN(
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
            $filter .= "AND cblscheduleno IN(
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

        $sql = "SELECT channelno,(SELECT channeltitle FROM msg_channel WHERE channelno=b.channelno) as channeltitle,
                        b.backlogno,story,storytype,storytypetitle,
                        prioritylevelno,(SELECT priorityleveltitle FROM asp_prioritylevel WHERE prioritylevelno=b.prioritylevelno) as priorityleveltitle,
                        storyphaseno,(SELECT storyphasetitle FROM asp_storyphase WHERE storyphaseno=b.storyphaseno) as storyphasetitle,
                        relativepriority,howto,assigntime,scheduledate,duration,
                        s.cblscheduleno,
                        s.assignedto, (SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=s.assignedto) as assignee,
                        b.userno, (SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=b.userno) as assignedby
                FROM
                    (
                        SELECT cblscheduleno,backlogno,assignedto,howto,assigntime,scheduledate,
                                duration, userno
                        FROM asp_cblschedule as cs
                        WHERE 1 $filter
                    ) as s
                    INNER JOIN
                    (
                        SELECT backlogno, channelno, story, 
                            storytype,(SELECT storytypetitle FROM asp_storytype WHERE storytypeno=storytype) as storytypetitle,
                            storyphaseno,prioritylevelno, relativepriority, userno
                        FROM asp_channelbacklog
                    ) as b
                    ON s.backlogno=b.backlogno
                ORDER BY b.backlogno DESC
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

    function get_all_incomplete_task($dbcon){
        $sql = "SELECT channelno,(SELECT channeltitle FROM msg_channel WHERE channelno=b.channelno) as channeltitle,
                        b.backlogno,story,storytype,storytypetitle,
                        prioritylevelno,(SELECT priorityleveltitle FROM asp_prioritylevel WHERE prioritylevelno=b.prioritylevelno) as priorityleveltitle,
                        storyphaseno,(SELECT storyphasetitle FROM asp_storyphase WHERE storyphaseno=b.storyphaseno) as storyphasetitle,
                        relativepriority,howto,assigntime,scheduledate,duration,
                        s.cblscheduleno,
                        b.userno, (SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=b.userno) as assignedby
                FROM
                    (SELECT cblscheduleno,backlogno,howto,assigntime,scheduledate,duration, userno
                    FROM asp_cblschedule as cs
                    WHERE cblscheduleno IN(
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
                            storytype,(SELECT storytypetitle FROM asp_storytype WHERE storytypeno=storytype) as storytypetitle,
                            storyphaseno,prioritylevelno, relativepriority, userno
                    ON s.backlogno=b.backlogno
                ";
        $stmt = $dbcon->prepare($sql);
        //$stmt->bind_param("ii", $userno,$userno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_my_incomplete_not_started_task($dbcon){
        $sql = "SELECT channelno,(SELECT channeltitle FROM msg_channel WHERE channelno=b.channelno) as channeltitle,
                        b.backlogno,story,storytype,storytypetitle,
                        prioritylevelno,(SELECT priorityleveltitle FROM asp_prioritylevel WHERE prioritylevelno=b.prioritylevelno) as priorityleveltitle,
                        storyphaseno,(SELECT storyphasetitle FROM asp_storyphase WHERE storyphaseno=b.storyphaseno) as storyphasetitle,
                        relativepriority,howto,assigntime,scheduledate,duration,
                        s.cblscheduleno,
                        b.userno, (SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=b.userno) as assignedby
                FROM
                    (SELECT cblscheduleno,backlogno,howto,assigntime,scheduledate,duration, userno
                    FROM asp_cblschedule as cs
                    WHERE cblscheduleno NOT IN(
                        SELECT DISTINCT cblscheduleno
                        FROM asp_cblprogress as p
                        )
                    ) as s
                    INNER JOIN
                    (SELECT backlogno, channelno, story, 
                            storytype,(SELECT storytypetitle FROM asp_storytype WHERE storytypeno=storytype) as storytypetitle,
                            storyphaseno,prioritylevelno, relativepriority, userno
                    FROM asp_channelbacklog) as b
                    ON s.backlogno=b.backlogno
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

