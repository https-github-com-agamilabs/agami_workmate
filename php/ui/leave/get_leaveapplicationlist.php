<?php
include_once  dirname(dirname(__FILE__)) . "/login/check_session.php";

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

try {
    $base_path = dirname(dirname(dirname(__FILE__)));
    require_once($base_path . "/db/Database.php");

    $db = new Database();
    $dbcon = $db->db_connect();
    if (!$db->is_connected()) {
        throw new \Exception("Database is not connected!", 1);
    }

    if(isset($_SESSION['wm_orgno'])){
        $orgno=(int) $_SESSION['wm_orgno'];
    }else{
        throw new \Exception("You must select an organization!", 1);
    }

    if (isset($_SESSION['wm_userno'])) {
        $wm_userno = (int) $_SESSION['wm_userno'];
    } else {
        throw new \Exception("You must login first!", 1);
    }

    $ucatno = 0;
    if (isset($_SESSION['wm_ucatno'])) {
        $ucatno = (int) $_SESSION['wm_ucatno'];
    }

    $userno = -1;
    if (isset($_POST['userno'])) {
        $userno = (int) $_POST['userno'];
    }

    $leavetypeno = 0;
    if (isset($_POST['leavetypeno'])) {
        $leavetypeno = (int) $_POST['leavetypeno'];
    }

    $leavestatusno = 0;
    if (isset($_POST['leavestatusno'])) {
        $leavestatusno = (int) $_POST['leavestatusno'];
    }

    $pageno = 1;
    if (isset($_POST['pageno'])) {
        $pageno = (int) $_POST['pageno'];
        if ($pageno < 1) {
            $pageno = 1;
        }
    }

    $limit = 30;
    if (isset($_POST['limit'])) {
        $limit = (int) $_POST['limit'];
        if ($limit < 1) {
            $limit = 1;
        }
    }

    if ($ucatno >= 10) {
        $result = get_filtered_applications_of_user_for_user($dbcon, $orgno, $wm_userno, -1, $leavestatusno, $leavetypeno, $pageno, $limit);
    } else {
        $result = get_filtered_applications_of_user_for_user($dbcon, $orgno, $wm_userno, $userno, $leavestatusno, $leavetypeno, $pageno, $limit);
    }

    $notfication_array = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            if ($ucatno >= 10) {
                $row['can_approve'] = true;
                $row['can_reject'] = true;
                $row['can_delete'] = true;
            }

            if ($userno == $row['userno']) {
                $row['can_delete'] = true;
            }
            $notfication_array[] = $row;
        }
    }
    $response['error'] = false;
    $response['data'] = $notfication_array;
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$dbcon->close();

//emp_leaveapplication(lappno,orgno,empno,leavetypeno,reasontext,leavestatusno,actiontakenby,createdatetime,updatetime)
function get_filtered_applications_of_user_for_user($dbcon, $orgno,$wm_userno, $userno, $leavestatusno, $leavetypeno, $pageno, $limit)
{

    $queryClause = " 1 ";
    $values = array();
    $types = array();

    if ($wm_userno > 0) {
        $values[] = &$wm_userno;
        $types[] = "i";
        $values[] = &$wm_userno;
        $types[] = "i";
    }

    if ($userno > 0) {
        $queryClause .= " AND emp.userno=?";
        $values[] = &$userno;
        $types[] = "i";
    }

    if ($leavestatusno > 0) {
        $queryClause .= " AND la.leavestatusno=?";
        $values[] = &$leavestatusno;
        $types[] = "i";
    }

    if ($leavetypeno > 0) {
        $queryClause .= " AND la.leavetypeno=?";
        $values[] = &$leavetypeno;
        $types[] = "i";
    }

    $start = ($pageno - 1) * $limit;
    $values[] = &$start;
    $types[] = "i";
    $values[] = &$limit;
    $types[] = "i";

    $sql = "SELECT *,
                (SELECT GROUP_CONCAT(leavedate)
                FROM emp_leavedates
                WHERE lappno=la.lappno) as leavedays
            FROM
                (SELECT * FROM emp_leaveapplication) as la
                INNER JOIN
                (SELECT  `userno`, `username`, `firstname`, `lastname`,
                    `affiliation`, `jobtitle`, `email`, `primarycontact`,
                    `createtime`, `lastupdatetime`, `isactive`
                    FROM hr_user
                    WHERE userno=? OR EXISTS (SELECT userno FROM com_userorg WHERE userno=? AND ucatno>=10)
                ) as emp
                ON la.empno=emp.userno
                INNER JOIN
                (SELECT * FROM emp_leavetype) as lt
                ON lt.leavetypeno=la.leavetypeno
                INNER JOIN
                (SELECT * FROM emp_leavestatus) as ls
                ON ls.leavestatusno=la.leavestatusno
            WHERE la.orgno=$orgno AND $queryClause
            ORDER BY lappno DESC
            LIMIT ?, ?";
    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        // echo $sql;
        // echo $dbcon->error;
    }
    // echo json_encode(array($values));
    $stmt->bind_param(implode('', $types), ...$values);
    // $stmt->bind_param("i", $userno);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
