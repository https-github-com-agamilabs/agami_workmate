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

        if (isset($_SESSION['cogo_userno'])) {
            $userno=(int) $_SESSION['cogo_userno'];
        } else {
            throw new \Exception("You must login first!", 1);
        }

        if(!isset($_SESSION['cogo_orgno'])){
            throw new \Exception("You must select an organization!", 1);
        }else{
            $orgno= (int) $_SESSION['cogo_orgno'];
        }

        $ucatno=0;
        if (isset($_SESSION['cogo_ucatno'])) {
            $ucatno=(int) $_SESSION['cogo_ucatno'];
        }

        // if($ucatno>=19){
        //     $result=get_admin_new_update($dbcon,$userno);
        // }else{
        //     $result=get_user_new_update($dbcon, $userno);
        // }

        // $notfication_array=array();
        // if($result->num_rows>0){
        //     while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        //         $notfication_array[]=$row;
        //     }
        // }
        // $response['error'] = false;
        // $response['chat'] = $notfication_array;

        $result=get_user_task_update($dbcon,$userno);
        $notfication_array=array();
        if($result->num_rows>0){
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $notfication_array[]=$row;
            }
        }
        $response['task'] = $notfication_array;

    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    $dbcon->close();


    /*
    *   LOCAL FUNCTIONS
    */
//asp_channelbacklog(backlogno,channelno,story,storytype,prioritylevelno,relativepriority,storyphaseno,parentbacklogno,approved,accessibility,lastupdatetime,userno)
//msg_chat(chatno,messenger,channelno,message,createtime,lastupdatetime,editcount,catno,statusno,parentchatno,chatflag)
function get_lastvisittime($dbcon, $userno){
    $sql = "SELECT channelno, lastvisittime
            FROM msg_lastvisit
            WHERE userno=?";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $userno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
// function get_user_new_update($dbcon, $userno)
// {
//     $sql = "SELECT c.channelno,cl.parentchannel,count(chatno) as updateqty
//             FROM (SELECT channelno, chatno, createtime, lastupdatetime
//                     FROM msg_chat
//                     WHERE channelno IN(
//                         SELECT channelno
//                         FROM msg_channelmember
//                         WHERE userno=?)
//                 ) as c
//                 LEFT JOIN (SELECT channelno, lastvisittime
//                             FROM msg_lastvisit
//                             WHERE userno=?) as lv
//                     ON c.channelno=lv.channelno
//                 INNER JOIN msg_channel as cl ON c.channelno=cl.channelno
//             WHERE (lv.lastvisittime is NULL) OR c.createtime > lv.lastvisittime OR c.lastupdatetime > lv.lastvisittime
//             GROUP BY c.channelno
//             HAVING count(chatno)>0
//             ORDER BY c.channelno";
//     $stmt = $dbcon->prepare($sql);
//     $stmt->bind_param("ii", $userno,$userno);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $stmt->close();

//     return $result;
// }

// function get_admin_new_update($dbcon,$userno)
// {
//     $sql = "SELECT c.channelno,cl.parentchannel,count(chatno) as updateqty
//             FROM (SELECT channelno, chatno, createtime, lastupdatetime
//                     FROM msg_chat
//                 ) as c
//                 LEFT JOIN (SELECT channelno, lastvisittime
//                             FROM msg_lastvisit
//                             WHERE userno=?) as lv
//                     ON c.channelno=lv.channelno
//                 INNER JOIN msg_channel as cl ON c.channelno=cl.channelno
//             WHERE (lv.lastvisittime is NULL) OR c.createtime > lv.lastvisittime OR c.lastupdatetime > lv.lastvisittime
//             GROUP BY c.channelno
//             HAVING count(chatno)>0
//             ORDER BY c.channelno";
//     $stmt = $dbcon->prepare($sql);
//     $stmt->bind_param("i", $userno);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $stmt->close();

//     return $result;
// }

function get_user_task_update($dbcon,$userno)
{
    $sql = "SELECT t.channelno, (SELECT parentchannel FROM msg_channel WHERE channelno=t.channelno) as parentchannel,
                    count(backlogno) as updateqty
            FROM
                (SELECT channelno, backlogno, lastupdatetime as updatetime
                    FROM asp_channelbacklog
                UNION
                (SELECT channelno, b.backlogno,s.updatetime
                FROM asp_channelbacklog as b
                    INNER JOIN
                    (SELECT backlogno, assigntime as updatetime
                    FROM asp_cblschedule
                    )  as s
                    ON b.backlogno=s.backlogno
                )
                UNION
                (SELECT channelno, b.backlogno,d.updatetime
                FROM 	asp_channelbacklog as b
                        INNER JOIN asp_cblschedule as s ON b.backlogno=s.backlogno
                        INNER JOIN
                        (SELECT cblscheduleno, entrytime as updatetime
                        FROM asp_deadlines
                        )  as d ON s.cblscheduleno=d.cblscheduleno
                )
                UNION
                (SELECT channelno, b.backlogno,p.updatetime
                FROM 	asp_channelbacklog as b
                        INNER JOIN asp_cblschedule as s ON b.backlogno=s.backlogno
                        INNER JOIN asp_deadlines as d ON s.cblscheduleno=d.cblscheduleno
                        INNER JOIN
                        (SELECT cblscheduleno, progresstime as updatetime
                        FROM asp_cblprogress
                        )  as p ON s.cblscheduleno=p.cblscheduleno
                    )
                ) as t
                LEFT JOIN (SELECT channelno, lastvisittime
                            FROM msg_lastvisit
                            WHERE userno=?) as lv
                    ON t.channelno=lv.channelno
            WHERE (lv.lastvisittime is NULL) OR t.updatetime > lv.lastvisittime
            GROUP BY t.channelno
            HAVING count(backlogno)>0
            ORDER BY t.channelno";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $userno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
