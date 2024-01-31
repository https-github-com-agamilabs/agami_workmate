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

        if(isset($_SESSION['cogo_userno'])){
            $empno=(int) $_SESSION['cogo_userno'];
        }else{
            throw new \Exception("You must login first!", 1);
        }

        if(isset($_SESSION['cogo_ucatno'])){
            $ucatno=(int) $_SESSION['cogo_ucatno'];
        }else{
            throw new \Exception("You must login first!", 1);
        }

        if(isset($_POST['monthno'])){
            $monthno=(int) $_POST['monthno'];
        }else{
            throw new \Exception("You must select a month!", 1);
        }

        if(isset($_POST['yearno'])){
            $yearno=(int) $_POST['yearno'];
        }else{
            throw new \Exception("Year must not be empty!", 1);
        }

        $dateRange = getStartAndEndDate($month, $year);
        $startdate=$dateRange['start_date'];
        $enddate=$dateRange['end_date'];

        if ($ucatno>=19) {
            if (isset($_POST['workfor']) && strlen($_POST['workfor'])>0) {
                $workfor=(int) $_POST['workfor'];
                $list = get_emp_elapsedtime_workfor($dbcon, $workfor, $startdate, $enddate);
            }else{
                $list= get_all_emp_elapsedtime($dbcon, $startdate, $enddate);
            }
        } else if($ucatno>=13){
            $list = get_emp_elapsedtime_workfor($dbcon, $empno, $startdate, $enddate);
        }else {
            $list = get_emp_elapsedtime($dbcon, $empno, $startdate, $enddate);
        }

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
    function get_emp_elapsedtime($dbcon, $empno, $startdate, $enddate)
    {
        $sql = "SELECT empno,
                        (SELECT concat(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=dt.empno) as empfullname,
                        workingdate,sum(elapsedtime) as dailyelapsedtime
                FROM (
                        (SELECT empno, date(starttime) as workingdate,
                                CASE
                                    WHEN day(starttime)!=day(endtime)
                                        THEN TIMESTAMPDIFF(SECOND,starttime,date(endtime))
                                    ELSE TIMESTAMPDIFF(SECOND,starttime, endtime)
                                END as elapsedtime
                        FROM emp_workingtime
                        WHERE empno IN
                            (   SELECT userno
                                FROM hr_user
                                WHERE isactive=1 AND (supervisor=? OR userno=?)
                            )
                        AND (date(starttime) BETWEEN ? AND ?)
                        )
                        UNION ALL
                        (SELECT empno, date(endtime) as workingdate,
                                TIMESTAMPDIFF(SECOND,date(endtime),endtime) as elapsedtime
                        FROM emp_workingtime
                        WHERE day(starttime)!=day(endtime)
                        AND empno IN
                            (   SELECT userno
                                FROM hr_user
                                WHERE isactive=1 AND (supervisor=? OR userno=?)
                            )
                        AND (date(endtime) BETWEEN ? AND ?)
                        )
                    ) as dt
                GROUP BY empno,workingdate
                ORDER BY empno,workingdate";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iissiiss", $empno,$empno,$startdate, $enddate,$empno,$empno,$startdate, $enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_all_emp_elapsedtime($dbcon, $startdate, $enddate)
    {
        $sql = "SELECT empno,
                        (SELECT concat(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=dt.empno) as empfullname,
                        workingdate,sum(elapsedtime) as dailyelapsedtime
                FROM (
                        (SELECT empno, date(starttime) as workingdate,
                                CASE
                                    WHEN day(starttime)!=day(endtime)
                                        THEN TIMESTAMPDIFF(SECOND,starttime,date(endtime))
                                    ELSE TIMESTAMPDIFF(SECOND,starttime, endtime)
                                END as elapsedtime
                        FROM emp_workingtime
                        WHERE (date(starttime) BETWEEN ? AND ?)
                        )
                        UNION ALL
                        (SELECT empno, date(endtime) as workingdate,
                                TIMESTAMPDIFF(SECOND,date(endtime),endtime) as elapsedtime
                        FROM emp_workingtime
                        WHERE day(starttime)!=day(endtime) AND (date(endtime) BETWEEN ? AND ?)
                        )
                    ) as dt
                GROUP BY empno,workingdate
                ORDER BY empno,workingdate";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ssss", $startdate, $enddate,$startdate, $enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_emp_elapsedtime_workfor($dbcon, $workfor, $startdate, $enddate)
    {
        $sql = "SELECT empno,
                        (SELECT concat(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=dt.empno) as empfullname,
                        workingdate,sum(elapsedtime) as dailyelapsedtime
                FROM (
                        (SELECT empno, date(starttime) as workingdate,
                                CASE
                                    WHEN day(starttime)!=day(endtime)
                                        THEN TIMESTAMPDIFF(SECOND,starttime,date(endtime))
                                    ELSE TIMESTAMPDIFF(SECOND,starttime, endtime)
                                END as elapsedtime
                        FROM emp_workingtime
                        WHERE workfor=? AND (date(starttime) BETWEEN ? AND ?)
                        )
                        UNION ALL
                        (SELECT empno, date(endtime) as workingdate,
                                TIMESTAMPDIFF(SECOND,date(endtime),endtime) as elapsedtime
                        FROM emp_workingtime
                        WHERE workfor=? AND day(starttime)!=day(endtime) AND (date(endtime) BETWEEN ? AND ?)
                        )
                    ) as dt
                GROUP BY empno,workingdate
                ORDER BY empno,workingdate";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ississ", $workfor,$startdate, $enddate,$workfor,$startdate, $enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function getStartAndEndDate($month, $year) {
        // Create a DateTime object for the first day of the given month and year
        $startDate = new DateTime("$year-$month-01");

        // Get the last day of the given month and year
        $endDate = new DateTime("$year-$month-" . $startDate->format('t'));

        return array(
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        );
    }

?>




