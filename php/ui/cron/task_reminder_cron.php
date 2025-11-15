<?php
/**
 * task_reminder.php
 * Daily Task Reminder + Progress Monitor
 * Runs: 8:30 AM Dhaka time (cron)
 * Features:
 *  - Personalized emails with employee name
 *  - HTML-rich task descriptions (howto)
 *  - Multipart email (HTML + Plain text)
 *  - WhatsApp support (plain text)
 *  - Lagging & Stalled alerts
 *  - SQL injection safe
 *  - No duplicate alerts
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

date_default_timezone_set('Asia/Dhaka');

// ==================== CONFIG ====================
$admin_email   = "agamilabs@gmail.com";
$CC_emails     = array_filter(array_map('trim', explode(',', "shmazumder23@gmail.com, shazzad@agamilabs.com")));
$from_email    = "noreply@workmate.agamilab.com";
$lag_buffer    = 10;        // % buffer for lagging
$stalled_hours = 1;         // hours with no progress

$enable_whatsapp    = false;  // Set true + API key to enable
$callmebot_api_key  = "YOUR_CALLMEBOT_API_KEY";
// ===============================================

// ==================== DB CONNECTION ====================
$base_path = dirname(dirname(dirname(__FILE__)));
require_once $base_path . "/db/Database.php";

$db   = new Database();
$conn = $db->db_connect();
if (!$db->is_connected()) {
    die("Database connection failed");
}

$now   = new DateTime();
$today = $now->format('Y-m-d');
// =======================================================

// ==================== HELPER FUNCTIONS ====================

/** Send multipart email (HTML + Plain text) */
function sendEmail(string $to, string $subject, string $plainBody, string $htmlBody): void
{
    global $from_email, $CC_emails;

    $boundary = md5(time());
    $headers  = "From: AGAMiLabs <$from_email>\r\n";
    $headers .= "CC: " . implode(', ', $CC_emails) . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";

    $message  = "--$boundary\r\n";
    $message .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
    $message .= $plainBody . "\r\n";

    $message .= "--$boundary\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
    $message .= $htmlBody . "\r\n";

    $message .= "--$boundary--";

    mail($to, $subject, $message, $headers);
}

/** Send WhatsApp message (plain text only) */
function sendWhatsApp(?string $phone, string $message): void
{
    global $enable_whatsapp, $callmebot_api_key;
    if (!$enable_whatsapp || !$phone || empty($callmebot_api_key)) return;

    $phone = preg_replace('/\D/', '', $phone);
    $phone = $phone[0] === '+' ? $phone : '+' . $phone;
    $text  = urlencode($message);
    $url   = "https://api.callmebot.com/whatsapp.php?phone=$phone&text=$text&apikey=$callmebot_api_key";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    curl_close($ch);
}

/** Check if reminder already sent today */
function reminderSent(mysqli $conn, int $userno, ?int $cblscheduleno, string $type): bool
{
    $sql = "SELECT 1 FROM asp_task_reminder_log 
            WHERE userno = ? AND reminder_type = ? AND DATE(sent_time) = CURDATE()";
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

/** Log reminder to prevent duplicates */
function logReminder(mysqli $conn, int $userno, ?int $cblscheduleno, string $type): void
{
    $stmt = $conn->prepare(
        "INSERT INTO asp_task_reminder_log (userno, cblscheduleno, reminder_type) VALUES (?, ?, ?)"
    );
    $stmt->bind_param('iis', $userno, $cblscheduleno, $type);
    $stmt->execute();
}

/** Sanitize and format howto HTML safely */
function formatHowTo(string $html): string
{
    // Allow only safe tags
    $allowed = '<b><i><u><strong><em><a><ul><ol><li><br><p><blockquote><code><pre>';
    $safe = strip_tags($html, $allowed);

    // Make links clickable and styled
    $safe = preg_replace(
        '#<a\s+href=["\']([^"\']+)["\'][^>]*>([^<]+)</a>#i',
        '<a href="$1" style="color:#0066cc;text-decoration:underline;">$2</a>',
        $safe
    );

    return $safe;
}
// =======================================================

// ==================== 1. MORNING SUMMARY (HTML + Plain) ====================
$empStmt = $conn->prepare(
    "SELECT userno, CONCAT(firstname,' ',lastname) AS fullname, email,
            CONCAT(IFNULL(countrycode,''),IFNULL(primarycontact,'')) AS whatsapp
       FROM hr_user WHERE isactive = 1"
);
$empStmt->execute();
$employees = $empStmt->get_result();

while ($emp = $employees->fetch_assoc()) {
    $userno   = (int)$emp['userno'];
    $fullname = $emp['fullname'];
    $email    = $emp['email'];
    $whatsapp = $emp['whatsapp'];

    $taskStmt = $conn->prepare(
        "SELECT cblscheduleno, howto, duration
           FROM asp_cblschedule
          WHERE assignedto = ? AND scheduledate = ?"
    );
    $taskStmt->bind_param('is', $userno, $today);
    $taskStmt->execute();
    $tasksResult = $taskStmt->get_result();

    if ($tasksResult->num_rows > 0 && !reminderSent($conn, $userno, null, 'morning')) {
        $plainTasks = '';
        $htmlTasks  = '<ul style="margin:15px 0; padding-left:25px; line-height:1.6;">';

        while ($t = $tasksResult->fetch_assoc()) {
            $taskId   = $t['cblscheduleno'];
            $howtoRaw = $t['howto'] ?? '';
            $duration = $t['duration'];

            // Plain text version
            $plainHowTo = html_entity_decode(strip_tags($howtoRaw), ENT_QUOTES, 'UTF-8');
            $plainTasks .= "Task #$taskId: $plainHowTo (Duration: {$duration}h)\n";

            // HTML version
            $safeHowTo = formatHowTo($howtoRaw);
            $htmlTasks .= "<li style=\"margin:10px 0;\">
                <strong style=\"color:#2c3e50;\">Task #$taskId</strong>
                <span style=\"color:#7f8c8d;font-size:0.9em;\"> (Duration: {$duration}h)</span><br>
                $safeHowTo
            </li>";
        }
        $htmlTasks .= '</ul>';

        // Email bodies
        $plainBody = "Good morning, $fullname!\n\nHere are your tasks for today:\n\n$plainTasks";
        $htmlBody  = "
        <html>
        <body style=\"font-family: 'Segoe UI', Arial, sans-serif; color: #333; background:#f9f9fb; padding:20px;\">
            <div style=\"max-width:600px; margin:auto; background:#fff; border-radius:10px; padding:25px; box-shadow:0 2px 10px rgba(0,0,0,0.1);\">
                <h2 style=\"color:#2c3e50; border-bottom:2px solid #3498db; padding-bottom:8px;\">Good morning, $fullname!</h2>
                <p style=\"font-size:16px;\">Here are your tasks for <strong>today</strong>:</p>
                $htmlTasks
                <hr style=\"border:0; border-top:1px solid #eee; margin:25px 0;\">
                <p style=\"font-size:13px; color:#95a5a6;\">
                    This is an automated reminder from <strong>WorkMate</strong> @ AGAMiLabs.
                </p>
            </div>
        </body>
        </html>";

        sendEmail($email, "Today's Tasks", $plainBody, $htmlBody);

        if (!empty($whatsapp)) {
            sendWhatsApp($whatsapp, $plainBody);
        }

        logReminder($conn, $userno, null, 'morning');
    }
    elseif ($tasksResult->num_rows === 0 && !reminderSent($conn, $userno, null, 'morning_admin')) {
        $body = "No task assigned for employee $fullname (UserID: $userno) for today.";
        sendEmail($admin_email, "No Task Assigned", $body, "<p>$body</p>");
        logReminder($conn, $userno, null, 'morning_admin');
    }

    $tasksResult->free();
}
$employees->free();

// ==================== 2. PROGRESS CHECK (HTML emails) ====================
$progressSQL = "
SELECT s.cblscheduleno, s.assignedto, s.assigntime, s.duration,
       u.email,
       CONCAT(IFNULL(u.countrycode,''),IFNULL(u.primarycontact,'')) AS whatsapp,
       CONCAT(u.firstname,' ',u.lastname) AS fullname,
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
    $taskId        = (int)$row['cblscheduleno'];
    $userno        = (int)$row['assignedto'];
    $email         = $row['email'];
    $whatsapp      = $row['whatsapp'];
    $fullname      = $row['fullname'];
    $durationHours = (float)$row['duration'];
    $assignTS      = strtotime($row['assigntime']);
    $elapsedHours  = ($now->getTimestamp() - $assignTS) / 3600;
    $expectedPct   = min(100, ($elapsedHours / $durationHours) * 100);
    $currentPct    = (int)$row['percentile'];

    $lastProgTS    = $row['last_progress'] ? strtotime($row['last_progress']) : $assignTS;
    $hoursNoProg   = ($now->getTimestamp() - $lastProgTS) / 3600;

    /* ---- Lagging Reminder ---- */
    if (($currentPct + $lag_buffer) < $expectedPct && !reminderSent($conn, $userno, $taskId, 'lagging')) {
        $plainBody = "Hi $fullname,\n\nTask #$taskId is lagging behind expected progress (expected ~" . round($expectedPct) . "%, current $currentPct%). Please update your progress.";
        $htmlBody  = "
        <html><body style=\"font-family:Arial,sans-serif;color:#333;\">
            <h3>Hi $fullname,</h3>
            <p><strong>Task #$taskId</strong> is <span style=\"color:#e74c3c;font-weight:bold;\">lagging behind</span> expected progress.</p>
            <ul>
                <li>Expected: ~" . round($expectedPct) . "%</li>
                <li>Current: $currentPct%</li>
            </ul>
            <p>Please update your progress now.</p>
        </body></html>";

        sendEmail($email, "Task #$taskId Lagging Reminder", $plainBody, $htmlBody);
        if (!empty($whatsapp)) sendWhatsApp($whatsapp, $plainBody);
        logReminder($conn, $userno, $taskId, 'lagging');
    }

    /* ---- Stalled Alert (Admin) ---- */
    if ($hoursNoProg >= $stalled_hours && !reminderSent($conn, $userno, $taskId, 'stalled')) {
        $plainBody = "Task #$taskId assigned to $fullname (UserID: $userno) has no progress for " . round($hoursNoProg, 1) . " hours.";
        $htmlBody  = "<p>$plainBody</p>";

        sendEmail($admin_email, "No Progress Update for Task #$taskId", $plainBody, $htmlBody);
        logReminder($conn, $userno, $taskId, 'stalled');
    }
}
$progressRes->free();
$conn->close();