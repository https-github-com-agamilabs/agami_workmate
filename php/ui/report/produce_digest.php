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

        if(isset($_SESSION['wm_orgno'])){
            $orgno=(int) $_SESSION['wm_orgno'];
        }else{
            throw new \Exception("You must selct an organization!", 1);
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
        
        //Organization wise employee task-and-time history
        $rs_company_wide_time=get_all_emp_elapsedtime($dbcon, $startdate, $enddate,$orgno);
        if ($rs_company_wide_time->num_rows > 0) {
            $meta_array = array();
            while ($row = $rs_company_wide_time->fetch_array(MYSQLI_ASSOC)) {
                $empno=$row['empno'];
                $workingdate=$row['workingdate'];
                
                //What is the task update by the user
                $taskupdate_array = array();
                $rs_taskupdate=get_emp_date_taskupdate($dbcon, $orgno,$workingdate,$userno);
                if ($rs_taskupdate->num_rows > 0) {
                    while ($trow = $rs_taskupdate->fetch_array(MYSQLI_ASSOC)) {
                        $taskupdate_array[] = $trow;
                    }
                }
                $row['taskupdate']=$taskupdate_array;

                //What is the textual update by the user
                $chatupdate_array = array();
                $rs_chatupdate=get_emp_date_chatupdate($dbcon, $orgno,$workingdate,$userno);
                if ($rs_chatupdate->num_rows > 0) {
                    while ($crow = $rs_chatupdate->fetch_array(MYSQLI_ASSOC)) {
                        $chatupdate_array[] = $crow;
                    }
                }
                $row['chatupdate']=$chatupdate_array;
                

                $meta_array[] = $row;
            }
            $response['error'] = false;
            $response['companywise'] = $meta_array;
        }

        //Special date note
        $specialdate_array = array();
        $rs_special=get_date_special($dbcon, $orgno, $startdate, $enddate);
        if ($rs_special->num_rows > 0) {
            while ($srow = $rs_special->fetch_array(MYSQLI_ASSOC)) {
                $specialdate_array[] = $srow;
            }
        }
        $response['specialdate']=$specialdate_array;

        //Workfor wise employee working history
        $workfor_array=array();
        $rs_workfor=get_all_workfor($dbcon, $orgno, $startdate, $enddate);
        if($rs_workfor->num_rows>0){
            while ($wrow = $rs_company_wide_time->fetch_array(MYSQLI_ASSOC)) {
                $workfor=$wrow['workfor'];

                $emp_array = array();
                $rs_workfor_wide_time=get_emp_elapsedtime_workfor($dbcon, $orgno, $workfor, $startdate, $enddate);
                if ($rs_company_wide_time->num_rows > 0) {
                    while ($row = $rs_company_wide_time->fetch_array(MYSQLI_ASSOC)) {
                        $empno=$row['empno'];
                        
                        //What is the task update by the user
                        $taskupdate_array = array();
                        $rs_taskupdate=get_all_emp_taskupdate($dbcon, $orgno,$startdate, $enddate,$userno);
                        if ($rs_taskupdate->num_rows > 0) {
                            while ($trow = $rs_taskupdate->fetch_array(MYSQLI_ASSOC)) {
                                $taskupdate_array[] = $trow;
                            }
                        }
                        $row['taskupdate']=$taskupdate_array;
        
                        //What is the textual update by the user
                        $chatupdate_array = array();
                        $rs_chatupdate=get_emp_date_chatupdate($dbcon, $orgno,$chatdate,$userno);
                        if ($rs_chatupdate->num_rows > 0) {
                            while ($crow = $rs_chatupdate->fetch_array(MYSQLI_ASSOC)) {
                                $chatupdate_array[] = $crow;
                            }
                        }
                        $row['chatupdate']=$chatupdate_array;
                        
                        $emp_array[] = $row;
                    }
                }
                $wrow['employee'] = $meta_array;
                $workfor_array[]=$wrow;
            }
            
            $response['workfor'] = $workfor_array;

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
    function get_all_emp_elapsedtime($dbcon, $startdate, $enddate,$orgno)
    {
        $sql = "SELECT empno,
                        (SELECT concat(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=dt.empno) as empfullname,
                        workingdate,sum(elapsedtime) as dailyelapsedtime
                FROM (
                        (SELECT empno, date(starttime) as workingdate,
                                CASE
                                    WHEN day(starttime)!=day(endtime) THEN TIMESTAMPDIFF(SECOND,starttime,date(endtime))
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
                GROUP BY empno,workingdate
                ORDER BY empno,workingdate";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ississ", $orgno,$startdate, $enddate,$orgno,$startdate, $enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }
    

    // asp_channelbacklog(backlogno,channelno,story,storytype,points,prioritylevelno,relativepriority,storyphaseno,parentbacklogno,approved,accessibility,lastupdatetime,userno)
    // asp_cblschedule(cblscheduleno,backlogno,howto,assignedto, assigntime,scheduledate,duration,userno)
    // asp_deadlines(dno,cblscheduleno,deadline,entrytime,userno)
    // asp_cblprogress(cblprogressno,cblscheduleno,progresstime,result,wstatusno,percentile, userno)
    function get_emp_date_taskupdate($dbcon, $orgno,$progressdate,$userno)
    {
        $sql = "SELECT  
                    p.userno, 
                    bs.channelno, (SELECT channeltitle FROM msg_channel WHERE channelno=bs.channelno) as channeltitle,
                    bs.backlogno,bs.story,
                    p.cblscheduleno,d.deadline,date(progresstime) as progressdate,
                    p.wstatusno,(SELECT statustitle FROM asp_workstatus WHERE wstatusno=p.wstatusno) as statustitle 
                FROM asp_cblprogress as p
                        LEFT JOIN 
                            (SELECT cblscheduleno,max(deadline) as deadline
                            FROM asp_deadlines
                            GROUP BY cblscheduleno) as d ON p.cblscheduleno=d.cblscheduleno
                        LEFT JOIN 
                            (SELECT s.cblscheduleno, assignedto, b.backlogno,channelno, b.story
                            FROM asp_cblschedule as s
                                INNER JOIN 
                                    (SELECT backlogno,channelno,story
                                    FROM asp_channelbacklog 
                                    WHERE channelno IN (
                                        SELECT DISTINCT channelno
                                        FROM msg_channel
                                        WHERE orgno=?
                                        )
                                    )as b ON s.backlogno=b.backlogno
                            ) as bs ON p.cblscheduleno=bs.cblscheduleno AND p.userno=bs.assignedto
                WHERE (date(p.progresstime) = ?)
                    AND p.userno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("isi", $orgno,$progressdate,$userno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_emp_date_chatupdate($dbcon, $orgno,$chatdate,$userno)
    {
        $sql = "SELECT userno,
                    channelno,(SELECT channeltitle FROM msg_channel WHERE channelno=b.channelno) as channeltitle,
                    backlogno,story
                FROM asp_channelbacklog as b
                WHERE userno=? 
                    AND parentbacklogno IS NOT NULL
                    AND (date(lastupdatetime) = ?)
                    AND channelno IN (
                        SELECT DISTINCT channelno
                        FROM msg_channel
                        WHERE orgno=?
                        )
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("is", $orgno,$chatdate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    //emp_specialdays(specialdayno, orgno, specialdate, reasontext, sdtypeid, minworkinghour)
    //emp_specialdaytype(sdtypeid, displaytitle, minworkinghour, color)
    function get_date_special($dbcon, $orgno, $startdate, $enddate)
    {
        $sql = "SELECT specialdate,reasontext,sdtypeid, minworkinghour
                FROM emp_specialdays as s
                WHERE orgno=? AND (specialdate  BETWEEN ? AND ?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iss", $orgno,$startdate, $enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    /*******
     * NOT YET FINALISED
     */
    function get_emp_elapsedtime_workfor($dbcon, $orgno, $workfor, $startdate, $enddate)
    {
        $sql = "SELECT empno,
                        (SELECT concat(firstname,' ',IFNULL(lastname,'')) FROM hr_user WHERE userno=wt.empno) as empfullname,
                        sum(TIMESTAMPDIFF(SECOND,starttime, endtime)) as totalelapsedtime
                FROM emp_workingtime as wt
                WHERE orgno=? AND workfor=? AND (date(starttime) BETWEEN ? AND ?)
                GROUP BY empno
                HAVING sum(TIMESTAMPDIFF(SECOND,starttime, endtime))>0
                ORDER BY empno";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iiss", $orgno,$workfor,$startdate, $enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_all_workfor($dbcon, $orgno, $startdate, $enddate)
    {
        $sql = "SELECT DISTINCT workfor
                FROM emp_workingtime as wt
                WHERE orgno=? AND (date(starttime) BETWEEN ? AND ?)
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("iss", $orgno,$startdate, $enddate);
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




