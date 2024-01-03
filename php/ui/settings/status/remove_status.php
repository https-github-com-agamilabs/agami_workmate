<?php
    include_once  dirname(dirname(dirname(__FILE__)))."/login/check_session.php";

    $response = array();
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        $response['error'] = true;
        $response['message'] = "Invalid Request method";
        echo json_encode($response);
        exit();
    }

    try {

        $base_path = dirname(dirname(dirname(dirname(__FILE__))));
        require_once($base_path."/db/Database.php");

        $db = new Database();
        $dbcon=$db->db_connect();
        if (!$db->is_connected()) {
            throw new \Exception("Database is not connected!", 1);
        }

        if (isset($_POST['statusno'])) {
            $statusno = (int) $_POST['statusno'];
        } else{
            throw new \Exception("No Status Selected!", 1);
        }

        $status = del_status($dbcon,$statusno);

        if ($status > 0) {
            $response['error'] = false;
            $response['message'] = "Status info is removed.";
        } else {
            $response['error'] = true;
            $response['message'] = "Cannot Delete Status Info!";
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
    function del_status($dbcon,$statusno){
        $sql = "DELETE
                FROM msg_status
                WHERE statusno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $statusno);
        $stmt->execute();
        return $stmt->affected_rows;
    }
