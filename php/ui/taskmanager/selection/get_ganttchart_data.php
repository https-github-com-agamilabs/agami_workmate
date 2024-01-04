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

        $assignedto=-1;
        if (isset($_POST['assignedto'])) {
            $assignedto = (int) $_POST['assignedto'];
        }

        if (isset($_POST['startdate'])) {
            $startdate = trim(strip_tags($_POST['startdate']));
        }else{
            throw new \Exception("Start-date cannot be empty!", 1);
        }

        if (isset($_POST['enddate'])) {
            $enddate = trim(strip_tags($_POST['enddate']));
        }else{
            throw new \Exception("End-date cannot be empty!", 1);
        }

        if($assignedto>0){
            $results = get_individual_schedule_data($dbcon, $assignedto,$startdate,$enddate);
        }else{
            $results = get_schedule_data($dbcon, $startdate,$enddate);
        }
        $results_array = array();
        if ($results->num_rows > 0) {
            while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
                $results_array[] = $row;
            }
        }
        $response['results'] = $results_array;

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
    function get_schedule_data($dbcon, $startdate,$enddate){
        $sql = "SELECT s.backlogno,
                    b.channelno, (SELECT channeltitle FROM msg_channel WHERE channelno=b.channelno) as channeltitle,
                    assignedto,(SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=s.assignedto) as assignee,
                    scheduledate as startdate,
                    DATE_ADD(scheduledate, INTERVAL (duration-1) DAY) as enddate,
                    extendeddate,
                    lastprogressdate
                FROM (SELECT *
                        FROM asp_cblschedule
                        WHERE ((scheduledate BETWEEN ? AND ?) OR (DATE_ADD(scheduledate, INTERVAL (duration-1) DAY) BETWEEN ? AND ?) OR (scheduledate<? AND DATE_ADD(scheduledate, INTERVAL (duration-1) DAY)>?))
                    ) as s
                    INNER JOIN (SELECT cblscheduleno, deadline as extendeddate
                                FROM asp_deadlines
                                WHERE dno IN (SELECT max(dno)
                                            FROM asp_deadlines
                                            GROUP BY cblscheduleno)
                                ) as d ON s.cblscheduleno=d.cblscheduleno
                    INNER JOIN asp_channelbacklog as b ON s.backlogno=b.backlogno
                    LEFT JOIN (SELECT cblscheduleno, DATE(progresstime) as lastprogressdate
                                FROM asp_cblprogress
                                WHERE cblprogressno IN (SELECT max(cblprogressno)
                                            FROM asp_cblprogress
                                            GROUP BY cblscheduleno)
                                ) as p ON s.cblscheduleno=p.cblscheduleno
                ORDER BY assignedto,scheduledate
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ssssss", $startdate,$enddate,$startdate,$enddate,$startdate,$enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_individual_schedule_data($dbcon, $assignedto,$startdate,$enddate){
        $sql = "SELECT s.backlogno,
                    b.channelno, (SELECT channeltitle FROM msg_channel WHERE channelno=b.channelno) as channeltitle,
                    assignedto,(SELECT CONCAT(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=s.assignedto) as assignee,
                    scheduledate as startdate,
                    DATE_ADD(scheduledate, INTERVAL (duration-1) DAY) as enddate,
                    extendeddate,
                    lastprogressdate
                FROM (SELECT *
                        FROM asp_cblschedule
                        WHERE assignedto=? AND ((scheduledate BETWEEN ? AND ?) OR (DATE_ADD(scheduledate, INTERVAL (duration-1) DAY) BETWEEN ? AND ?) OR (scheduledate<? AND DATE_ADD(scheduledate, INTERVAL (duration-1) DAY)>?))
                    ) as s
                    INNER JOIN (SELECT cblscheduleno, deadline as extendeddate
                                FROM asp_deadlines
                                WHERE dno IN (SELECT max(dno)
                                            FROM asp_deadlines
                                            GROUP BY cblscheduleno)
                                ) as d ON s.cblscheduleno=d.cblscheduleno
                    INNER JOIN asp_channelbacklog as b ON s.backlogno=b.backlogno
                    LEFT JOIN (SELECT cblscheduleno, DATE(progresstime) as lastprogressdate
                                FROM asp_cblprogress
                                WHERE cblprogressno IN (SELECT max(cblprogressno)
                                            FROM asp_cblprogress
                                            GROUP BY cblscheduleno)
                                ) as p ON s.cblscheduleno=p.cblscheduleno
                ORDER BY assignedto,scheduledate
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("issssss", $assignedto,$startdate,$enddate,$startdate,$enddate,$startdate,$enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }
