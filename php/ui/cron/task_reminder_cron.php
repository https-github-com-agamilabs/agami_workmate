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
 *  - NEW: Hourly task update reminders to employees with progress summary
 *  - NEW: No-task reminders to admin
 *  - NEW: Start-of-day reminders to all employees (at 9 AM)
 *  - NEW: Daily summaries to admin (at 9 AM for previous day, at 6 PM for current day)
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

date_default_timezone_set('Asia/Dhaka');

$now = new DateTime();
$hour = (int)$now->format('H');

// === ONLY RUN DURING WORK HOURS (9 AM – 6 PM) ===
// if ($hour < 9 || $hour > 18) exit("Outside work hours\n");

// Config
$admin_email = "agamilabs@gmail.com";
$CC_emails   = ["shmazumder23@gmail.com", "shazzad@agamilabs.com"];
$from_email  = "noreply@workmate.agamilab.com";
$lag_buffer  = 15;  // % buffer
$stalled_hours = 2; // no progress

$enable_whatsapp = true;
$callmebot_api_key = "YOUR_KEY";
$textmebot_api_key = "XUonpYwBBxqt";

$base_path = dirname(dirname(dirname(__FILE__)));
require_once $base_path . "/db/Database.php";

$db = new Database();
$conn = $db->db_connect();
if (!$db->is_connected()) exit("DB failed");

$today = $now->format('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
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

// function sendWhatsApp($phone, $msg) {
//     global $enable_whatsapp, $callmebot_api_key;
//     if (!$enable_whatsapp || !$phone) return;
//     $phone = preg_replace('/\D/', '', $phone); $phone = '+' . $phone;
//     $url = "https://api.callmebot.com/whatsapp.php?phone=$phone&text=" . urlencode($msg) . "&apikey=$callmebot_api_key";
//     @file_get_contents($url);
// }

function sendWhatsApp($phone, $msg) {
    global $enable_whatsapp, $callmebot_api_key, $textmebot_api_key;
    if (!$enable_whatsapp || !$phone) return;
    $phone = preg_replace('/\D/', '', $phone);
    $phone = '+' . $phone;
    // $url = "https://api.callmebot.com/whatsapp.php?phone=$phone&text=" . urlencode($msg) . "&apikey=" . urlencode($callmebot_api_key);
    // $url = "http://api.textmebot.com/send.php?recipient=+8801843730611&apikey=XUonpYwBBxqt&text=This%20is%20a%20test";
    
    $url = "https://api.textmebot.com/send.php?phone=$phone&text=" . urlencode($msg) . "&apikey=" . urlencode($textmebot_api_key);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $resp = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    // optionally log $resp / $err
}


// function reminderSent($conn, $userno, $backlogno, $type, $level = null) {
//     $sql = "SELECT level FROM asp_task_reminder_log 
//             WHERE userno = ? AND cblscheduleno in (SELECT cblscheduleno FROM asp_cblschedule WHERE backlogno = ?)
//               AND reminder_type = ? AND DATE(sent_time) = CURDATE()";
//     if ($level) $sql .= " AND level = ?";
//     $stmt = $conn->prepare($sql);
//     if ($level) $stmt->bind_param('iisi', $userno, $backlogno, $type, $level);
//     else $stmt->bind_param('iis', $userno, $backlogno, $type);
//     $stmt->execute();
//     return $stmt->get_result()->num_rows > 0;
// }

function reminderSent($conn, $userno, $backlogno, $type, $level = null) {
    $curdate = date('Y-m-d');
    $sql = "SELECT level FROM asp_task_reminder_log 
            WHERE userno = ? AND cblscheduleno in (SELECT cblscheduleno FROM asp_cblschedule WHERE backlogno = ?)
              AND reminder_type = ? AND DATE(sent_time) = ?";
    if ($level !== null) $sql .= " AND level = ?";
    $stmt = $conn->prepare($sql);
    if ($level !== null) {
        $stmt->bind_param('iisis', $userno, $backlogno, $type, $curdate, $level);
    } else {
        $stmt->bind_param('iiss', $userno, $backlogno, $type, $curdate);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $found = $res->num_rows > 0;
    $stmt->close();
    return $found;
}

// function logReminder($conn, $userno, $backlogno, $type, $level = 1) {
//     $cbl = $conn->query("SELECT cblscheduleno FROM asp_cblschedule WHERE backlogno = $backlogno LIMIT 1")->fetch_row()[0] ?? null;
//     $stmt = $conn->prepare("INSERT INTO asp_task_reminder_log (userno, cblscheduleno, reminder_type, level) VALUES (?, ?, ?, ?)");
//     $stmt->bind_param('iisi', $userno, $cbl, $type, $level);
//     $stmt->execute();
// }

function logReminder($conn, $userno, $backlogno, $type, $level = 1) {
    $cbl = null;
    $stmtCbl = $conn->prepare("SELECT cblscheduleno FROM asp_cblschedule WHERE backlogno = ? LIMIT 1");
    $stmtCbl->bind_param('i', $backlogno);
    $stmtCbl->execute();
    $resCbl = $stmtCbl->get_result();
    if ($row = $resCbl->fetch_assoc()) $cbl = (int)$row['cblscheduleno'];
    $stmtCbl->close();

    $stmt = $conn->prepare("INSERT INTO asp_task_reminder_log (userno, cblscheduleno, reminder_type, level) VALUES (?, ?, ?, ?)");
    // allow NULL for cblscheduleno
    if ($cbl === null) {
        $null = null;
        $stmt->bind_param('iisi', $userno, $null, $type, $level);
    } else {
        $stmt->bind_param('iisi', $userno, $cbl, $type, $level);
    }
    $stmt->execute();
    $stmt->close();
}

function getUserEmailAndPhone($conn, $userno) {
    $stmt = $conn->prepare("SELECT email, CONCAT(IFNULL(countrycode,''),IFNULL(primarycontact,'')) AS whatsapp FROM hr_user WHERE userno = ?");
    $stmt->bind_param('i', $userno);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getUserFullName($conn, $userno) {
    $stmt = $conn->prepare("SELECT CONCAT(firstname,' ',lastname) AS fullname FROM hr_user WHERE userno = ?");
    $stmt->bind_param('i', $userno);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['fullname'] ?? 'Unknown';
}

// === MAIN QUERY FOR LAGGING/STALLED/DEADLINES ===
$sql = "
SELECT s.backlogno, s.cblscheduleno, s.assignedto, s.assigntime, s.duration,
       u.email, CONCAT(IFNULL(u.countrycode,''),IFNULL(u.primarycontact,'')) AS whatsapp,
       CONCAT(u.firstname,' ',u.lastname) AS fullname,
       lp.progresstime AS last_progress,
       COALESCE(lp.percentile,0) AS percentile,
       d.deadline
FROM asp_cblschedule s
JOIN hr_user u ON u.userno = s.assignedto
LEFT JOIN (
    SELECT p1.cblscheduleno, p1.progresstime, p1.percentile
    FROM asp_cblprogress p1
    JOIN (
        SELECT cblscheduleno, MAX(progresstime) AS max_progresstime
        FROM asp_cblprogress
        GROUP BY cblscheduleno
    ) p2 ON p1.cblscheduleno = p2.cblscheduleno AND p1.progresstime = p2.max_progresstime
) lp ON lp.cblscheduleno = s.cblscheduleno
LEFT JOIN asp_deadlines d ON d.cblscheduleno = s.cblscheduleno
WHERE s.scheduledate <= ? AND u.isactive = 1
GROUP BY s.cblscheduleno";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $today);
$stmt->execute();
$res = $stmt->get_result();

$user_tasks = []; // Collect for hourly summaries

while ($r = $res->fetch_assoc()) {
    $backlogno = (int)$r['backlogno'];
    $userno = (int)$r['assignedto'];
    $email = $r['email'];
    $whatsapp = $r['whatsapp'];
    $fullname = $r['fullname'];
    $duration = (float)$r['duration'];
    $assignTS = strtotime($r['assigntime']);
    $elapsed = ($now_ts - $assignTS) / 3600;
    $expected = 0;
    if ($duration <= 0) {
        // If duration unknown/zero assume expected = 100% after long elapsed, or set safer default
        $expected = $elapsed >= 1 ? 100 : 0;
    } else {
        $expected = min(100, ($elapsed / $duration) * 100);
    }
    $current = (int)$r['percentile'];
    $lastProg = $r['last_progress'] ? strtotime($r['last_progress']) : $assignTS;
    $noProgHrs = ($now_ts - $lastProg) / 3600;

    $deadline = $r['deadline'] ? new DateTime($r['deadline']) : null;
    $hoursToDL = $deadline ? ($deadline->getTimestamp() - $now_ts) / 3600 : null;
    $dlStr = $deadline ? $deadline->format('M j, Y g:i A') : 'None';

    // Collect for hourly summary
    $user_tasks[$userno][] = [
        'backlogno' => $backlogno,
        'percentile' => $current,
        'expected' => round($expected),
        'duration' => $duration,
        'deadline' => $dlStr
    ];

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

// === 1. HOURLY TASK UPDATE REMINDERS WITH PROGRESS SUMMARY ===
foreach ($user_tasks as $userno => $tasks) {
    $user_info = getUserEmailAndPhone($conn, $userno);
    $email = $user_info['email'];
    $whatsapp = $user_info['whatsapp'];
    $fullname = getUserFullName($conn, $userno);

    $plain = "Hi $fullname,\n\nYour hourly task progress summary:\n";
    $html = "<p>Hi <strong>$fullname</strong>,</p><h3>Hourly Task Progress Summary</h3><ul>";
    
    foreach ($tasks as $t) {
        $plain .= "- Task #{$t['backlogno']}: {$t['percentile']}% done (expected: {$t['expected']}%). Deadline: {$t['deadline']}\n";
        $html .= "<li><strong>Task #{$t['backlogno']}</strong>: {$t['percentile']}% done (expected: {$t['expected']}%). Deadline: {$t['deadline']}</li>";
    }
    
    $plain .= "\nPlease update your progress if needed.";
    $html .= "</ul><p>Please update your progress if needed.</p>";
    
    sendEmail($email, "Hourly Task Progress Summary", $plain, $html);
    if ($whatsapp) sendWhatsApp($whatsapp, $plain);
}

// === 2. EMPLOYEES WITH NO TASKS - REMINDER TO ADMIN ===
$sql_no_tasks = "
SELECT u.userno, CONCAT(u.firstname,' ',u.lastname) AS fullname
FROM hr_user u
JOIN com_userorg uo ON uo.userno = u.userno
WHERE u.isactive = 1 AND uo.isactive = 1
AND NOT EXISTS (
    SELECT 1 FROM asp_cblschedule s
    LEFT JOIN asp_cblprogress p ON p.cblscheduleno = s.cblscheduleno
    AND p.progresstime = (SELECT MAX(p2.progresstime) FROM asp_cblprogress p2 WHERE p2.cblscheduleno = s.cblscheduleno)
    WHERE s.assignedto = u.userno AND (p.wstatusno IS NULL OR p.wstatusno IN (1,2))
)";

$res_no = $conn->query($sql_no_tasks);
$no_task_list = [];

while ($r = $res_no->fetch_assoc()) {
    $no_task_list[] = $r['fullname'];
}

if (!empty($no_task_list)) {
    $msg = "Employees with no active tasks: " . implode(', ', $no_task_list);
    sendEmail($admin_email, "No Tasks Alert", $msg, "<p>$msg</p>");
}

// === 3. START OF DAY REMINDER TO ALL EMPLOYEES (9 AM) ===
if ($hour == 9) {
    $sql_all_employees = "
    SELECT u.userno, u.email, CONCAT(IFNULL(u.countrycode,''),IFNULL(u.primarycontact,'')) AS whatsapp,
           CONCAT(u.firstname,' ',u.lastname) AS fullname
    FROM hr_user u
    JOIN com_userorg uo ON uo.userno = u.userno
    WHERE u.isactive = 1 AND uo.isactive = 1";

    $res_all = $conn->query($sql_all_employees);

    while ($r = $res_all->fetch_assoc()) {
        $userno = (int)$r['userno'];
        $email = $r['email'];
        $whatsapp = $r['whatsapp'];
        $fullname = $r['fullname'];

        // Get today's tasks
        $sql_tasks = "
        SELECT s.backlogno, cb.story, s.duration, d.deadline
        FROM asp_cblschedule s
        JOIN asp_channelbacklog cb ON cb.backlogno = s.backlogno
        LEFT JOIN asp_deadlines d ON d.cblscheduleno = s.cblscheduleno
        LEFT JOIN asp_cblprogress p ON p.cblscheduleno = s.cblscheduleno
        AND p.progresstime = (SELECT MAX(p2.progresstime) FROM asp_cblprogress p2 WHERE p2.cblscheduleno = s.cblscheduleno)
        WHERE s.assignedto = ? AND s.scheduledate = ?
        AND (p.wstatusno IS NULL OR p.wstatusno IN (1,2))";

        $stmt_tasks = $conn->prepare($sql_tasks);
        $stmt_tasks->bind_param('is', $userno, $today);
        $stmt_tasks->execute();
        $res_tasks = $stmt_tasks->get_result();

        $plain = "Good morning, $fullname!\n\nYour tasks for today:\n";
        $html = "<p>Good morning, <strong>$fullname</strong>!</p><h3>Your tasks for today:</h3><ul>";

        $has_tasks = false;
        while ($t = $res_tasks->fetch_assoc()) {
            $has_tasks = true;
            $dl = $t['deadline'] ? date('M j, Y g:i A', strtotime($t['deadline'])) : 'None';
            $plain .= "- Task #{$t['backlogno']}: {$t['story']} (Duration: {$t['duration']} hrs, Deadline: $dl)\n";
            $html .= "<li><strong>Task #{$t['backlogno']}</strong>: {$t['story']} (Duration: {$t['duration']} hrs, Deadline: $dl)</li>";
        }

        if (!$has_tasks) {
            $plain .= "No tasks scheduled for today.\n";
            $html .= "<li>No tasks scheduled for today.</li>";
        }

        $plain .= "\nHave a productive day!";
        $html .= "</ul><p>Have a productive day!</p>";

        sendEmail($email, "Start of Day Reminder", $plain, $html);
        if ($whatsapp) sendWhatsApp($whatsapp, $plain);
    }
}

// === 4. SUMMARY OF WORKS TO ADMIN (9 AM: Previous Day, 6 PM: Today) ===
if ($hour == 9 || $hour == 18) {
    $summary_date = ($hour == 9) ? $yesterday : $today;
    $period = ($hour == 9) ? "Previous Day's" : "Today's";
    
    // Query completed tasks and progress updates
    $sql_summary = "
    SELECT u.userno, CONCAT(u.firstname,' ',u.lastname) AS fullname,
           GROUP_CONCAT(DISTINCT CONCAT('Task #', s.backlogno, ': ', cb.story) SEPARATOR '\n') AS completed_tasks
    FROM asp_cblprogress p
    JOIN asp_cblschedule s ON s.cblscheduleno = p.cblscheduleno
    JOIN asp_channelbacklog cb ON cb.backlogno = s.backlogno
    JOIN hr_user u ON u.userno = s.assignedto
    WHERE DATE(p.progresstime) = ? AND p.wstatusno = 3
    GROUP BY u.userno";

    $stmt_summary = $conn->prepare($sql_summary);
    $stmt_summary->bind_param('s', $summary_date);
    $stmt_summary->execute();
    $res_summary = $stmt_summary->get_result();

    $plain = "$period Work Summary:\n";
    $html = "<h3>$period Work Summary</h3><ul>";

    $has_activity = false;
    while ($r = $res_summary->fetch_assoc()) {
        $has_activity = true;
        $fullname = $r['fullname'];
        $tasks = $r['completed_tasks'] ? str_replace("\n", "\n- ", $r['completed_tasks']) : 'No completions';
        $plain .= "$fullname:\n- $tasks\n\n";
        $html .= "<li><strong>$fullname</strong>:<ul><li>" . str_replace("\n", "</li><li>", $r['completed_tasks']) . "</li></ul></li>";
    }

    if (!$has_activity) {
        $plain .= "No completions recorded.\n";
        $html .= "<li>No completions recorded.</li>";
    }

    sendEmail($admin_email, "$period Work Summary", $plain, $html);
}

$conn->close();