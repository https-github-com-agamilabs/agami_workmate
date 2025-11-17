<?php
/**
 * task_monitor.php – HOURLY PROGRESS + DEADLINE WATCHDOG
 * Runs: Every hour, 9 AM – 6 PM Dhaka time
 * Features:
 *  - Hourly lagging/stalled checks
 *  - Escalating alerts (1st, 2nd, 3rd)
 *  - Deadline countdown (5h, 2h, 1h, overdue)
 *  - Uses BACKLOGNO
 *  - HTML + WhatsApp
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

date_default_timezone_set('Asia/Dhaka');

$now = new DateTime();
$hour = (int)$now->format('H');

// === ONLY RUN DURING WORK HOURS (9 AM – 6 PM) ===
if ($hour < 9 || $hour > 18) exit("Outside work hours\n");

// Config
$admin_email = "agamilabs@gmail.com";
$CC_emails   = ["shmazumder23@gmail.com", "shazzad@agamilabs.com"];
$from_email  = "noreply@workmate.agamilab.com";
$lag_buffer  = 15;  // % buffer
$stalled_hours = 2; // no progress

$enable_whatsapp = false;
$callmebot_api_key = "YOUR_KEY";

$base_path = dirname(dirname(dirname(__FILE__)));
require_once $base_path . "/db/Database.php";

$db = new Database();
$conn = $db->db_connect();
if (!$db->is_connected()) exit("DB failed");

$today = $now->format('Y-m-d');
$now_ts = $now->getTimestamp();

// === HELPERS ===
function sendEmail($to, $sub, $plain, $html) {
    global $from_email, $CC_emails;
    $b = md5(time());
    $h  = "From: AGAMiLabs <$from_email>\r\n";
    $h .= "CC: " . implode(', ', $CC_emails) . "\r\n";
    $h .= "MIME-Version: 1.0\r\nContent-Type: multipart/alternative; boundary=\"$b\"\r\n";
    $m  = "--$b\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n$plain\r\n";
    $m .= "--$b\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n$html\r\n";
    $m .= "--$b--";
    mail($to, $sub, $m, $h);
}

function sendWhatsApp($phone, $msg) {
    global $enable_whatsapp, $callmebot_api_key;
    if (!$enable_whatsapp || !$phone) return;
    $phone = preg_replace('/\D/', '', $phone); $phone = '+' . $phone;
    $url = "https://api.callmebot.com/whatsapp.php?phone=$phone&text=" . urlencode($msg) . "&apikey=$callmebot_api_key";
    @file_get_contents($url);
}

function reminderSent($conn, $userno, $backlogno, $type, $level = null) {
    $sql = "SELECT level FROM asp_task_reminder_log 
            WHERE userno = ? AND cblscheduleno in (SELECT cblscheduleno FROM asp_cblschedule WHERE backlogno = ?)
              AND reminder_type = ? AND DATE(sent_time) = CURDATE()";
    if ($level) $sql .= " AND level = ?";
    $stmt = $conn->prepare($sql);
    if ($level) $stmt->bind_param('iisi', $userno, $backlogno, $type, $level);
    else $stmt->bind_param('iis', $userno, $backlogno, $type);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function logReminder($conn, $userno, $backlogno, $type, $level = 1) {
    $cbl = $conn->query("SELECT cblscheduleno FROM asp_cblschedule WHERE backlogno = $backlogno LIMIT 1")->fetch_row()[0] ?? null;
    $stmt = $conn->prepare("INSERT INTO asp_task_reminder_log (userno, cblscheduleno, reminder_type, level) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iisi', $userno, $cbl, $type, $level);
    $stmt->execute();
}

// === MAIN QUERY ===
$sql = "
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

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $today);
$stmt->execute();
$res = $stmt->get_result();

while ($r = $res->fetch_assoc()) {
    $backlogno = (int)$r['backlogno'];
    $userno = (int)$r['assignedto'];
    $email = $r['email'];
    $whatsapp = $r['whatsapp'];
    $fullname = $r['fullname'];
    $duration = (float)$r['duration'];
    $assignTS = strtotime($r['assigntime']);
    $elapsed = ($now_ts - $assignTS) / 3600;
    $expected = min(100, ($elapsed / $duration) * 100);
    $current = (int)$r['percentile'];
    $lastProg = $r['last_progress'] ? strtotime($r['last_progress']) : $assignTS;
    $noProgHrs = ($now_ts - $lastProg) / 3600;

    $deadline = $r['deadline'] ? new DateTime($r['deadline']) : null;
    $hoursToDL = $deadline ? ($deadline->getTimestamp() - $now_ts) / 3600 : null;

    // === LAGGING (Escalating) ===
    if (($current + $lag_buffer) < $expected) {
        $level = 1;
        if (reminderSent($conn, $userno, $backlogno, 'lagging', 3)) continue;
        if (reminderSent($conn, $userno, $backlogno, 'lagging', 2)) $level = 3;
        elseif (reminderSent($conn, $userno, $backlogno, 'lagging', 1)) $level = 2;

        $msg = "Backlog #$backlogno is lagging ($current% done, ~" . round($expected) . "% expected).";
        if ($level > 1) $msg = "URGENT: $msg [Escalated]";

        $plain = "Hi $fullname,\n\n$msg Please update now.";
        $html  = "<p>Hi <strong>$fullname</strong>,</p><p style=\"color:#e74c3c;\"><strong>Backlog #$backlogno</strong> is <u>lagging</u>.</p><ul><li>Done: $current%</li><li>Expected: ~" . round($expected) . "%</li></ul><p><strong>Update now!</strong></p>";
        sendEmail($email, "Lagging: Backlog #$backlogno", $plain, $html);
        if ($whatsapp) sendWhatsApp($whatsapp, $plain);
        logReminder($conn, $userno, $backlogno, 'lagging', $level);
    }

    // === STALLED ===
    if ($noProgHrs >= $stalled_hours && !reminderSent($conn, $userno, $backlogno, 'stalled')) {
        $plain = "Backlog #$backlogno ($fullname) – no progress for " . round($noProgHrs, 1) . " hours.";
        sendEmail($admin_email, "Stalled: Backlog #$backlogno", $plain, "<p>$plain</p>");
        logReminder($conn, $userno, $backlogno, 'stalled');
    }

    // === DEADLINE COUNTDOWN ===
    if ($deadline && $hoursToDL !== null) {
        $dlStr = $deadline->format('M j, Y g:i A');

        // Overdue
        if ($hoursToDL < 0 && !reminderSent($conn, $userno, $backlogno, 'deadline_overdue')) {
            $hrsLate = round(abs($hoursToDL), 1);
            $plain = "OVERDUE: Backlog #$backlogno is $hrsLate hours late! (Due: $dlStr)";
            $html  = "<p style=\"color:#c0392b;font-weight:bold;\">OVERDUE: Backlog #$backlogno is $hrsLate hours late!</p><p>Due: <strong>$dlStr</strong></p>";
            sendEmail($email, "OVERDUE: Backlog #$backlogno", $plain, $html);
            sendEmail($admin_email, "OVERDUE: Backlog #$backlogno", $plain, $html);
            if ($whatsapp) sendWhatsApp($whatsapp, $plain);
            logReminder($conn, $userno, $backlogno, 'deadline_overdue');
        }
        // Countdown
        elseif ($hoursToDL > 0) {
            $msg = $hours = null;
            if ($hoursToDL <= 1 && !reminderSent($conn, $userno, $backlogno, 'deadline_1h')) {
                $msg = "Backlog #$backlogno due in <1 hour! ($dlStr)"; $hours = '1h';
            } elseif ($hoursToDL <= 2 && !reminderSent($conn, $userno, $backlogno, 'deadline_2h')) {
                $msg = "Backlog #$backlogno due in ~2 hours ($dlStr)"; $hours = '2h';
            } elseif ($hoursToDL <= 5 && !reminderSent($conn, $userno, $backlogno, 'deadline_5h')) {
                $msg = "Backlog #$backlogno due in ~5 hours ($dlStr)"; $hours = '5h';
            }
            if ($msg) {
                $plain = "Hi $fullname,\n\n$msg";
                $html  = "<p>Hi <strong>$fullname</strong>,</p><p style=\"color:#e67e22;\"><strong>Backlog #$backlogno</strong> is due in <strong>$hours</strong>!</p><p>Deadline: <strong>$dlStr</strong></p>";
                sendEmail($email, "Due Soon: Backlog #$backlogno", $plain, $html);
                if ($whatsapp) sendWhatsApp($whatsapp, $plain);
                logReminder($conn, $userno, $backlogno, "deadline_$hours");
            }
        }
    }
}
$conn->close();