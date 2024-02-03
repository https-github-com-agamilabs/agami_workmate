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

        if(!isset($_SESSION['cogo_orgno'])){
            throw new \Exception("You must select an organization!", 1);
        }else{
            $orgno= (int) $_SESSION['cogo_orgno'];
        }

        if(isset($_POST['startdate'])){
            $startdate=trim(strip_tags($_POST['startdate']));
        }else{
            throw new \Exception("You must specify start date!", 1);
        }

        if(isset($_POST['enddate'])){
            $enddate=trim(strip_tags($_POST['enddate']));
        }else{
            throw new \Exception("You must specify end date!", 1);
        }

        $list = get_all_elapsedtime($dbcon, $startdate, $enddate, $orgno);

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
    function get_all_elapsedtime($dbcon, $startdate, $enddate, $orgno)
    {
        $sql = "SELECT empno,workingdate,sum(elapsedtime) as dailyelapsedtime
                FROM (
                        (SELECT empno, date(starttime) as workingdate,
                                CASE
                                    WHEN day(starttime)!=day(endtime)
                                        THEN TIMESTAMPDIFF(SECOND,starttime,date(endtime))
                                    ELSE TIMESTAMPDIFF(SECOND,starttime, endtime)
                                END as elapsedtime
                        FROM emp_workingtime
                        WHERE orgno=? AND (date(starttime) BETWEEN ? AND ?)
                        )
                        UNION ALL
                        (SELECT empno, date(endtime) as workingdate,
                                TIMESTAMPDIFF(SECOND,date(endtime),endtime) as elapsedtime
                        FROM emp_workingtime
                        WHERE orgno=? AND day(starttime)!=day(endtime) AND (date(endtime) BETWEEN ? AND ?)
                        )
                    ) as dt
                GROUP BY empno,workingdate";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ississ", $orgno,$startdate, $enddate,$orgno,$startdate, $enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    /*
    (SELECT empno, date(starttime) as workingdate,
            CASE
                WHEN day(starttime)!=day(endtime)
                    THEN TIMESTAMPDIFF(MINUTE,starttime,date(endtime))
                ELSE TIMESTAMPDIFF(MINUTE,starttime, endtime)
            END as elapsedtime
    FROM emp_workingtime
    WHERE empno=?
    )
    UNION ALL
    (SELECT empno, date(endtime) as workingdate,
            TIMESTAMPDIFF(MINUTE,date(endtime),endtime) as elapsedtime
    FROM emp_workingtime
    WHERE day(starttime)!=day(endtime) AND empno=?
    );
    */


