<?php
include_once  dirname(dirname(__FILE__)) . "/login/check_session.php";

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

try {
    $base_path = dirname(dirname(dirname(__FILE__)));
    require_once($base_path . "/db/Database.php");

    $db = new Database();
    $dbcon = $db->db_connect();
    if (!$db->is_connected()) {
        throw new \Exception("Database is not connected!", 1);
    }

    // if($ucatno == 19){
    // }

    //$userno comes from check_session
    $result = get_watchlist($dbcon, $userno);

    $watchlist_array = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $backlogno = $row['backlogno'];
            
            $rs_cbschedule=get_task_schedule($dbcon, $backlogno);
            $schedule_array = array();
            if ($rs_cbschedule->num_rows > 0) {
                while ($srow = $rs_cbschedule->fetch_array(MYSQLI_ASSOC)) {
                    $schedule_array[]=$srow;
                }
            }
            $row['schedule_progress'] = $schedule_array;
            $watchlist_array[] = $row;
        }
    }
    $response['error'] = false;
    $response['data'] = $watchlist_array;
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();


/*
    *   LOCAL FUNCTIONS
    */
//asp_channelbacklog(backlogno,channelno,story,storytype,points,prioritylevelno,relativepriority,storyphaseno,parentbacklogno,approved,accessibility,lastupdatetime,userno)
//asp_storytype(storytypeno,storytypetitle)
function get_watchlist($dbcon, $userno)
{
    $sql = "SELECT w.backlogno, 
                    b.channelno, (SELECT channeltitle FROM msg_channel WHERE channelno=b.channelno) as channeltitle,
                    b.story, b.points,
                    b.storytype,(SELECT storytypetitle FROM asp_storytype WHERE storytypeno=b.storytype) as storytypetitle,
                    b.prioritylevelno,(SELECT priorityleveltitle FROM asp_prioritylevel WHERE prioritylevelno=b.prioritylevelno) as priorityleveltitle,
                    b.relativepriority,
                    b.storyphaseno,(SELECT storyphasetitle FROM asp_storyphase WHERE storyphaseno=b.storyphaseno) as storyphasetitle               
            FROM asp_watchlist as w
                INNER JOIN asp_channelbacklog as b ON w.backlogno=b.backlogno
            WHERE w.userno=? AND b.storytype=3
            ORDER BY createtime";
    $stmt = $dbcon->prepare($sql);
    if ($dbcon->error) {
        echo $dbcon->error;
    }
    $stmt->bind_param("i", $userno);
    $stmt->execute();
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

//asp_cblschedule(cblscheduleno,backlogno,howto,assignedto, assigntime,scheduledate,duration,userno)
function get_task_schedule($dbcon, $backlogno){
    $sql = "SELECT  s.cblscheduleno,scheduledate,duration, 
                    s.assignedto, CONCAT(u.firstname,' ',IFNULL(u.lastname,'')) as assignee, u.photo_url,
                    lp.cblscheduleno,lp.progresstime,lp.percentile,lp.wstatusno, lp.statustitle
            FROM asp_cblschedule as s
                INNER JOIN hr_user as u ON s.assignedto=u.userno
                LEFT JOIN (
                    SELECT cblscheduleno,progresstime,percentile,
                            wstatusno,(SELECT statustitle FROM asp_workstatus WHERE wstatusno=p.wstatusno) as statustitle
                    FROM (
                        SELECT cblprogressno,cblscheduleno, progresstime, percentile,
                            wstatusno,
                            ROW_NUMBER() OVER (PARTITION BY cblscheduleno ORDER BY progresstime DESC) AS recentprogress
                        FROM asp_cblprogress
                        WHERE cblscheduleno IN 
                            (SELECT DISTINCT cblscheduleno
                            FROM asp_cblschedule
                            WHERE backlogno=?
                            )
                        ) as p
                    WHERE p.recentprogress=1
                    ) as lp ON lp.cblscheduleno=s.cblscheduleno
            WHERE backlogno=?
            ORDER BY s.cblscheduleno 
            ";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("ii", $backlogno,$backlogno);
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