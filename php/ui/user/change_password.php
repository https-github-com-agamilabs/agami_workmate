<?php
    include_once  dirname(dirname(__FILE__))."/login/check_session.php";

    $response = array();
    if($_SERVER['REQUEST_METHOD']!='POST'){
        $response['error'] = true;
        $response['message'] = "Invalid Request method";
        echo json_encode($response);
        exit();
    }

    try{
        if (isset($_POST['oldpassword']) && strlen($_POST['oldpassword'])>7) {
            $oldpassword = trim(strip_tags($_POST['oldpassword']));
        } else{
            throw new \Exception("Please enter your valid password!", 1);
        }

        if (isset($_POST['newpassword']) && strlen($_POST['newpassword'])>7) {
            $newpassword = trim(strip_tags($_POST['newpassword']));
        } else{
            throw new \Exception("Password cannot be Empty or less than 8 characters long!", 1);
        }

        if (isset($_POST['newconfirmpassword'])) {
            $newconfirmpassword = trim(strip_tags($_POST['newconfirmpassword']));
        } else{
            throw new \Exception("User Name cannot be Empty!", 1);
        }

        if(isset($_SESSION['wm_userno'])){
            $userno=(int) $_SESSION['wm_userno'];
        }else{
            throw new \Exception("You must login first!", 1);
        }


        if(strcmp($newpassword, $newconfirmpassword)!=0){
            throw new \Exception("New password and confirm password doesn't match!", 1);
        }

        $base_path = dirname(dirname(dirname(__FILE__)));
        require_once($base_path."/db/Database.php");

        $db = new Database();
        $dbcon = $db->db_connect();

        if(!$db->is_connected()){
            throw new \Exception("Database is not connected!", 1);
        }

        $result = getOldPassphraseOfAUser($dbcon, $userno);
        if($result->num_rows!=1){
            throw new \Exception("86: Please try again!", 1);
        }

        $oldPassphrase = $result->fetch_array(MYSQLI_ASSOC)['passphrase'];

        if(!password_verify($oldpassword, $oldPassphrase)){
            throw new \Exception("Old password doesn't match!", 1);
        }

        $newPassphrase = password_hash($newpassword, PASSWORD_DEFAULT);
        $updateResult = updatePassphraseOfAUser($dbcon, $newPassphrase, $userno);

        if ($updateResult<=0) {
            throw new \Exception("Your password can not be updated!", 1);
        }else{
            $response['error'] = false;
            $response['message'] = "Your password updated successfully!";
        }
    }catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }
	echo json_encode($response);

    $dbcon->close();

    function getOldPassphraseOfAUser($dbcon, $userno){
        $sql = "SELECT passphrase
                FROM hr_user
                WHERE userno=?";

        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $userno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function updatePassphraseOfAUser($dbcon, $newPassphrase, $userno){
        $sql = "UPDATE hr_user
                SET passphrase=?
                WHERE userno=?";

        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("si", $newPassphrase, $userno);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows;
    }
?>
