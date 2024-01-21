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

        //userno,cblprogressno,result,iscompleted,percentile
        //$userno=1;

        $cblprogressno=-1;
        if (isset($_POST['cblprogressno'])) {
            $cblprogressno = (int) $_POST['cblprogressno'];
        }

        if (isset($_POST['cblscheduleno'])) {
            $cblscheduleno = (int) $_POST['cblscheduleno'];
        }else{
            throw new \Exception("Work Schedule cannot be empty!", 1);
        }

        $result = NULL;
        if (isset($_POST['result'])) {
            $result = trim($_POST['result']);
        }

        if (isset($_POST['wstatusno'])) {
            $wstatusno = (int) $_POST['wstatusno'];
        }else{
            throw new \Exception("Work Status cannot be empty!", 1);
        }

        $percentile = 0;
        if (isset($_POST['percentile'])) {
            $percentile = (int) $_POST['percentile'];
        }

        if($cblprogressno>0){
            $result=update_schedule($dbcon, $cblscheduleno,$result,$wstatusno,$percentile,$userno,$cblprogressno);
            if($result>0){
                $response['error'] = false;
                $response['message'] = "Progress is Successfully Updated.";
            }else{
                throw new \Exception("Cannot Update Progress.", 1);
            }
        }else{
            if(is_approved($dbcon,$cblscheduleno)){
                $result=create_progress($dbcon, $cblscheduleno,$result,$wstatusno,$percentile,$userno);
                if($result>0){
                    $response['error'] = false;
                    $response['message'] = "Progress is Successfully Added.";
                }else{
                    throw new \Exception("Cannot Add Progress.", 1);
                }
            }else{
                throw new \Exception("The task is not yet approved by Admin. Contact them and try again.", 1);
            }
        }

    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();

    function is_approved($dbcon,$cblscheduleno){
        $sql = "SELECT backlogno
                FROM asp_channelbacklog
                WHERE backlogno IN (
                    SELECT DISTINCT backlogno
                    FROM asp_cblschedule
                    WHERE cblscheduleno=?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $cblscheduleno);
        $stmt->execute();
        $result=$stmt->get_result();
        $stmt->close();
        return $result->num_rows>0;
    }

    //asp_cblprogress(cblprogressno,cblscheduleno,progresstime,result,wstatusno,percentile,userno)
    function create_progress($dbcon, $cblscheduleno,$result,$wstatusno,$percentile,$userno){
        date_default_timezone_set("Asia/Dhaka");
        $progresstime = date("Y-m-d H:i:s");

        $sql = "INSERT INTO asp_cblprogress(cblscheduleno,progresstime,result,wstatusno,percentile,userno)
                VALUES(?,?,?,?,?,?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("issiii", $cblscheduleno,$progresstime,$result,$wstatusno,$percentile,$userno);
        $stmt->execute();
        $result=$stmt->insert_id;
        $stmt->close();
        return $result;
    }

    function update_schedule($dbcon, $cblscheduleno,$result,$wstatusno,$percentile,$userno,$cblprogressno){
        date_default_timezone_set("Asia/Dhaka");
        $progresstime = date("Y-m-d H:i:s");

        $sql = "UPDATE asp_cblprogress
                SET cblscheduleno=?, progresstime=?, result=?, wstatusno=?, percentile=?,userno=?
                WHERE cblprogressno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("issiiii", $cblscheduleno,$progresstime, $result,
                                    $wstatusno, $percentile,$userno,$cblprogressno);
        $stmt->execute();
        $result=$stmt->affected_rows;
        $stmt->close();
        return $result;

    }