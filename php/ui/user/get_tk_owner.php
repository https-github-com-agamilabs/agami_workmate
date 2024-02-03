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

        if ($ucatno<=13) {
            $meta_array=array();
            $meta_array['userno']=$userno;
            $meta_array['firstname']=$_SESSION['cogo_firstname'];
            $meta_array['lastname']=$_SESSION['cogo_lastname'];
            $meta_array['email']=$_SESSION['cogo_email'];
            $response['results'][] = $meta_array;
            echo json_encode($response);
            $dbcon->close();
            exit();
        }

        if(!isset($_SESSION['cogo_orgno'])){
            throw new \Exception("You must select an organization!", 1);
        }else{
            $orgno= (int) $_SESSION['cogo_orgno'];
        }

        $list = get_tk_owner($dbcon, $orgno);

        if ($list->num_rows > 0) {
            $meta_array = array();
            while ($row = $list->fetch_array(MYSQLI_ASSOC)) {
                $meta_array[] = $row;
            }
            $response['error'] = false;
            $response['results'] = $meta_array;

        } else {
            $response['error'] = true;
            $response['results'] = array();
            $response['message'] = "No User Found!";
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

    function get_tk_owner($dbcon, $orgno){  
        $sql = "SELECT userno,firstname,lastname,email
                FROM hr_user as u
                WHERE isactive>=1 
                    AND userno IN
                        (SELECT DISTINCT userno
                        FROM com_userorg
                        WHERE orgno=? AND ucatno IN (13,18))
                ORDER BY userno DESC";

        $stmt = $dbcon->prepare($sql);
        //$stmt->bind_param("i", $userno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;

    }


