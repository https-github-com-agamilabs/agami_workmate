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

        if (isset($_POST['userno'])) {
            $userno = (int) $_POST['userno'];
        } else{
            throw new \Exception("No User Selected!", 1);
        }

        $dnos = toggle_user_activation($dbcon,$userno);

        if ($dnos > 0) {
            $response['error'] = false;
            $response['message'] = "User Activation is just Toggled.";
        } else {
            $response['error'] = true;
            $response['message'] = "Cannot Toggle User Activation!";
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
    function toggle_user_activation($dbcon,$userno){
        $sql = "UPDATE hr_user
                SET isactive=(1-isactive)
                WHERE userno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $userno);
        $stmt->execute();
        return $stmt->affected_rows;
    }
