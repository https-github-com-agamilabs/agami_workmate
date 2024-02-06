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

        if(isset($_SESSION['wm_userno'])){
            $empno=(int) $_SESSION['wm_userno'];
        }else{
            throw new \Exception("You must login first!", 1);
        }

        if(isset($_SESSION['wm_orgno'])){
            $orgno=(int) $_SESSION['wm_orgno'];
        }else{
            throw new \Exception("You must selct an organization!", 1);
        }

        if(isset($_SESSION['wm_ucatno'])){
            $ucatno=(int) $_SESSION['wm_ucatno'];
        }else{
            throw new \Exception("You must login first!", 1);
        }

        if (isset($_POST['startdate'])) {
            $startdate=trim(strip_tags($_POST['startdate']));
        } else {
            throw new \Exception("You must specify start date!", 1);
        }

        if (isset($_POST['enddate'])) {
            $enddate=trim(strip_tags($_POST['enddate']));
        } else {
            throw new \Exception("You must specify end date!", 1);
        }

        $rs_org=get_org_info($dbcon, $orgno);
        if ($rs_org->num_rows > 0) {
            $response['org'] = $rs_org->fetch_array(MYSQLI_ASSOC);
        }
        

        if ($ucatno>=19) {
            if (isset($_POST['workfor']) && strlen($_POST['workfor'])>0) {
                $workfor=(int) $_POST['workfor'];
                $rs_workfor=get_workfor_info($dbcon, $orgno,$workfor);
                if ($rs_workfor->num_rows > 0) {
                    $response['workfor'] = $rs_workfor->fetch_array(MYSQLI_ASSOC);
                }

                $list = get_emp_elapsedtime_workfor($dbcon, $workfor, $startdate, $enddate);
            }else{
                $list= get_all_emp_elapsedtime($dbcon, $startdate, $enddate);
            }
        } else if($ucatno>=13){
            $rs_workfor=get_workfor_info($dbcon, $empno);
            if ($rs_workfor->num_rows > 0) {
                $response['workfor'] = $rs_workfor->fetch_array(MYSQLI_ASSOC);
            }
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
                        (SELECT concat(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=wt.empno) as empfullname,
                        sum(TIMESTAMPDIFF(SECOND,starttime, endtime)) as totalelapsedtime
                FROM emp_workingtime as wt
                WHERE empno=? AND (date(starttime) BETWEEN ? AND ?)
                GROUP BY empno
                HAVING sum(TIMESTAMPDIFF(SECOND,starttime, endtime))>0
                ORDER BY empno";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iss", $empno,$startdate, $enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_all_emp_elapsedtime($dbcon, $startdate, $enddate)
    {
        $sql = "SELECT empno,
                        (SELECT concat(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=wt.empno) as empfullname,
                        sum(TIMESTAMPDIFF(SECOND,starttime, endtime)) as totalelapsedtime
                FROM emp_workingtime as wt
                WHERE (date(starttime) BETWEEN ? AND ?)
                GROUP BY empno
                HAVING sum(TIMESTAMPDIFF(SECOND,starttime, endtime))>0
                ORDER BY empno";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ss", $startdate, $enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_emp_elapsedtime_workfor($dbcon, $workfor, $startdate, $enddate)
    {
        $sql = "SELECT empno,
                        (SELECT concat(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=wt.empno) as empfullname,
                        sum(TIMESTAMPDIFF(SECOND,starttime, endtime)) as totalelapsedtime
                FROM emp_workingtime as wt
                WHERE workfor=? AND (date(starttime) BETWEEN ? AND ?)
                GROUP BY empno
                HAVING sum(TIMESTAMPDIFF(SECOND,starttime, endtime))>0
                ORDER BY empno";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iss", $workfor,$startdate, $enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    //hr_user(userno,username,firstname,lastname,affiliation,jobtitle,email,primarycontact,passphrase,ucatno,supervisor,permissionlevel,createtime,lastupdatetime,isactive)
    //com_userorg (uono,orgno,userno,uuid,ucatno,supervisor,moduleno,designation,hourlyrate,monthlysalary,permissionlevel,dailyworkinghour,timeflexibility,shiftno,starttime,endtime,timezone,isactive)

    function get_workfor_info($dbcon, $orgno,$workfor)
    {
        $sql = "SELECT u.userno,uo.uuid,firstname,lastname,affiliation,jobtitle,uo.designation,email,primarycontact
                FROM hr_user as u
                    INNER JOIN (
                        SELECT userno,uuid, designation
                        FROM com_userorg 
                        WHERE orgno=? AND userno=?) as uo ON u.userno=uo.userno
                WHERE userno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iii", $orgno,$workfor,$workfor);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    //com_orgs (orgno, orgname, street, city, state, country, gpslat, gpslon, orgtypeid, privacy, picurl, primarycontact, orgnote, weekend1, weekend2, starttime, endtime, verifiedno)
    function get_org_info($dbcon, $orgno)
    {
        $sql = "SELECT orgno, orgname, street, city, state, country,picurl, primarycontact, orgnote
                FROM com_orgs 
                WHERE orgno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $orgno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }
?>




