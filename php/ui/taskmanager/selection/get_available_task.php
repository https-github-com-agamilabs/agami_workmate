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

        $results = get_non_assigned_task($dbcon, $channelno);

        $results_array = array();
        if ($results->num_rows > 0) {
            while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
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
    //asp_channelbacklog(backlogno,channelno,story,points,storytype,prioritylevelno,relativepriority,storyphaseno,parentbacklogno,approved,accessibility,lastupdatetime,userno)
    //asp_cblschedule(cblscheduleno,backlogno,howto,assignedto, assigntime,scheduledate,userno)
    //asp_cblprogress(cblprogressno,cblscheduleno,progresstime,result,wstatusno,userno)
    function get_non_assigned_task($dbcon, $channelno){
        $sql = "SELECT backlogno, channelno, story,b.points,
                    storyphaseno,(SELECT storyphasetitle FROM asp_storyphase WHERE storyphaseno=b.storyphaseno) as storyphasetitle,
                    storytype,
                    prioritylevelno,(SELECT priorityleveltitle FROM asp_prioritylevel WHERE prioritylevelno=b.prioritylevelno) as priorityleveltitle,
                    relativepriority
                FROM asp_channelbacklog as b
                WHERE channelno=? AND backlogno NOT IN
                    (SELECT DISTINCT backlogno
                    FROM asp_cblschedule
                    WHERE backlogno IN (
                        SELECT backlogno
                        FROM asp_channelbacklog
                        WHERE channelno=?
                        )
                    )
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ii", $channelno,$channelno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }
