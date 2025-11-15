<?php
/**
 * task_reminder.php – WITH backlogno + DEADLINE MONITORING
 * Runs daily at 8:30 AM Dhaka time
 * Features:
 *  - Uses BACKLOGNO (not cblscheduleno) in all messages
 *  - Morning task list (HTML + rich howto)
 *  - Lagging / Stalled / Deadline alerts
 *  - Personalized, beautiful emails
 *  - WhatsApp (plain text)
 *  - No duplicates
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

date_default_timezone_set('Asia/Dhaka');

// ==================== CONFIG ====================
$admin_email   = "agamilabs@gmail.com";
$CC_emails     = array_filter(array_map('trim', explode(',', "shmazumder23@gmail.com, shazzad@agamilabs.com")));
$from_email    = "noreply@workmate.agamilab.com";
$lag_buffer    = 10;
$stalled_hours = 1;

$enable_whatsapp    = false;
$callmebot_api_key  = "YOUR_CALLMEBOT_API_KEY";
// ===============================================

$base_path = dirname(dirname(dirname(__FILE__)));
require_once $base_path . "/db/Database.php";

$db   = new Database();
$conn = $db->db_connect();
if (!$db->is_connected()) die("DB failed");

$now   = new DateTime();
$today = $now->format('Y-m-d');
$now_ts = $now->getTimestamp();
// ===============================================

// ==================== HELPERS ====================
function sendEmail(string $to, string $subject, string $plain, string $html): void
{
    global $from_email, $CC_emails;
    $boundary = md5(time());
    $headers  = "From: AGAMiLabs <$from_email>\r\n";
    $headers .= "CC: " . implode(', ', $CC_emails) . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";

    $msg  = "--$boundary\r\n";
    $msg .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n$plain\r\n";
    $msg .= "--$boundary\r\n";
    $msg .= "Content-Type: text/html; charset=UTF-8\r\n\r\n$html\r\n";
    $msg .= "--$boundary--";

    mail($to, $subject, $msg, $headers);
}

function sendWhatsApp(?string $phone, string $msg): void
{
    global $enable_whatsapp, $callmebot_api_key;
    if (!$enable_whatsapp || !$phone || empty($callmebot_api_key)) return;
    $phone = preg_replace('/\D/', '', $phone);
    $phone = $phone[0] === '+' ? $phone : '+' . $phone;
    $text  = urlencode($msg);
    $url   = "https://api.callmebot.com/whatsapp.php?phone=$phone&text=$text&apikey=$callmebot_api_key";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    curl_close($ch);
}

function reminderSent(mysqli $conn, int $userno, ?int $backlogno, string $type): bool
{
    $sql = "SELECT 1 FROM asp_task_reminder_log WHERE userno = ? AND reminder_type = ? AND DATE(sent_time) = CURDATE()";
    $params = [$userno, $type];
    $types  = 'is';
    if ($backlogno !== null) {
        $sql .= " AND cblscheduleno = (SELECT cblscheduleno FROM asp_cblschedule WHERE backlogno = ? LIMIT 1)";
        $params[] = $backlogno;
        $types .= 'i';
    } else $sql .= " AND cblscheduleno IS NULL";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function logReminder(mysqli $conn, int $userno, ?int $backlogno, string $type): void
{
    $cblscheduleno = $backlogno ? $conn->query("SELECT cblscheduleno FROM asp_cblschedule WHERE backlogno = $backlogno LIMIT 1")->fetch_row()[0] ?? null : null;
    $stmt = $conn->prepare("INSERT INTO asp_task_reminder_log (userno, cblscheduleno, reminder_type) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $userno, $cblscheduleno, $type);
    $stmt->execute();
}

function formatHowTo(string $html): string
{
    $allowed = '<b><i><u><strong><em><a><ul><ol><li><br><p><blockquote><code><pre>';
    $safe = strip_tags($html, $allowed);
    return preg_replace(
        '#<a\s+href=["\']([^"\']+)["\'][^>]*>([^<]+)</a>#i',
        '<a href="$1" style="color:#0066cc;text-decoration:underline;">$2</a>',
        $safe
    );
}
// ===============================================

// ==================== 1. MORNING SUMMARY (backlogno) ====================
$empStmt = $conn->prepare(
    "SELECT u.userno, CONCAT(u.firstname,' ',u.lastname) AS fullname, u.email,
            CONCAT(IFNULL(u.countrycode,''),IFNULL(u.primarycontact,'')) AS whatsapp
       FROM hr_user u WHERE u.isactive = 1"
);
$empStmt->execute();
$employees = $empStmt->get_result();

while ($emp = $employees->fetch_assoc()) {
    $userno   = (int)$emp['userno'];
    $fullname = $emp['fullname'];
    $email    = $emp['email'];
    $whatsapp = $emp['whatsapp'];

    $taskStmt = $conn->prepare(
        "SELECT s.backlogno, s.howto, s.duration 
           FROM asp_cblschedule s 
          WHERE s.assignedto = ? AND s.scheduledate = ?"
    );
    $taskStmt->bind_param('is', $userno, $today);
    $taskStmt->execute();
    $tasksResult = $taskStmt->get_result();

    if ($tasksResult->num_rows > 0 && !reminderSent($conn, $userno, null, 'morning')) {
        $plainTasks = $htmlTasks = '<ul style="margin:15px 0;padding-left:25px;line-height:1.6;">';
        while ($t = $tasksResult->fetch_assoc()) {
            $backlogno = $t['backlogno'];
            $how = $t['howto'] ?? '';
            $dur = $t['duration'];

            $plainHow = html_entity_decode(strip_tags($how), ENT_QUOTES, 'UTF-8');
            $plainTasks .= "Backlog #$backlogno: $plainHow (Duration: {$dur}h)\n";

            $safeHow = formatHowTo($how);
            $htmlTasks .= "<li style=\"margin:10px 0;\">
                <strong style=\"color:#2c3e50;\">Backlog #$backlogno</strong> 
                <span style=\"color:#7f8c8d;font-size:0.9em;\">(Duration: {$dur}h)</span><br>
                $safeHow
            </li>";
        }
        $htmlTasks .= '</ul>';

        $plainBody = "Good morning, $fullname!\n\nHere are your tasks for today:\n\n$plainTasks";
        $htmlBody  = "
        <html><body style=\"font-family:'Segoe UI',Arial,sans-serif;color:#333;background:#f9f9fb;padding:20px;\">
            <div style=\"max-width:600px;margin:auto;background:#fff;border-radius:10px;padding:25px;box-shadow:0 2px 10px rgba(0,0,0,0.1);\">
                <h2 style=\"color:#2c3e50;border-bottom:2px solid #3498db;padding-bottom:8px;\">Good morning, $fullname!</h2>
                <p style=\"font-size:16px;\">Your tasks for <strong>today</strong>:</p>
                $htmlTasks
                <hr style=\"border:0;border-top:1px solid #eee;margin:25px 0;\">
                <p style=\"font-size:13px;color:#95a5a6;\">Automated from <strong>WorkMate</strong> @ AGAMiLabs</p>
            </div>
        </body></html>";

        sendEmail($email, "Today's Tasks", $plainBody, $htmlBody);
        if (!empty($whatsapp)) sendWhatsApp($whatsapp, $plainBody);
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

// ==================== 2. PROGRESS + DEADLINE CHECK (backlogno) ====================
$progressSQL = "
SELECT s.backlogno, s.cblscheduleno, s.assignedto, s.assigntime, s.duration,
       u.email, CONCAT(IFNULL(u.countrycode,''),IFNULL(u.primarycontact,'')) AS whatsapp,
       CONCAT(u.firstname,' ',u.lastname) AS fullname,
       MAX(p.progresstime) AS last_progress,
       COALESCE(MAX(p.percentile),0) AS percentile,
       d.deadline
FROM asp_cblschedule s
JOIN hr_user u ON u.userno = s.assignedto
LEFT JOIN asp_cblprogress p ON p.cblscheduleno = s.cblscheduleno
LEFT JOIN asp_deadlines d ON d.cblscheduleno = s.cblscheduleno
WHERE s.scheduledate <= ? AND u.isactive = 1
GROUP BY s.cblscheduleno";

$progStmt = $conn->prepare($progressSQL);
$progStmt->bind_param('s', $today);
$progStmt->execute();
$progressRes = $progStmt->get_result();

while ($row = $progressRes->fetch_assoc()) {
    $backlogno     = (int)$row['backlogno'];
    $cblscheduleno = (int)$row['cblscheduleno'];
    $userno        = (int)$row['assignedto'];
    $email         = $row['email'];
    $whatsapp      = $row['whatsapp'];
    $fullname      = $row['fullname'];
    $durationHours = (float)$row['duration'];
    $assignTS      = strtotime($row['assigntime']);
    $elapsedHours  = ($now_ts - $assignTS) / 3600;
    $expectedPct   = min(100, ($elapsedHours / $durationHours) * 100);
    $currentPct    = (int)$row['percentile'];
    $lastProgTS    = $row['last_progress'] ? strtotime($row['last_progress']) : $assignTS;
    $hoursNoProg   = ($now_ts - $lastProgTS) / 3600;

    $deadlineStr   = $row['deadline'];
    $hasDeadline   = !empty($deadlineStr);
    $deadlineDate  = $hasDeadline ? new DateTime($deadlineStr) : null;
    $daysToDeadline = $hasDeadline ? (int)$deadlineDate->diff($now)->format('%r%a') : null;

    // === LAGGING ===
    if (($currentPct + $lag_buffer) < $expectedPct && !reminderSent($conn, $userno, $backlogno, 'lagging')) {
        $plain = "Hi $fullname,\n\nBacklog #$backlogno is lagging (expected ~" . round($expectedPct) . "%, current $currentPct%). Please update.";
        $html  = "<p>Hi <strong>$fullname</strong>,</p><p><strong>Backlog #$backlogno</strong> is <span style=\"color:#e74c3c;font-weight:bold;\">lagging</span>.</p><ul><li>Expected: ~" . round($expectedPct) . "%</li><li>Current: $currentPct%</li></ul><p>Please update now.</p>";
        sendEmail($email, "Backlog #$backlogno Lagging", $plain, $html);
        if (!empty($whatsapp)) sendWhatsApp($whatsapp, $plain);
        logReminder($conn, $userno, $backlogno, 'lagging');
    }

    // === STALLED ===
    if ($hoursNoProg >= $stalled_hours && !reminderSent($conn, $userno, $backlogno, 'stalled')) {
        $plain = "Backlog #$backlogno assigned to $fullname (UserID: $userno) has no progress for " . round($hoursNoProg, 1) . " hours.";
        $html  = "<p>$plain</p>";
        sendEmail($admin_email, "No Progress: Backlog #$backlogno", $plain, $html);
        logReminder($conn, $userno, $backlogno, 'stalled');
    }

    // === DEADLINE ALERTS ===
    if ($hasDeadline) {
        $dlFormatted = $deadlineDate->format('F j, Y');

        // Overdue
        if ($daysToDeadline < 0 && !reminderSent($conn, $userno, $backlogno, 'deadline_overdue')) {
            $daysLate = abs($daysToDeadline);
            $plain = "URGENT: Backlog #$backlogno is OVERDUE by $daysLate day(s)! Deadline was $dlFormatted.";
            $html  = "<p style=\"color:#c0392b;font-weight:bold;\">Backlog #$backlogno is <u>OVERDUE</u> by $daysLate day(s)!</p><p>Deadline: <strong>$dlFormatted</strong></p>";
            sendEmail($email, "OVERDUE: Backlog #$backlogno", $plain, $html);
            sendEmail($admin_email, "OVERDUE Alert: Backlog #$backlogno", $plain, $html);
            if (!empty($whatsapp)) sendWhatsApp($whatsapp, $plain);
            logReminder($conn, $userno, $backlogno, 'deadline_overdue');
        }

        // 1 Day Before
        elseif ($daysToDeadline == 1 && !reminderSent($conn, $userno, $backlogno, 'deadline_1day')) {
            $plain = "Reminder: Backlog #$backlogno is due TOMORROW ($dlFormatted).";
            $html  = "<p>Backlog #$backlogno is due <strong style=\"color:#e67e22;\">TOMORROW</strong> – $dlFormatted.</p>";
            sendEmail($email, "Due Tomorrow: Backlog #$backlogno", $plain, $html);
            if (!empty($whatsapp)) sendWhatsApp($whatsapp, $plain);
            logReminder($conn, $userno, $backlogno, 'deadline_1day');
        }

        // 3 Days Before
        elseif ($daysToDeadline == 3 && !reminderSent($conn, $userno, $backlogno, 'deadline_3day')) {
            $plain = "Heads up: Backlog #$backlogno deadline in 3 days ($dlFormatted).";
            $html  = "<p>Backlog #$backlogno deadline in <strong>3 days</strong> – $dlFormatted.</p>";
            sendEmail($email, "Deadline in 3 Days: Backlog #$backlogno", $plain, $html);
            logReminder($conn, $userno, $backlogno, 'deadline_3day');
        }
    }
}
$progressRes->free();
$conn->close();