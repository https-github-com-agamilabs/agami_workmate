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
        if($ucatno>=19 || $my_permissionlevel>=7){
            $results = get_all_employee($dbcon);
        }elseif($my_permissionlevel==3){
            // $results = get_my_fellow($dbcon,$userno);
            $results = get_all_employee($dbcon);
        }else{
            // $results = get_my_info($dbcon,$userno);
            $results = get_all_employee($dbcon);

        }
        $results_array = array();
        if ($results->num_rows > 0) {
            while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
                $results_array[] = $row;
            }
        }
        $response['results'] = $results_array;

    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();


    /*
    *   LOCAL FUNCTIONS
    */

    //hr_user(userno,username,firstname,lastname,affiliation,jobtitle,email,primarycontact,passphrase,ucatno,supervisor,permissionlevel,createtime,lastupdatetime,isactive)
    function get_my_info($dbcon,$userno){
        $sql = "SELECT userno, CONCAT(firstname,' ',lastname) as userfullname
                FROM hr_user
                WHERE userno=?
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $userno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_my_fellow($dbcon,$userno){
        $sql = "SELECT userno, CONCAT(firstname,' ',lastname) as userfullname
                FROM hr_user
                WHERE isactive=1 AND (userno in(SELECT userno FROM com_userorg WHERE supervisor=?) OR userno=?)
                ORDER BY firstname ASC
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ii", $userno,$userno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_all_employee($dbcon){
        $sql = "SELECT userno, CONCAT(firstname,' ',lastname) as userfullname
                FROM hr_user
                WHERE isactive=1
                ORDER BY firstname ASC
                ";
        $stmt = $dbcon->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }
