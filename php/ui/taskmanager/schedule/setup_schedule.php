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

        //cblscheduleno,backlogno,howto,takentime,scheduledate,userno
        //$userno=1;

        if(!isset($_SESSION['wm_orgno'])){
            throw new \Exception("You must select an organization!", 1);
        }else{
            $orgno= (int) $_SESSION['wm_orgno'];
        }

        $cblscheduleno=-1;
        if (isset($_POST['cblscheduleno'])) {
            $cblscheduleno = (int) $_POST['cblscheduleno'];
        }

        if (isset($_POST['backlogno'])) {
            $backlogno = (int) $_POST['backlogno'];
        }else{
            throw new \Exception("Backlog cannot be empty!", 1);
        }

        $howto = NULL;
        if (isset($_POST['howto'])) {
            $howto = trim($_POST['howto']);
        }

        if($my_permissionlevel<1 && $ucatno<19){
            throw new \Exception("You cannot assign task to employee!", 1);
        }elseif($my_permissionlevel==1){
            $assignedto = $userno;
        }else{
            if (isset($_POST['assignedto'])) {
                $assignedto = (int) $_POST['assignedto'];
            }else{
                throw new \Exception("You must select employee to assign a task!", 1);
            }
        }

        if (isset($_POST['scheduledate'])) {
            $scheduledate = trim(strip_tags($_POST['scheduledate']));
        }else{
            if($cblscheduleno<0)
                throw new \Exception("Deadline cannot be empty!", 1);
        }

        $duration = 1.0;
        if (isset($_POST['duration'])) {
            $duration = (double) $_POST['duration'];
        }

        if($cblscheduleno>0){

            if(isset($_POST['scheduledate']) || isset($_POST['duration'])){
                $dbcon->begin_transaction();
                $result=update_schedule_detail($dbcon, $backlogno,$howto,$assignedto,$scheduledate,$duration,$userno,$cblscheduleno);
                if($result>0){
                    $dnos=delete_deadline($dbcon, $cblscheduleno);
                    $deadline=date('Y-m-d', strtotime($scheduledate. ' + '.($duration-1).' days'));
                    $dno=create_deadline($dbcon, $cblscheduleno,$deadline,$userno);
                    $response['error'] = false;
                    $response['message'] = "Schedule is Successfully Updated.";
                }else{
                    $dbcon->rollback();
                    throw new \Exception("Cannot Update Schedule.", 1);
                }
                $dbcon->commit();
            }else{
                $result=update_schedule_nontime($dbcon, $backlogno,$howto,$assignedto,$userno,$cblscheduleno);
                if($result>0){
                    $response['error'] = false;
                    $response['message'] = "Schedule is Successfully Updated.";
                }else{
                    throw new \Exception("Cannot Update Schedule.", 1);
                }
            }
        }else{
            $dbcon->begin_transaction();
            $result=create_schedule($dbcon, $backlogno,$howto,$assignedto,$scheduledate,$duration,$userno);
            if($result>0){
                $deadline=date('Y-m-d', strtotime($scheduledate. ' + '.($duration-1).' days'));
                $dno=create_deadline($dbcon, $result,$deadline,$userno);
                if($dno<=0){
                    $dbcon->rollback();
                    throw new \Exception("Deadline Error! Cannot Update Schedule.", 1);
                }

                $wno=insert_watchlist($dbcon, $assignedto,$backlogno, $orgno);


                $rs_to = get_userinfo($dbcon,$assignedto);
                if($rs_to->num_rows>0){
                    $trow = $rs_to->fetch_array(MYSQLI_ASSOC);
                    $to=$trow['email'];
                    $greet='Hi '.$trow['firstname'].' '.(isset($trow['lastname'])?$trow['lastname']:'');
                    
                    $from = "info@agamilabs.com";
                    $cc =  "khanam.toslima@gmail.com";
                    $message=build_message($dbcon, $backlogno, $howto, $scheduledate, $duration, $greet);
                    $is_sent=sendMail($from, $to, $cc, $subject, $message);
                }

                $response['error'] = false;
                if($is_sent){
                    $response['message'] = "Schedule is Successfully added and email is sent.";
                }else{
                    $response['message'] = "Schedule is Successfully added";
                }
            }else{
                $dbcon->rollback();
                throw new \Exception("Cannot Add Schedule.", 1);
            }
            $dbcon->commit();
        }

    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();

    //asp_cblschedule(cblscheduleno,backlogno,howto,assignedto, assigntime,scheduledate,userno)
    function create_schedule($dbcon, $backlogno,$howto,$assignedto,$scheduledate,$duration,$userno){
        date_default_timezone_set("Asia/Dhaka");
        $assigntime = date("Y-m-d H:i:s");

        $sql = "INSERT INTO asp_cblschedule(backlogno,howto,assignedto,assigntime,scheduledate,duration,userno)
                VALUES(?,?,?,?,?,?,?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("isissdi", $backlogno,$howto,$assignedto,
                                    $assigntime,$scheduledate,$duration,$userno);
        $stmt->execute();
        $result=$stmt->insert_id;
        $stmt->close();
        return $result;
    }

    function update_schedule_detail($dbcon, $backlogno,$howto,$assignedto,$scheduledate,$duration,$userno,$cblscheduleno){

        $sql = "UPDATE asp_cblschedule
                SET backlogno=?, howto=?, assignedto=?,scheduledate=?,
                    duration=?,userno=?
                WHERE cblscheduleno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("isisdii", $backlogno, $howto,$assignedto,$scheduledate,
                                     $duration,$userno, $cblscheduleno);
        $stmt->execute();
        $result=$stmt->affected_rows;
        $stmt->close();
        return $result;
    }

    function update_schedule_nontime($dbcon, $backlogno,$howto,$assignedto,$userno,$cblscheduleno){

        $sql = "UPDATE asp_cblschedule
                SET backlogno=?, howto=?, assignedto=?,userno=?
                WHERE cblscheduleno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("isiii", $backlogno, $howto,$assignedto,
                                     $userno, $cblscheduleno);
        $stmt->execute();
        $result=$stmt->affected_rows;
        $stmt->close();
        return $result;
    }

    function create_deadline($dbcon, $cblscheduleno,$deadline,$userno){
        date_default_timezone_set("Asia/Dhaka");
        $entrytime = date("Y-m-d H:i:s");

        $sql = "INSERT INTO asp_deadlines(cblscheduleno,deadline,entrytime,userno)
                VALUES(?,?,?,?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("issi", $cblscheduleno,$deadline,$entrytime,$userno);
        $stmt->execute();
        $result=$stmt->insert_id;
        $stmt->close();
        return $result;
    }

    function delete_deadline($dbcon, $cblscheduleno){
        $sql = "DELETE
                FROM asp_deadlines
                WHERE cblscheduleno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $cblscheduleno);
        $stmt->execute();
        $result=$stmt->affected_rows;
        $stmt->close();
        return $result;
    }

    // asp_watchlist(userno,backlogno,createtime)
    function insert_watchlist($dbcon, $userno,$backlogno, $orgno)
    {
        date_default_timezone_set("Asia/Dhaka");
        $createtime = date('Y-m-d H:i:s');

        $sql = "INSERT INTO asp_watchlist(orgno,userno,backlogno,createtime)
                VALUES(?,?,?,?)";
        $stmt = $dbcon->prepare($sql);
        if ($dbcon->error) {
            echo $dbcon->error;
        }
        $stmt->bind_param("iiis", $orgno,$userno,$backlogno, $createtime);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    //hr_user(userno,username,firstname,lastname,affiliation,jobtitle,photo_url,email,primarycontact,passphrase,authkey,createtime,lastupdatetime,isactive,userstatusno)
    function get_userinfo($dbcon,$userno){
        $sql = "SELECT firstname,lastname,email
                FROM hr_user
                WHERE userno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $userno);
        $stmt->execute();
        return $stmt->get_result();
    }

    //asp_channelbacklog(backlogno,channelno,story,storytype,prioritylevelno,relativepriority,storyphaseno,parentbacklogno,approved,accessibility,lastupdatetime,userno)
    //asp_prioritylevel(prioritylevelno,priorityleveltitle,priorityleveldescription, colorno)
    function build_message($dbcon, $backlogno, $howto, $scheduledate, $duration, $greet){
        $sql = "SELECT story,
                        prioritylevelno,(SELECT priorityleveltitle FROM asp_prioritylevel WHERE prioritylevelno=b.prioritylevelno) as priorityleveltitle
                FROM asp_channelbacklog as b
                WHERE backlogno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $backlogno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $message = $greet.",<br>The following task has been assigned to you:<br>";
        if($result->num_rows>0){
            $srow = $result->fetch_array(MYSQLI_ASSOC);
            $story=$srow['story'];
            $priority=$srow['priorityleveltitle'];
            
            $message .= "Task: ".$story."br>";
            $message .= "Priority: ".$priority."<br>";
            $message .= isset($howto)?"How-to: ".$howto."<br>":"";
            $message .= "Start-date: ".$scheduledate."<br>";
            $message .= "Deadline: In ".$duration." day(s). <br>";

            $message .= "<br><br>Your task may affect our customer\'s expectation. <br><br>Therefore, you are obliged to inform early with explanation if you cannot complete the task in-time.<br>We appreciate your understanding.";
            $message .= "<br><br>With best wishes,<br>AGAMiLabs Limited.";
        }
    }

    function sendMail($from, $to, $cc, $subject, $message){
        //Validated
        if (!isset($from) || !validateEmail($from)) return false;
        if (!isset($to) || !validateEmail($to)) return false;
        if (!isset($subject)) $subject = "A task is assigned to you from Workmate";

        if (!isset($message)) {
            $message = "Please login workmate to check your task in detail.";
        }

        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // More headers
        $headers .= 'From: ' . $from . "\r\n";
        if (isset($cc) && strlen($cc)>0) {
            $headers .= 'Cc: ' . $cc . "\r\n";
        }

        return mail($to, $subject, $message, $headers);
    }

    
    function validateEmail($email)
    {
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        return true;
    }

    
