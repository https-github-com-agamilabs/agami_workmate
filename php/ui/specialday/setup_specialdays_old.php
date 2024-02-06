<?php
date_default_timezone_set("Asia/Dhaka");
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

    $dbcon->begin_transaction();

    if (isset($_SESSION['wm_userno'])) {
        $userno = (int) $_SESSION['wm_userno'];
    } else {
        throw new \Exception("You must login first!", 1);
    }

    $specialdayno = -1;
    if (isset($_POST['specialdayno'])) {
        $specialdayno = (int) $_POST['specialdayno'];
    }

    if (isset($_POST['sdtypeid'])) {
        $sdtypeid = trim(strip_tags($_POST['sdtypeid']));
    } else {
        throw new \Exception("sdtypeid is not set", 1);
    }
    $insert_count = 0;

    if ($sdtypeid == "WEEK_END") {
        if (!isset($_POST['weekend_date'])) {
            throw new \Exception("No initial weekend_date set", 1);
        }

        if (!isset($_POST['start_date'])) {
            throw new \Exception("No initial start_month set", 1);
        }

        if (!isset($_POST['end_date'])) {
            throw new \Exception("No initial end_month set", 1);
        }

        $weekend_date = trim(strip_tags($_POST['weekend_date']));
        $start_date = strip_tags($_POST['start_date']);
        $end_date = strip_tags($_POST['end_date']);


        if (!validateDate($start_date) || !validateDate($start_date) || !validateDate($end_date)) {
            throw new \Exception("Invalid dates", 1);
        }

        // this query may be taken inside the if block afterwards
        $current_specialdays = get_specialdays_in_range($dbcon, $start_date, $end_date, $sdtypeid);

        if (isset($_POST['delete_previous_weekends']) && $_POST['delete_previous_weekends'] && $current_specialdays->num_rows > 0) {
            $deleted_specialdays = delete_specialdays_in_range($dbcon, $start_date, $end_date, $sdtypeid);
        }

        $specialdate = $weekend_date;
        for ($i = 0; strtotime($specialdate) <= strtotime($end_date); $i += 7) {

            if (strtotime($specialdate) < strtotime($start_date)) {
                $specialdate = date('Y-m-d',  strtotime("+" . 7 . " days", strtotime($specialdate)));

                continue;
            }
            if (strtotime($specialdate) > strtotime($end_date)) {
                break;
            }
            // echo $specialdate;
            // $res = 1;
            $res = insert_specialday($dbcon, $specialdate, $sdtypeid, $sdtypeid);

            if ($res <= 0) {
                throw new \Exception("Failed to insert specialday " . $sdtypeid, 1);
            }
            $specialdate = date('Y-m-d',  strtotime("+" . 7 . " days", strtotime($specialdate)));

            // if ($i > 100) {
            //     break;
            // }

            $insert_count++;
        }
    } else {
        if (isset($_POST['reasontext'])) {
            $reasontext = trim(strip_tags($_POST['reasontext']));
        } else {
            $reasontext = NULL;
        }

        if (!isset($_POST['specialdays'])) {
            throw new \Exception("No specialday dates set", 1);
        }

        $specialday = json_decode(trim(strip_tags($_POST['specialdays'])), true);

        if ($specialdayno > 0 && count($specialday) == 1) {
            $specialdate = $specialday[0];
            $res = update_specialday($dbcon, $specialdayno, $specialdate, $reasontext, $sdtypeid);

            if ($res <= 0) {
                throw new \Exception("Failed to update specialday " . $sdtypeid, 1);
            }
        } else {
            for ($i = 0; $i < count($specialday); $i++) {
                $specialdate = $specialday[$i];
                $res = insert_specialday($dbcon, $specialdate, $reasontext, $sdtypeid);

                if ($res <= 0) {
                    throw new \Exception("Failed to insert specialday " . $sdtypeid, 1);
                }
            }
        }
    }

    if ($dbcon->commit()) {
        $response['error'] = false;
        if ($specialdayno > 0 && count($specialday) == 1) {
            $response['message'] = "specialdays updated Successfully. [" . $res . "] [" . $i . "]";
        } else {
            $response['message'] = "specialdays inserted Successfully. [" . $insert_count . "] [" . $i . "]";
        }
    } else {
        throw new \Exception("Failed! Please try again.", 1);
    }
} catch (Exception $e) {
    $dbcon->rollback();
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
if (isset($dbcon)) {
    $dbcon->close();
}

/**
 * Local Function
 */


function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && $d->format($format) === $date;
}

// -- emp_leaveapplication(lappno, empno, leavetypeno, reasontext, leavestatusno, createdatetime, updatetime)

function update_specialday($dbcon, $specialdayno, $specialdate, $reasontext, $sdtypeid)
{
    $sql = "UPDATE emp_specialdays
            SET specialdate=?, reasontext=?, sdtypeid=?
            WHERE specialdayno=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("sssi", $specialdate, $reasontext, $sdtypeid, $specialdayno);
    $stmt->execute();
    return $stmt->affected_rows;
}

// specialdayno
// specialdate
// reasontext
// sdtypeid
function insert_specialday($dbcon, $specialdate, $reasontext, $sdtypeid)
{
    $sql = "INSERT INTO emp_specialdays
            (specialdate, reasontext, sdtypeid)
            VALUES (?,?,?)";
    $stmt = $dbcon->prepare($sql);
    if ($dbcon->error) {
        echo $dbcon->error;
    }
    $stmt->bind_param("sss", $specialdate, $reasontext, $sdtypeid);
    $stmt->execute();
    return $stmt->insert_id;
}


function get_specialdays_in_range($dbcon, $start_date, $end_date, $sdtypeid)
{
    $today = date('Y-m-d');
    $sql = "SELECT * FROM emp_specialdays
            WHERE (specialdate BETWEEN ? AND ?) AND sdtypeid=? ";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("sss", $start_date, $end_date, $sdtypeid);
    $stmt->execute();
    return $stmt->get_result();
}


function delete_specialdays_in_range($dbcon, $start_date, $end_date, $sdtypeid)
{
    $today = date('Y-m-d');
    $sql = "DELETE FROM emp_specialdays
            WHERE (specialdate BETWEEN ? AND ?) AND sdtypeid=? ";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("sss", $start_date, $end_date, $sdtypeid);
    $stmt->execute();
    return $stmt->affected_rows;
}
