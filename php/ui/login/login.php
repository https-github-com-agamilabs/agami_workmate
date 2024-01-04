<?php

    $response = array();
    if($_SERVER['REQUEST_METHOD']!='POST'){
        $response['error'] = true;
        $response['message'] = "Invalid Request method";
        echo json_encode($response);
		exit();
	}

    if(!isset($_POST['action']) || !isset($_POST['captchatoken']) || !isset($_POST['username']) || !isset($_POST['password'])){
        $response['error'] = true;
        $response['message'] = "Required field missing!";
        echo json_encode($response);
        exit();
    }


    $captchatoken = trim(strip_tags($_POST['captchatoken']));
    $secret = '6Ld1TUUpAAAAAH1f2pY5OAvqm6KmLeCizgj4lcqS';
    $action = trim(strip_tags($_POST['action']));
    // now you need do a POST requst to google recaptcha server.
    // url: https://www.google.com/recaptcha/api/siteverify.
    // with data secret:$secret and response:$token
    $data = array(
                'secret' => $secret,
                'response' => $captchatoken
            );

    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    $reCaptcharesponse = curl_exec($verify);

    //echo $reCaptcharesponse;
    $result = json_decode($reCaptcharesponse, true);

    if(!$result){
        $response['error'] = true;
        $response['message'] = "Please try again later.";
        echo json_encode($response);
        exit();
    }

    if($result['score']<=0.09){
        $response['error'] = true;
        $response['message'] = "Too many tries!";
        echo json_encode($response);
        exit();
    }

    $username = trim(strip_tags($_POST['username']));
    $password = trim(strip_tags($_POST['password']));

    if(strlen($username)<=0){
        $response['error'] = true;
        $response['message'] = "Invalid username!";
        echo json_encode($response);
		exit();
    }

    $base_path = dirname(dirname(dirname(__FILE__)));

    require_once($base_path."/db/Database.php");

    $db = new Database();
    $dbcon = $db->db_connect();

    if(!$db->is_connected()){
        $response['error'] = true;
        $response['message'] = "Database is not connected!";
        echo json_encode($response);
        exit();
    }

    $userLoginResult = get_user_info($dbcon,$username);

    if ($userLoginResult->num_rows==1) {
        $row = $userLoginResult->fetch_array(MYSQLI_ASSOC);
        $userno = $row['userno'];
        $passphrase = $row['passphrase'];

        if(password_verify($password, $passphrase)){
            session_start();

            $_SESSION['cogo_userno'] = $row['userno'];
            $_SESSION['cogo_firstname'] = $row['firstname'];
            $_SESSION['cogo_lastname'] = $row['lastname'];
            $_SESSION['cogo_ucatno'] = $row['ucatno'];
            $_SESSION['cogo_ucattitle'] = $row['ucattitle'];
            $_SESSION['cogo_email'] = $row['email'];
            $_SESSION['cogo_designation'] = $row['jobtitle'];
            $_SESSION['cogo_permissionlevel'] = $row['permissionlevel'];

            $response['error'] = false;
            $response['ucatno'] = $row['ucatno'];
            $response['message'] = "Login successful!";

        }else{
            $response['error'] = true;
            $response['message'] = "Invalid login credential!";
        }
    }else{
          $response['error'] = true;
          $response['message'] = "User is not valid!";
    }

	echo json_encode($response);

    $dbcon->close();

    /**
     * Local Function
     */

    function get_user_info($dbcon,$username){
        $sql="SELECT userno,username,firstname,lastname,
                    affiliation,jobtitle,email,primarycontact,
                    passphrase,createtime,lastupdatetime,
                    ucatno, (SELECT ucattitle FROM hr_usercat WHERE ucatno=u.ucatno) as ucattitle,
                    permissionlevel,isactive
               FROM hr_user as u
               WHERE isactive=1 AND username=?";
       $stmt = $dbcon->prepare($sql);
       $stmt->bind_param("s", $username);
       $stmt->execute();
       $result = $stmt->get_result();
       $stmt->close();

       return $result;
    }

?>
