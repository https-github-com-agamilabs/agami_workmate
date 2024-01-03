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

        if (isset($_POST['timeno'])) {
            $timeno = (int) $_POST['timeno'];
        } else{
            throw new \Exception("You must select a particular time record!", 1);
        }

        $dnos = del_a_workingtime($dbcon,$timeno);

        if ($dnos > 0) {
            $response['error'] = false;
            $response['message'] = "Time record is removed.";
        } else {
            $response['error'] = true;
            $response['message'] = "Cannot Delete Time Record!";
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
    function del_a_workingtime($dbcon,$timeno){
        $sql = "DELETE
                FROM emp_workingtime
                WHERE timeno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $timeno);
        $stmt->execute();
        return $stmt->affected_rows;
    }
