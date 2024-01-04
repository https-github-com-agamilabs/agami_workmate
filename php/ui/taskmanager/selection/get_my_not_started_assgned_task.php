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


        $results = get_my_not_started_assigned_task($dbcon, $userno);
        $results_array = array();
        if ($results->num_rows > 0) {
            while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
                $results_array[] = $row;
            }
        }
        $response['not_started'] = $results_array;

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

    function get_my_not_started_assigned_task($dbcon, $userno){
        $sql = "SELECT channelno,(SELECT channeltitle FROM msg_channel WHERE channelno=b.channelno) as channeltitle,
                        b.backlogno,story,storytype,storytypetitle, prioritylevelno, relativepriority,
                        howto,assigntime,scheduledate,
                        s.cblscheduleno
                FROM
                    (SELECT cblscheduleno,backlogno,howto,assigntime,scheduledate,duration
                    FROM asp_cblschedule
                    WHERE assignedto=? AND cblscheduleno NOT IN(
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
        $stmt->bind_param("iii", $userno,$userno,$userno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }
