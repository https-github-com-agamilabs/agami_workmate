<?php
date_default_timezone_set("Asia/Dhaka");
$base_path = dirname(dirname(dirname(__FILE__)));
include_once  $base_path."/ui/login/check_session.php";

$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

try {

    require_once($base_path . "/db/Database.php");

    $db = new Database();
    $dbcon = $db->db_connect();
    if (!$db->is_connected()) {
        throw new \Exception("Database is not connected!", 1);
    }

    $dbcon->begin_transaction();

    $holidayno = -1;
    if (isset($_POST['holidayno'])) {
        $holidayno = (int) $_POST['holidayno'];
    }

    $delete_previous_weekends = 1;
    if (isset($_POST['delete_previous_weekends']) && strlen($_POST['delete_previous_weekends'])>0) {
        $delete_previous_weekends = (int) $_POST['delete_previous_weekends'];
    }

    if (isset($_POST['hdtypeid'])) {
        $hdtypeid = trim(strip_tags($_POST['hdtypeid']));
    } else {
        throw new \Exception("hdtypeid is not set", 1);
    }
    $insert_count = 0;

    $dbcon->begin_transaction();
    if ($holidayno<=0 && isset($_POST['start_date']) && isset($_POST['end_date'])) {
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
        //$current_holidays = get_holidays_in_range($dbcon, $start_date, $end_date, $hdtypeid);
        if ($delete_previous_weekends!=0) {
            $deleted_holidays = delete_holidays_in_range($dbcon, $start_date, $end_date, $hdtypeid);
        }

        $holidaydate = $weekend_date;
        for ($i = 0; strtotime($holidaydate) <= strtotime($end_date); $i += 7) {

            if (strtotime($holidaydate) < strtotime($start_date)) {
                $holidaydate = date('Y-m-d',  strtotime("+" . 7 . " days", strtotime($holidaydate)));

                continue;
            }
            if (strtotime($holidaydate) > strtotime($end_date)) {
                break;
            }
            // echo $holidaydate;
            // $res = 1;
            $res = insert_holiday($dbcon, $holidaydate, $hdtypeid, $hdtypeid);

            if ($res <= 0) {
                throw new \Exception("Failed to insert holiday " . $hdtypeid, 1);
            }
            $holidaydate = date('Y-m-d',  strtotime("+" . 7 . " days", strtotime($holidaydate)));

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

        if (!isset($_POST['holidays'])) {
            throw new \Exception("No holiday dates set", 1);
        }

        $holiday = json_decode(trim(strip_tags($_POST['holidays'])), true);

        if ($holidayno > 0 && count($holiday) == 1) {
            $holidaydate = $holiday[0];
            $res = update_holiday($dbcon, $holidayno, $holidaydate, $reasontext, $hdtypeid);

            if ($res <= 0) {
                throw new \Exception("Failed to update holiday " . $hdtypeid, 1);
            }
        } else {
            for ($i = 0; $i < count($holiday); $i++) {
                $holidaydate = $holiday[$i];
                $res = insert_holiday($dbcon, $holidaydate, $reasontext, $hdtypeid);

                if ($res <= 0) {
                    throw new \Exception("Failed to insert holiday " . $hdtypeid, 1);
                }
            }
        }
    }

    if ($dbcon->commit()) {
        $response['error'] = false;
        if ($holidayno > 0 && count($holiday) == 1) {
            $response['message'] = "Holidays updated Successfully. [" . $res . "] [" . $i . "]";
        } else {
            $response['message'] = "Holidays inserted Successfully. [" . $insert_count . "] [" . $i . "]";
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

function update_holiday($dbcon, $holidayno, $holidaydate, $reasontext, $hdtypeid)
{
    $sql = "UPDATE emp_holidays
            SET holidaydate=?, reasontext=?, hdtypeid=?
            WHERE holidayno=?";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("sssi", $holidaydate, $reasontext, $hdtypeid, $holidayno);
    $stmt->execute();
    return $stmt->affected_rows;
}

// holidayno
// holidaydate
// reasontext
// hdtypeid
function insert_holiday($dbcon, $holidaydate, $reasontext, $hdtypeid)
{
    $sql = "INSERT INTO emp_holidays
            (holidaydate, reasontext, hdtypeid, minworkinghour)
            SELECT ?, ?, ?, minworkinghour
            FROM emp_holidaytype WHERE hdtypeid=?";
    $stmt = $dbcon->prepare($sql);
    if ($dbcon->error) {
        echo $dbcon->error;
    }
    $stmt->bind_param("ssss", $holidaydate, $reasontext, $hdtypeid, $hdtypeid);
    $stmt->execute();
    return $stmt->insert_id;
}

function get_holidays_in_range($dbcon, $start_date, $end_date, $hdtypeid)
{
    $today = date('Y-m-d');
    $sql = "SELECT * FROM emp_holidays
            WHERE (holidaydate BETWEEN ? AND ?) AND hdtypeid=? ";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("sss", $start_date, $end_date, $hdtypeid);
    $stmt->execute();
    return $stmt->get_result();
}


function delete_holidays_in_range($dbcon, $start_date, $end_date, $hdtypeid)
{
    $today = date('Y-m-d');
    $sql = "DELETE FROM emp_holidays
            WHERE (holidaydate BETWEEN ? AND ?) AND hdtypeid=? ";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("sss", $start_date, $end_date, $hdtypeid);
    $stmt->execute();
    return $stmt->affected_rows;
}
