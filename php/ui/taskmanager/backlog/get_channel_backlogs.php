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

        if (isset($_POST['channelno'])) {
            $channelno = (int) $_POST['channelno'];
        }else{
            throw new \Exception("You must select a channel!", 1);
        }


        $pageno=1;
        if (isset($_POST['pageno'])) {
            $pageno = (int) $_POST['pageno'];
        }

        $limit=10;
        if (isset($_POST['limit'])) {
            $limit = (int) $_POST['limit'];
        }

        /*
        $search_key="";
        if (isset($_POST['search_key'])) {
            $search_key = trim(strip_tags($_POST['search_key']));
        }
        */

        if($my_permissionlevel==1){
            $backlogs = get_my_backlog($dbcon,$channelno,$userno,$pageno,$limit);
        }else{
            $backlogs = get_backlog($dbcon,$channelno,$pageno,$limit);//,$pageno,$limit,$search_key);
        }

        $backlog_array = array();
        if ($backlogs->num_rows > 0) {
            while ($row = $backlogs->fetch_array(MYSQLI_ASSOC)) {
                $backlogno=$row['backlogno'];
                $rs_schedule=get_schedule($dbcon,$backlogno);
                $schedule_array = array();
                $running = 0;
                if ($rs_schedule->num_rows > 0) {
                    while ($srow = $rs_schedule->fetch_array(MYSQLI_ASSOC)) {
                        $cblscheduleno=$srow['cblscheduleno'];
                        $rs_progress=get_progress($dbcon,$cblscheduleno);
                        $progress_array = array();
                        if ($rs_progress->num_rows > 0) {
                            while ($prow = $rs_progress->fetch_array(MYSQLI_ASSOC)) {
                                $progress_array[]=$prow;
                            }
                        }
                        $srow['progress']=$progress_array;

                        $rs_deadline=get_all_deadlines($dbcon, $cblscheduleno);
                        $deadline_array = array();
                        if ($rs_deadline->num_rows > 0) {
                            while ($drow = $rs_deadline->fetch_array(MYSQLI_ASSOC)) {
                                $deadline_array[]=$drow;
                            }
                        }
                        $srow['deadlines'] = $deadline_array;
                        if($srow['scheduledate']<date("Y-m-d"))
                            $$running = 1;
                        $schedule_array[]=$srow;
                    }
                }
                $row['schedule']=$schedule_array;

                //task status calculation
                $inprogress=0;
                $completed=0;
                $rejected=0;
                $schedulenos=0;
                $rs_status=get_last_status($dbcon, $backlogno);
                $status_array=array();
                if ($rs_status->num_rows > 0) {
                    $schedulenos=$rs_status->num_rows;
                    while ($strow = $rs_status->fetch_array(MYSQLI_ASSOC)) {
                        if($strow['wstatusno']==4)
                            $rejected++;
                        else if($strow['wstatusno']==3)
                            $completed++;
                        else $inprogress++;
                    }

                    /*
                    * all assigned task is rejected, means abondoned
                    * or, all non-rejected task is completed, means completed
                    * otherwise, in-progress
                    */
                    // if($rejected==$schedulenos)
                    //     $wstatusno=4;
                    // else if($completed>0 && ($completed+$rejected)==$schedulenos)
                    //     $wstatusno=3;
                    // else $wstatusno=2;
                }
                // else{
                //     if($running>0)
                //         $wstatusno=2;
                //     else
                //         $wstatusno=0;
                // }
                // $row['wstatusno']=$wstatusno;
                $backlog_array[] = $row;
            }
        }

        $response['error'] = false;
        $response['results'] = $backlog_array;
    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();

    //asp_channelbacklog(backlogno,channelno,story,points,storytype,prioritylevelno,relativepriority,storyphaseno,parentbacklogno,approved,accessibility,lastupdatetime,userno)
    //asp_cblschedule(cblscheduleno,backlogno,howto,assignedto, assigntime,scheduledate,userno)
    //asp_cblprogress(cblprogressno,cblscheduleno,progresstime,result,wstatusno,userno)

    function get_my_backlog($dbcon,$channelno,$userno,$pageno,$limit)//,$pageno,$limit,$search_key)
    {
        //$search='%'.$search_key.'%';
        $startindex=($pageno-1)*$limit;

        $sql = "SELECT backlogno,
                        channelno, (SELECT channeltitle FROM msg_channel WHERE channelno=cb.channelno) as channeltitle,
                        story, points,storytype,
                        prioritylevelno,(SELECT priorityleveltitle FROM asp_prioritylevel WHERE prioritylevelno=cb.prioritylevelno) as priorityleveltitle,
                        relativepriority,
                        storyphaseno,(SELECT storyphasetitle FROM asp_storyphase WHERE storyphaseno=cb.storyphaseno) as storyphasetitle,
                        cb.lastupdatetime,
                        cb.userno, (SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=cb.userno) as assignedby,
                        approved
                FROM asp_channelbacklog as cb
                WHERE channelno=? AND cb.userno=?
                ORDER BY backlogno DESC
                LIMIT ?,?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iiii", $channelno,$userno,$startindex, $limit);//,$search, $startindex, $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    function get_backlog($dbcon,$channelno,$pageno,$limit)//,$pageno,$limit,$search_key)
    {
        //$search='%'.$search_key.'%';
        $startindex=($pageno-1)*$limit;

        $sql = "SELECT backlogno,
                        channelno, (SELECT channeltitle FROM msg_channel WHERE channelno=cb.channelno) as channeltitle,
                        story, points,storytype,
                        prioritylevelno,(SELECT priorityleveltitle FROM asp_prioritylevel WHERE prioritylevelno=cb.prioritylevelno) as priorityleveltitle,
                        relativepriority,
                        storyphaseno,(SELECT storyphasetitle FROM asp_storyphase WHERE storyphaseno=cb.storyphaseno) as storyphasetitle,
                        cb.lastupdatetime,
                        cb.userno, (SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=cb.userno) as assignedby,
                        approved
                FROM asp_channelbacklog as cb
                WHERE channelno=?
                ORDER BY backlogno DESC
                LIMIT ?,?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iii", $channelno,$startindex, $limit);//,$search, $startindex, $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    function get_schedule($dbcon,$backlogno){
        $sql = "SELECT cblscheduleno,howto,
                        assignedto, (SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=s.assignedto) as assignee,
                        assigntime,scheduledate,duration,
                        userno, (SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=s.userno) as entryby
                FROM asp_cblschedule as s
                WHERE backlogno=?
                ORDER BY cblscheduleno DESC
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $backlogno);
        $stmt->execute();
        return $stmt->get_result();
    }

    function get_progress($dbcon,$cblscheduleno){
        $sql = "SELECT cblprogressno,progresstime,result,
                        wstatusno,(SELECT statustitle FROM asp_workstatus WHERE wstatusno=p.wstatusno) as statustitle,
                        userno, (SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=p.userno) as entryby
                FROM asp_cblprogress as p
                WHERE cblscheduleno=?
                ORDER BY cblprogressno DESC
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $cblscheduleno);
        $stmt->execute();
        return $stmt->get_result();
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

    function get_last_status($dbcon, $backlogno){
        $sql = "SELECT cblprogressno, cblscheduleno, wstatusno
                FROM asp_cblprogress
                WHERE cblprogressno IN (SELECT max(cblprogressno)
                            FROM asp_cblprogress
                            WHERE cblscheduleno IN (
                                SELECT DISTINCT cblscheduleno
                                FROM asp_cblschedule
                                WHERE backlogno=?)
                            GROUP BY cblscheduleno)
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $backlogno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }
