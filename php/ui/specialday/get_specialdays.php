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

    if (isset($_SESSION['wm_userno'])) {
        $userno = (int) $_SESSION['wm_userno'];
    } else {
        throw new \Exception("You must login first!", 1);
    }

    $ucatno = 0;
    if (isset($_SESSION['wm_ucatno'])) {
        $ucatno = (int) $_SESSION['wm_ucatno'];
    }

    $yyyy_mm = date('Y-m');
    $start = date('Y-m-01', strtotime($yyyy_mm));
    $end = date('Y-m-t', strtotime($yyyy_mm));
    if (isset($_POST['yyyy_mm'])) {
        $yyyy_mm = trim(strip_tags($_POST['yyyy_mm']));

        $start = date('Y-m-01', strtotime($yyyy_mm));
        $end = date('Y-m-t', strtotime($yyyy_mm));
    } else if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
        $start = trim(strip_tags($_POST['start_date']));
        $end = trim(strip_tags($_POST['end_date']));
    } else {
        throw new \Exception("Please send start date end date or a month of year as YYYY-mm format!", 1);
    }

    $result = get_specialdays($dbcon, $start, $end);

    $notfication_array = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
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


/*
    *   LOCAL FUNCTIONS
    */

function get_specialdays($dbcon, $start, $end)
{

    if (!validateDate($start)) {
        $start = date('Y-m-01');
    }
    if (!validateDate($end)) {
        $end = date('Y-m-t');
    }

    $sql = "SELECT *
            FROM emp_specialdays
            WHERE specialdate BETWEEN ? AND ? ";
    $stmt = $dbcon->prepare($sql);
    if ($dbcon->error) {
        echo $dbcon->error;
    }
    $stmt->bind_param('ss', $start, $end);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && $d->format($format) === $date;
}
