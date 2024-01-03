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

        $ucatno=-1;
        if (isset($_POST['ucatno'])) {
            $ucatno = (int) $_POST['ucatno'];
        }

        $isactive=-1;
        if (isset($_POST['isactive'])) {
            $isactive = (int) $_POST['isactive'];
        }

        $list = get_all_users($dbcon, $ucatno, $isactive);

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

    function get_all_users($dbcon, $ucatno, $isactive){

        $params = array();
        $types = "";
        $dataset = " WHERE 1 ";
        $count = 0;

        if($ucatno>0){
            $params[] = &$ucatno;
            $count++;
            $dataset .= " AND ucatno=?";
            $types .= 'i';
        }

        if($isactive>=0){
            $params[] = &$isactive;
            $count++;
            $dataset .= " AND isactive=?";
            $types .= 'i';
        }

        $sql = "SELECT userno,username,firstname,lastname,
                        affiliation,jobtitle,email,primarycontact,
                        ucatno,(SELECT ucattitle FROM hr_usercat WHERE ucatno=u.ucatno) as ucattitle,
                        supervisor,(SELECT CONCAT(firstname,' ', lastname) FROM hr_user s WHERE s.userno=u.supervisor) as supervisor_name,
                        permissionlevel,
                        createtime,lastupdatetime,isactive
                FROM hr_user as u
                $dataset
                ORDER BY isactive DESC,userno DESC";

        if( !$stmt = $dbcon->prepare($sql) )
            throw new Exception("Prepare statement failed: ".$dbcon->error);

        if(strlen($types)>0){
            call_user_func_array(array($stmt, "bind_param"), array_merge(array($types), $params));
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;

    }


