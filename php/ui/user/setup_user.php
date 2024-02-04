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

        $userno=-1;
        if (isset($_POST['userno'])) {
            $userno = (int) $_POST['userno'];
        }

        //username,firstname,lastname,affiliation,jobtitle,email,primarycontact,passphrase

        $username = NULL;
        if (isset($_POST['username']) && strlen($_POST['username'])>0) {
            $username = trim(strip_tags($_POST['username']));
        }else{
            if($userno<=0){
                throw new \Exception("User Name cannot be Empty!", 1);
            }
        }

        $firstname = NULL;
        if (isset($_POST['firstname']) && strlen($_POST['firstname'])>0) {
            $firstname = trim(strip_tags($_POST['firstname']));
        }else{
            if($userno<=0){
                throw new \Exception("User First Name cannot be Empty!", 1);
            }
        }

        $lastname=NULL;
        if (isset($_POST['lastname']) && strlen($_POST['lastname'])>0) {
            $lastname = trim(strip_tags($_POST['lastname']));
        }

        $affiliation=NULL;
        if (isset($_POST['affiliation']) && strlen($_POST['affiliation'])>0) {
            $affiliation = trim(strip_tags($_POST['affiliation']));
        }

        $jobtitle=NULL;
        if (isset($_POST['jobtitle']) && strlen($_POST['jobtitle'])>0) {
            $jobtitle = trim(strip_tags($_POST['jobtitle']));
        }

        $photo_url=NULL;
        if (isset($_POST['photo_url']) && strlen($_POST['photo_url'])>0) {
            $photo_url = trim(strip_tags($_POST['photo_url']));
        }

        $email=NULL;
        if (isset($_POST['email']) && strlen($_POST['email'])>0) {
            $email = trim(strip_tags($_POST['email']));
        }

        $primarycontact=NULL;
        if (isset($_POST['primarycontact']) && strlen($_POST['primarycontact'])>0) {
            $primarycontact = trim(strip_tags($_POST['primarycontact']));
        }

        if($userno<0){ //password operation is performed during insert, not during update
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
        }

        if($userno>0){
            $nos=update_user($dbcon, $username,$firstname,$lastname,
                                    $affiliation,$jobtitle,$photo_url,
                                    $email,$primarycontact,
                                    $userno);
            if($nos>0){
                $response['error'] = false;
                $response['message'] = "User is Updated.";
            }else{
                $response['error'] = true;
                $response['message'] = "Cannot Update User!";
            }
        }else{
            $userno=insert_user($dbcon, $username,$firstname,$lastname,
                                    $affiliation,$jobtitle,$photo_url,
                                    $email,$primarycontact,$passphrase);
            if($userno>0){
                $response['error'] = false;
                $response['message'] = "User is Added.";
            }else{
                $response['error'] = true;
                $response['message'] = "Cannot Add User!";
            }
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

    function insert_user($dbcon, $username,$firstname,$lastname,
                                $affiliation,$jobtitle,$photo_url,
                                $email,$primarycontact,$passphrase){

        $sql = "INSERT INTO hr_user(
                                username,firstname,lastname,
                                affiliation,jobtitle,photo_url,
                                email,primarycontact,passphrase
                            )
                VALUES(?,?,?,?,?,?,?,?,?)";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("sssssssss",$username,$firstname,$lastname,
                            $affiliation,$jobtitle,$photo_url,
                            $email,$primarycontact,$passphrase);
        $stmt->execute();
        return $stmt->insert_id;


    }

    function update_user($dbcon, $username,$firstname,$lastname, 
                                $affiliation,$jobtitle,$photo_url, 
                                $email,$primarycontact,$userno){

        $params = array();
        $types = array();
        $dataset = array();
        if(!is_null($username)){
            $dataset[] ="username=?";
            $params[] = &$username;
            $types[] = "s";
        }

        if(!is_null($firstname)){
            $dataset[] ="firstname=?";
            $params[] = &$firstname;
            $types[] = "s";
        }

        if(!is_null($lastname)){
            $dataset[] ="lastname=?";
            $params[] = &$lastname;
            $types[] = "s";
        }

        if(!is_null($affiliation)){
            $dataset[] ="affiliation=?";
            $params[] = &$affiliation;
            $types[] = "s";
        }

        if(!is_null($jobtitle)){
            $dataset[] ="jobtitle=?";
            $params[] = &$jobtitle;
            $types[] = "s";
        }

        if(!is_null($photo_url)){
            $dataset[] ="photo_url=?";
            $params[] = &$photo_url;
            $types[] = "s";
        }

        if(!is_null($email)){
            $dataset[] ="email=?";
            $params[] = &$email;
            $types[] = "s";
        }

        if(!is_null($primarycontact)){
            $dataset[] ="primarycontact=?";
            $params[] = &$primarycontact;
            $types[] = "s";
        }

        if(strlen($userno)){
            $params[] = &$userno;
            $types[] = "s";
        }

        if(!count($dataset)){
            return -1;
        }

        $dataset = implode(", ", $dataset);
        $sql = "UPDATE hr_user
                SET $dataset
                WHERE userno=?";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param(implode("", $types), ...$params);
        $stmt->execute();
        return $stmt->affected_rows;
    }
