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

        $isactive=1;
        if (isset($_POST['isactive'])) {
            $isactive = (int) $_POST['isactive'];
        }

        $showmyinfo=0;
        if (isset($_POST['showmyinfo'])) {
            $showmyinfo = (int) $_POST['showmyinfo'];
        }

        $selected_user = -1;
        if($showmyinfo>0){
            $selected_user = $userno;
        }

        $list = get_all_users($dbcon, $ucatno, $selected_user, $isactive);

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

    function get_all_users($dbcon, $ucatno, $selected_user, $isactive){

        $params = array();
        $types = "";
        $dataset = " WHERE 1 ";
        $count = 0;

        if($ucatno>0){
            $params[] = &$ucatno;
            $count++;
            $dataset .= " AND uo.ucatno=?";
            $types .= 'i';
        }

        if($selected_user>0){
            $params[] = &$selected_user;
            $count++;
            $dataset .= " AND u.userno=?";
            $types .= 'i';
        }

        if($isactive>=0){
            $params[] = &$isactive;
            $count++;
            $dataset .= " AND u.isactive=?";
            $types .= 'i';
        }

        $sql = "SELECT u.userno,username,firstname,lastname,photo_url,
                        email,primarycontact,
                        uo.ucatno,(SELECT ucattitle FROM hr_usercat WHERE ucatno=uo.ucatno) as ucattitle,
                        uo.supervisor,(SELECT CONCAT(firstname,' ', lastname) FROM hr_user s WHERE s.userno=uo.supervisor) as supervisor_name,
                        uo.permissionlevel,
                        createtime,lastupdatetime,u.isactive
                FROM hr_user as u
                    INNER JOIN com_userorg as uo ON u.userno=uo.userno
                $dataset
                ORDER BY u.isactive DESC,u.userno DESC";

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


