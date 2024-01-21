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

        if (isset($_POST['scheduledate'])) {
            $scheduledate = trim(strip_tags($_POST['scheduledate']));
        }else{
            date_default_timezone_set("Asia/Dhaka");
            $scheduledate = date('Y-m-d');
        }

        $results = get_my_task_todo($dbcon, $userno, $scheduledate);

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
    //task-to-do-today={wstatusno<3@progress on today,to-do backlog@schedule for him but not@progress}
    //asp_cblschedule(cblscheduleno,backlogno,howto,assignedto, assigntime,scheduledate,userno)
    //asp_cblprogress(cblprogressno,cblscheduleno,progresstime,result,wstatusno,percentile,userno)
    function get_my_task_todo($dbcon, $assignedto, $scheduledate){
        $sql = "SELECT DISTINCT cblscheduleno
                FROM
                    (SELECT cblscheduleno
                    FROM asp_cblprogress
                    WHERE userno=? AND wstatusno<3
                    UNION
                    SELECT cblscheduleno
                    FROM asp_cblschedule
                    WHERE assignedto=? AND scheduledate>=?
                    )
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iis", $assignedto, $assignedto,$scheduledate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }
