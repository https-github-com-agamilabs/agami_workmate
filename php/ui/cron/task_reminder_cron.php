<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

date_default_timezone_set('Asia/Dhaka');

/* ==================== CONFIG ==================== */
$admin_email   = "agamilabs@gmail.com";
$CC_emails     = array_filter(array_map('trim', explode(',', "shmazumder23@gmail.com, shazzad@agamilabs.com")));
$from_email    = "noreply@workmate.agamilab.com";
$lag_buffer    = 10;      // % buffer for lagging
$stalled_hours = 1;       // no progress threshold

$enable_whatsapp    = false;
$callmebot_api_key  = "YOUR_CALLMEBOT_API_KEY";
/* =============================================== */

/* ==================== DB ==================== */
$base_path = dirname(dirname(dirname(__FILE__)));
require_once $base_path . "/db/Database.php";

$db   = new Database();
$conn = $db->db_connect();
if (!$db->is_connected()) {
    die("Database connection failed");
}

$now   = new DateTime();
$today = $now->format('Y-m-d');
/* ============================================ */

/* ==================== HELPERS ==================== */
function sendEmail(string $to, string $subject, string $body): void
{
    global $from_email, $CC_emails;
    $headers  = "From: AGAMiLabs <$from_email>\r\n";
    $headers .= "CC: " . implode(', ', $CC_emails) . "\r\n";
    $headers .= "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    mail($to, $subject, $body, $headers);
}

function sendWhatsApp(string $phone, string $message): void
{
    global $enable_whatsapp, $callmebot_api_key;
    if (!$enable_whatsapp || empty($phone) || empty($callmebot_api_key)) {
        return;
    }

    $phone = preg_replace('/\D/', '', $phone);
    if ($phone[0] !== '+') {
        $phone = '+' . $phone;
    }
    $text = urlencode($message);
    $url  = "https://api.callmebot.com/whatsapp.php?phone=$phone&text=$text&apikey=$callmebot_api_key";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    curl_close($ch);
}

/** Prepared-statement version – safe & reusable */
function reminderSent(mysqli $conn, int $userno, ?int $cblscheduleno, string $type): bool
{
    $sql = "SELECT 1 FROM asp_task_reminder_log 
            WHERE userno = ? 
              AND reminder_type = ? 
              AND DATE(sent_time) = CURDATE()";
    $params = [$userno, $type];
    $types  = 'is';

    if ($cblscheduleno !== null) {
        $sql   .= " AND cblscheduleno = ?";
        $params[] = $cblscheduleno;
        $types .= 'i';
    } else {
        $sql .= " AND cblscheduleno IS NULL";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->num_rows > 0;
}

function logReminder(mysqli $conn, int $userno, ?int $cblscheduleno, string $type): void
{
    $sql = "INSERT INTO asp_task_reminder_log (userno, cblscheduleno, reminder_type) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iis', $userno, $cblscheduleno, $type);
    $stmt->execute();
}
/* ================================================= */

/* ==================== 1. MORNING SUMMARY ==================== */
$empStmt = $conn->prepare(
    "SELECT userno, CONCAT(firstname,' ',lastname) AS fullname, email,
            CONCAT(countrycode,primarycontact) AS whatsapp
       FROM hr_user WHERE isactive = 1"
);
$empStmt->execute();
$employees = $empStmt->get_result();

while ($emp = $employees->fetch_assoc()) {
    $userno = (int)$emp['userno'];

    // ---- today’s tasks for this employee ----
    $taskStmt = $conn->prepare(
        "SELECT cblscheduleno, howto, duration
           FROM asp_cblschedule
          WHERE assignedto = ? AND scheduledate = ?"
    );
    $taskStmt->bind_param('is', $userno, $today);
    $taskStmt->execute();
    $tasksResult = $taskStmt->get_result();

    if ($tasksResult->num_rows > 0 && !reminderSent($conn, $userno, null, 'morning')) {
        $taskList = '';
        while ($t = $tasksResult->fetch_assoc()) {
            $taskList .= "Task #{$t['cblscheduleno']}: {$t['howto']} (Duration: {$t['duration']}h)\n";
        }
        $subject = "Today's Tasks";
        $body    = "Good morning!\n\nHere are your tasks for today:\n\n" . $taskList;

        sendEmail($emp['email'], $subject, $body);
        sendWhatsApp($emp['whatsapp'], $body);
        logReminder($conn, $userno, null, 'morning');
    } elseif ($tasksResult->num_rows === 0 && !reminderSent($conn, $userno, null, 'morning_admin')) {
        $body = "No task assigned for employee {$emp['fullname']} (UserID: $userno) for today.";
        sendEmail($admin_email, "No Task Assigned", $body);
        logReminder($conn, $userno, null, 'morning_admin');
    }
    $tasksResult->free();
}
$employees->free();

/* ==================== 2. PROGRESS CHECK ==================== */
$progressSQL = "
SELECT s.cblscheduleno, s.assignedto, s.assigntime, s.duration,
       u.email, CONCAT(u.countrycode,u.primarycontact) AS whatsapp,
       MAX(p.progresstime) AS last_progress,
       COALESCE(MAX(p.percentile),0) AS percentile
FROM asp_cblschedule s
JOIN hr_user u ON u.userno = s.assignedto
LEFT JOIN asp_cblprogress p ON p.cblscheduleno = s.cblscheduleno
WHERE s.scheduledate <= ? AND u.isactive = 1
GROUP BY s.cblscheduleno";

$progStmt = $conn->prepare($progressSQL);
$progStmt->bind_param('s', $today);
$progStmt->execute();
$progressRes = $progStmt->get_result();

while ($row = $progressRes->fetch_assoc()) {
    $taskId         = (int)$row['cblscheduleno'];
    $userno         = (int)$row['assignedto'];
    $email          = $row['email'];
    $whatsapp       = $row['whatsapp'];
    $durationHours  = (float)$row['duration'];
    $assignTS       = strtotime($row['assigntime']);
    $elapsedHours   = ($now->getTimestamp() - $assignTS) / 3600;
    $expectedPct    = min(100, ($elapsedHours / $durationHours) * 100);
    $currentPct     = (int)$row['percentile'];

    $lastProgTS     = $row['last_progress'] ? strtotime($row['last_progress']) : $assignTS;
    $hoursNoProg    = ($now->getTimestamp() - $lastProgTS) / 3600;

    /* ---- Lagging ---- */
    if (($currentPct + $lag_buffer) < $expectedPct && !reminderSent($conn, $userno, $taskId, 'lagging')) {
        $subject = "Task #$taskId Lagging Reminder";
        $body    = "Task #$taskId is lagging behind expected progress (expected ~" .
                   round($expectedPct) . "%, current $currentPct%). Please update.";
        sendEmail($email, $subject, $body);
        sendWhatsApp($whatsapp, $body);
        logReminder($conn, $userno, $taskId, 'lagging');
    }

    /* ---- Stalled ---- */
    if ($hoursNoProg >= $stalled_hours && !reminderSent($conn, $userno, $taskId, 'stalled')) {
        $subject = "No Progress Update for Task #$taskId";
        $body    = "Task #$taskId (user $userno) has no progress for " .
                   round($hoursNoProg, 1) . " hours.";
        sendEmail($admin_email, $subject, $body);
        logReminder($conn, $userno, $taskId, 'stalled');
    }
}
$progressRes->free();
$conn->close();