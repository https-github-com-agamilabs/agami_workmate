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

        if (isset($_SESSION['cogo_userno'])) {
            $empno=(int) $_SESSION['cogo_userno'];
        } else {
            throw new \Exception("You must login first!", 1);
        }

        if ($ucatno>10) {
            $list=get_main_channels($dbcon);
        } else {
            $list = get_emp_main_channels($dbcon, $empno);
        }

        if ($list->num_rows > 0) {
            $meta_array = array();
            while ($row = $list->fetch_array(MYSQLI_ASSOC)) {
                $channelno=$row['channelno'];

                if ($ucatno>10) {
                    $subchannel=get_sub_channels($dbcon,$channelno);
                }else{
                    $subchannel=get_emp_sub_channels($dbcon,$channelno,$empno);
                }

                $subchannel_array = array();
                if ($subchannel->num_rows > 0) {
                    while ($scrow = $subchannel->fetch_array(MYSQLI_ASSOC)) {
                        $schannelno=$scrow['channelno'];

                        $members=get_channel_members($dbcon, $schannelno);
                        $member_array = array();
                        while ($mrow = $members->fetch_array(MYSQLI_ASSOC)) {
                            $member_array[]=$mrow;
                        }
                        $scrow['members']=$member_array;

                        $subchannel_array[]=$scrow;
                    }
                }
                $row['subchannels']=$subchannel_array;

                $meta_array[] = $row;

            }
            $response['error'] = false;
            $response['data'] = $meta_array;
        } else {
            $response['error'] = true;
            $response['message'] = "No Channel/Project is Found!";
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

    function get_main_channels($dbcon)
    {
        $sql = "SELECT channelno,channeltitle,parentchannel
                FROM msg_channel
                WHERE parentchannel is NULL
                ORDER BY channelno";

        return $dbcon->query($sql);
    }

    function get_sub_channels($dbcon,$parentchannel)
    {
        $sql = "SELECT channelno,channeltitle,parentchannel
                FROM msg_channel
                WHERE parentchannel=?
                ORDER BY channelno";

        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $parentchannel);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_emp_main_channels($dbcon,$empno)
    {
        $sql = "SELECT channelno,channeltitle,parentchannel
                FROM msg_channel
                WHERE (parentchannel is NULL) AND channelno IN(
                    SELECT DISTINCT parentchannel
                    FROM msg_channelmember as cm
                        INNER JOIN msg_channel as c ON cm.channelno=c.channelno
                    WHERE userno=?)
                ORDER BY channelno";

        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("i", $empno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_emp_sub_channels($dbcon,$parent,$empno)
    {
        $sql = "SELECT channelno,channeltitle,parentchannel
                FROM msg_channel
                WHERE parentchannel=?  AND channelno IN(
                    SELECT DISTINCT channelno
                    FROM msg_channelmember
                    WHERE userno=?)
                ORDER BY channelno";

        $stmt = $dbcon->prepare($sql);
        $stmt->bind_param("ii", $parent,$empno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function get_channel_members($dbcon, $channelno){
        $sql = "SELECT u.userno,firstname,lastname,
                        isactive, entrytime
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



