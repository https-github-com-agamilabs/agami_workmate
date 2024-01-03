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

        if (isset($_POST['username'])) {
            $username = trim(strip_tags($_POST['username']));
        } else{
            throw new \Exception("User Name cannot be Empty!", 1);
        }

        $base_path = dirname(dirname(dirname(__FILE__)));
        require_once($base_path."/db/Database.php");

        $db = new Database();
        $dbcon=$db->db_connect();
        if (!$db->is_connected()) {
            throw new \Exception("Database is not connected!", 1);
        }

        $result = get_user($dbcon,$username);

        if ($result->num_rows > 0) {
            $response['error'] = true;
            $response['message'] = "User Name Already Exists!";
        } else {
            $response['error'] = false;
            $response['message'] = "Congratulations! User name is available.";
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

    function get_user($dbcon,$username)
    {
        $sql = "SELECT userno
                FROM hr_user
                WHERE username=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }


