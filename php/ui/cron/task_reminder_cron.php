<?php

date_default_timezone_set('Asia/Dhaka');

// -------------------- CONFIG --------------------
$admin_email = "agamilabs@gmail.com";
$from_email = "noreply@workmate.agamilab.com";
$lag_buffer = 10; // % buffer for lagging
$stalled_hours = 4; // no progress threshold

$enable_whatsapp = false; // enable free WhatsApp notifications
$callmebot_api_key = "YOUR_CALLMEBOT_API_KEY"; // free WhatsApp API key
// ------------------------------------------------

// -------------------- DB CONNECTION --------------------
$base_path = dirname(dirname(dirname(__FILE__)));
require_once($base_path."/db/Database.php");

$db = new Database();
$conn = $db->db_connect();
if (!$db || !$db->is_connected()) {
    throw new \Exception("Database is not connected!", 1);
}

// Current datetime
$now = new DateTime();
$today = $now->format('Y-m-d');

// -------------------- HELPER FUNCTIONS --------------------
function sendEmail($to, $subject, $body) {
    global $from_email;
    mail($to, $subject, $body, "From: AGAMiLabs <$from_email>");
}

function sendWhatsApp($phone, $message) {
    global $enable_whatsapp, $callmebot_api_key;
    if(!$enable_whatsapp || empty($phone)) return;

    // Clean number & urlencode message
    $phone = preg_replace('/\D/', '', $phone);
    $text = urlencode($message);
    $url = "https://api.callmebot.com/whatsapp.php?phone=$phone&text=$text&apikey=$callmebot_api_key";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// Check if reminder sent
function reminderSent($conn, $userno, $cblscheduleno, $type) {
    $cblscheduleno_val = $cblscheduleno ? " = $cblscheduleno" : " IS NULL";
    $sql = "SELECT 1 FROM asp_task_reminder_log 
            WHERE userno = $userno AND cblscheduleno $cblscheduleno_val 
            AND reminder_type = '$type' 
            AND DATE(sent_time) = CURDATE() LIMIT 1";
    $res = $conn->query($sql);
    return $res->num_rows > 0;
}

function logReminder($conn, $userno, $cblscheduleno, $type) {
    $cblscheduleno_val = $cblscheduleno ? $cblscheduleno : "NULL";
    $conn->query("INSERT INTO asp_task_reminder_log (userno, cblscheduleno, reminder_type) 
                  VALUES ($userno, $cblscheduleno_val, '$type')");
}

// -------------------- 1. Morning Task Summary --------------------
$employees = $conn->query("SELECT userno, fullname, email, concat(u.countrycode,u.primarycontact) as whatsapp FROM users");
while($emp = $employees->fetch_assoc()) {
    $userno = $emp['userno'];

    $tasks = $conn->query("SELECT * FROM asp_cblschedule 
                           WHERE assignedto = $userno AND scheduledate = '$today'");

    if($tasks->num_rows > 0 && !reminderSent($conn, $userno, null, 'morning')) {
        $task_list = "";
        while($t = $tasks->fetch_assoc()) {
            $task_list .= "Task #" . $t['cblscheduleno'] . ": " . $t['howto'] . " (Duration: " . $t['duration'] . "h)\n";
        }
        $subject = "Today's Tasks";
        $body = "Good morning!\n\nHere are your tasks for today:\n\n" . $task_list;

        sendEmail($emp['email'], $subject, $body);
        sendWhatsApp($emp['whatsapp'], $body);
        logReminder($conn, $userno, null, 'morning');
    } elseif($tasks->num_rows == 0 && !reminderSent($conn, $userno, null, 'morning_admin')) {
        $body = "No task assigned for employee {$emp['fullname']} (UserID: $userno) for today.";
        sendEmail($admin_email, "No Task Assigned", $body);
        logReminder($conn, $userno, null, 'morning_admin');
    }
}

// -------------------- 2. Progress Check --------------------
$sql = "
SELECT s.cblscheduleno, s.assignedto, s.assigntime, s.duration, s.scheduledate,
       u.email, concat(u.countrycode,u.primarycontact) as whatsapp,
       (SELECT MAX(progresstime) FROM asp_cblprogress p WHERE p.cblscheduleno = s.cblscheduleno) AS last_progress,
       (SELECT percentile FROM asp_cblprogress p WHERE p.cblscheduleno = s.cblscheduleno ORDER BY progresstime DESC LIMIT 1) AS percentile
FROM asp_cblschedule s
JOIN users u ON u.userno = s.assignedto
WHERE s.scheduledate >= '$today'";

$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $assigntime = strtotime($row['assigntime']);
    $duration_hours = floatval($row['duration']);
    $elapsed_hours = ($now->getTimestamp() - $assigntime) / 3600;
    $expected_percent = min(100, ($elapsed_hours / $duration_hours) * 100);
    $current_percent = intval($row['percentile'] ?? 0);

    $last_progress = isset($row['last_progress']) ? strtotime($row['last_progress']) : $assigntime;
    $hours_since_progress = ($now->getTimestamp() - $last_progress) / 3600;

    $task_id = $row['cblscheduleno'];
    $employee_email = $row['email'];
    $employee_whatsapp = $row['whatsapp'];
    $userno = $row['assignedto'];

    // --- Lagging progress ---
    if($current_percent + $lag_buffer < $expected_percent && !reminderSent($conn, $userno, $task_id, 'lagging')) {
        $subject = "Task #$task_id Lagging Reminder";
        $body = "Task #$task_id is lagging behind expected progress. Please update your progress.";
        sendEmail($employee_email, $subject, $body);
        sendWhatsApp($employee_whatsapp, $body);
        logReminder($conn, $userno, $task_id, 'lagging');
    }

    // --- Stalled progress (>4h) ---
    if($hours_since_progress >= $stalled_hours && !reminderSent($conn, $userno, $task_id, 'stalled')) {
        $subject = "No Progress Update for Task #$task_id";
        $body = "Task #$task_id assigned to user $userno has no progress update for $stalled_hours+ hours.";
        sendEmail($admin_email, $subject, $body);
        logReminder($conn, $userno, $task_id, 'stalled');
    }
}

$conn->close();
