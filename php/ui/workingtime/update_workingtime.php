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

        if (isset($_POST['timeno']) && strlen($_POST['timeno'])>0) {
            $timeno = (int) $_POST['timeno'];
        } else{
            throw new \Exception("You must select a particular time record!", 1);
        }

        if (isset($_POST['starttime']) && strlen($_POST['starttime'])>0) {
            $starttime = trim(strip_tags($_POST['starttime']));
        } else{
            throw new \Exception("Start time cannot be empty!", 1);
        }

        $endtime=NULL;
        if (isset($_POST['endtime']) && strlen($_POST['endtime'])>0) {
            $endtime = trim(strip_tags($_POST['endtime']));
        }

        $comment=NULL;
        if (isset($_POST['comment']) && strlen($_POST['comment'])>0) {
            $comment = trim(strip_tags($_POST['comment']));
        }

        $dnos = update_a_workingtime($dbcon,$starttime,$endtime,$comment,$timeno);

        if ($dnos > 0) {
            $response['error'] = false;
            $response['message'] = "Time Record is Updated.";
        } else {
            $response['error'] = true;
            $response['message'] = "Cannot Update Time Record!";
        }
    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);

    $dbcon->close();

    /**
     * Local Function
     */

    //emp_workingtime(timeno, empno, starttime, endtime, comment, isaccepted)
    function update_a_workingtime($dbcon,$starttime,$endtime,$comment,$timeno){
        $sql = "UPDATE emp_workingtime
                SET starttime=?, endtime=?,comment=?
                WHERE timeno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("sssi", $starttime,$endtime,$comment,$timeno);
        $stmt->execute();
        return $stmt->affected_rows;
    }
