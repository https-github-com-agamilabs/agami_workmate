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

        if (isset($_POST['channelno'])) {
            $channelno = (int) $_POST['channelno'];
        } else{
            throw new \Exception("No Channel/Project Selected!", 1);
        }

        if (isset($_POST['userno'])) {
            $userno = (int) $_POST['userno'];
        } else{
            throw new \Exception("No Member Selected!", 1);
        }


        $list = get_channel_members($dbcon, $channelno);

        if ($list->num_rows > 0) {
            $meta_array = array();
            while ($row = $list->fetch_array(MYSQLI_ASSOC)) {
                $meta_array[] = $row;
            }
            $response['error'] = false;
            $response['data'] = $meta_array;
        } else {
            $response['error'] = true;
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

    function get_channel_members($dbcon, $channelno){
        $sql = "SELECT u.userno,firstname,lastname,
                        affiliation,jobtitle,email,primarycontact,
                        ucatno,(SELECT ucattitle FROM hr_usercat WHERE ucatno=u.ucatno) as ucattitle,
                        isactive, channelno, entrytime
                FROM hr_user as u
                    INNER JOIN (SELECT *
                                FROM msg_channelmember
                                WHERE channelno=?) as cm
                    ON u.userno=cm.userno
                ORDER BY isactive DESC, ucatno DESC, entrytime";

        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $channelno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }


