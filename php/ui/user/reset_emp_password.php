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
        }else{
            throw new \Exception("You must select a user to reset password!", 1);
        }

        if (isset($_POST['password']) && strlen($_POST['password'])>6) {
            $password = trim(strip_tags($_POST['password']));
        }else{
            throw new \Exception("Password cannot be less than 6 characters!", 1);
        }

        $retype_password="";
        if (isset($_POST['retype_password']) && strlen($_POST['retype_password'])>0) {
            $retype_password = trim(strip_tags($_POST['retype_password']));
        }

        if(strcmp($password,$retype_password)!=0){
            throw new \Exception("Password and Retype Password must be equal!", 1);
        }else{
            $passphrase = password_hash($password, PASSWORD_DEFAULT);
        }

        $nos=update_user_password($dbcon, $passphrase,$userno);
        if($nos>0){
            $response['error'] = false;
            $response['message'] = "Password is reset.";
        }else{
            $response['error'] = true;
            $response['message'] = "Cannot Reset Password!";
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

    function update_user_password($dbcon, $passphrase,$userno){
        $sql = "UPDATE hr_user
                SET passphrase=?
                WHERE userno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("si", $passphrase,$userno);
        $stmt->execute();
        return $stmt->affected_rows;
    }
