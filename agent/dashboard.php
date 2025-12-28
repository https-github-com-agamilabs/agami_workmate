<?php
session_start();

// Simple auth check: if not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// load db.php
require_once 'db.php';

try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
} catch (mysqli_sql_exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Get all employees (user_id distinct)
$usersResult = $conn->query("SELECT DISTINCT user_id FROM app_usage ORDER BY user_id");
$users = [];
while ($row = $usersResult->fetch_assoc()) {
    $users[] = $row['user_id'];
}

// Default: show first user, or user from GET param
$selectedUser = isset($_GET['user_id']) ? $_GET['user_id'] : ($users[0] ?? '');
// Default: today
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Query: top apps by this user (total duration) for the selected date
$stmt = $conn->prepare("
  SELECT app_name, SUM(duration) as total_duration, COUNT(*) as num_entries
  FROM app_usage
  WHERE user_id = ? AND DATE(created_at) = ?
  GROUP BY app_name
  ORDER BY total_duration DESC
");
$stmt->bind_param("ss", $selectedUser, $selectedDate);
$stmt->execute();
$appsResult = $stmt->get_result();
$topApps = [];
while ($row = $appsResult->fetch_assoc()) {
    $topApps[] = $row;
}

// Query: daily activity (entries per day) - keeping this historical context
$stmt = $conn->prepare("
  SELECT DATE(created_at) as activity_date, COUNT(*) as num_entries, SUM(duration) as total_duration
  FROM app_usage
  WHERE user_id = ?
  GROUP BY DATE(created_at)
  ORDER BY activity_date DESC
  LIMIT 30
");
$stmt->bind_param("s", $selectedUser);
$stmt->execute();
$dailyResult = $stmt->get_result();
$dailyActivity = [];
while ($row = $dailyResult->fetch_assoc()) {
    $dailyActivity[] = $row;
}
$dailyActivity = array_reverse($dailyActivity); // chronological order for chart

// Query: online percentage for the selected date
$onlineStmt = $conn->prepare("
  SELECT COUNT(*) as total, SUM(CASE WHEN online=1 THEN 1 ELSE 0 END) as online_count
  FROM app_usage
  WHERE user_id = ? AND DATE(created_at) = ?
");
$onlineStmt->bind_param("ss", $selectedUser, $selectedDate);
$onlineStmt->execute();
$onlineResult = $onlineStmt->get_result();
$onlineData = $onlineResult->fetch_assoc();
$onlinePercent = $onlineData['total'] > 0 ? round(($onlineData['online_count'] / $onlineData['total']) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkmateApp Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        header { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 28px; color: #333; }
        header a { color: #0066cc; text-decoration: none; padding: 8px 12px; border-radius: 4px; }
        header a:hover { background: #f0f0f0; }
        .controls { margin-bottom: 20px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; gap: 20px; align-items: center; }
        .controls label { font-weight: 600; margin-right: 5px; }
        .controls select, .controls input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .controls button { padding: 8px 16px; background: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .controls button:hover { background: #0052a3; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card h2 { font-size: 18px; margin-bottom: 15px; color: #333; }
        .stat { font-size: 32px; font-weight: bold; color: #0066cc; }
        .stat-label { font-size: 14px; color: #666; margin-top: 5px; }
        .full-width { grid-column: 1 / -1; }
        table { width: 100%; border-collapse: collapse; }
        table th { background: #f9f9f9; padding: 10px; text-align: left; font-weight: 600; border-bottom: 2px solid #eee; }
        table td { padding: 10px; border-bottom: 1px solid #eee; }
        table tr:hover { background: #f9f9f9; }
        .footer { text-align: center; margin-top: 40px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📊 WorkmateApp Dashboard</h1>
            <a href="logout.php">Logout</a>
        </header>

        <div class="controls">
            <form method="GET" style="display: flex; gap: 20px; align-items: center; width: 100%;">
                <div>
                    <label>Select Employee:</label>
                    <select name="user_id" onchange="this.form.submit()">
                        <?php foreach ($users as $uid): ?>
                            <option value="<?php echo htmlspecialchars($uid); ?>" <?php echo $selectedUser === $uid ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($uid); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Date:</label>
                    <input type="date" name="date" value="<?php echo htmlspecialchars($selectedDate); ?>" onchange="this.form.submit()">
                </div>
            </form>
        </div>

        <div class="grid">
            <div class="card">
                <h2>Online Status (<?php echo htmlspecialchars($selectedDate); ?>)</h2>
                <div class="stat"><?php echo $onlinePercent; ?>%</div>
                <div class="stat-label">of time connected to internet</div>
            </div>
            <div class="card">
                <h2>Total Activity Entries (<?php echo htmlspecialchars($selectedDate); ?>)</h2>
                <div class="stat"><?php echo $onlineData['total']; ?></div>
                <div class="stat-label">tracked intervals</div>
            </div>
        </div>

        <div class="grid full-width">
            <div class="card">
                <h2>Captured Screenshots (<?php echo htmlspecialchars($selectedDate); ?>)</h2>
                <?php
                $screenStmt = $conn->prepare("
                  SELECT created_at, app_name, screenshot_path
                  FROM app_usage
                  WHERE user_id = ? AND DATE(created_at) = ? AND screenshot_path IS NOT NULL
                  ORDER BY created_at DESC
                ");
                $screenStmt->bind_param("ss", $selectedUser, $selectedDate);
                $screenStmt->execute();
                $screenResult = $screenStmt->get_result();
                $screenshots = [];
                while ($row = $screenResult->fetch_assoc()) {
                    $screenshots[] = $row;
                }
                ?>
                <?php if (!empty($screenshots)): ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                        <?php foreach ($screenshots as $scr): ?>
                            <div style="border: 1px solid #eee; padding: 5px; border-radius: 4px;">
                                <a href="<?php echo htmlspecialchars($scr['screenshot_path']); ?>" target="_blank">
                                    <img src="<?php echo htmlspecialchars($scr['screenshot_path']); ?>" style="width: 100%; height: 120px; object-fit: cover; border-radius: 4px;" alt="Screenshot">
                                </a>
                                <div style="font-size: 12px; margin-top: 5px; color: #666;">
                                    <strong><?php echo htmlspecialchars(date('H:i:s', strtotime($scr['created_at']))); ?></strong><br>
                                    <?php echo htmlspecialchars($scr['app_name']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #999;">No screenshots captured for this date.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid full-width">
            <div class="card">
                <h2>Top Applications by Duration</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Application</th>
                            <th>Total Duration (seconds)</th>
                            <th>Entries</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($topApps)): ?>
                            <?php foreach ($topApps as $app): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($app['app_name']); ?></td>
                                    <td><?php echo number_format($app['total_duration']); ?></td>
                                    <td><?php echo $app['num_entries']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align: center; color: #999;">No data available.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid full-width">
            <div class="card">
                <h2>Daily Activity (Last 30 Days)</h2>
                <canvas id="dailyChart"></canvas>
            </div>
        </div>

        <div class="footer">
            <p>WorkmateApp &copy; 2025 | Data is for authorized monitoring only.</p>
        </div>
    </div>

    <script>
        const dailyActivity = <?php echo json_encode($dailyActivity); ?>;
        const ctx = document.getElementById('dailyChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dailyActivity.map(d => d.activity_date),
                datasets: [
                    {
                        label: 'Total Duration (seconds)',
                        data: dailyActivity.map(d => d.total_duration),
                        borderColor: '#0066cc',
                        backgroundColor: 'rgba(0, 102, 204, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Auto-refresh on inactivity logic
        let inactivityTime = 0;
        const checkInterval = 1000; // Check every second
        const refreshLimit = 60000; // 1 minute in milliseconds

        function resetTimer() {
            inactivityTime = 0;
        }

        // track events
        window.onclick = resetTimer;
        window.onmousemove = resetTimer;
        window.onkeypress = resetTimer;
        window.onscroll = resetTimer;

        setInterval(() => {
            inactivityTime += checkInterval;
            if (inactivityTime >= refreshLimit) {
                location.reload();
            }
        }, checkInterval);
    </script>
</body>
</html>
