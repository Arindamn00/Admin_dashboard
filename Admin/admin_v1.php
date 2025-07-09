<?php
session_start();

// Demo admin credentials
$adminUsername = 'admin';
$adminPassword = 'password123';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
        $inputUsername = $_POST['username'];
        $inputPassword = $_POST['password'];

        if ($inputUsername === $adminUsername && $inputPassword === $adminPassword) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $loginError = "Invalid username or password.";
        }
    }

    // Display login form
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #fff 0%, #000 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fff;
            }

            .login-container {
                background: rgba(30, 30, 30, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                padding: 40px;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
                width: 100%;
                max-width: 400px;
                border: 1px solid #444;
            }

            .login-header {
                text-align: center;
                margin-bottom: 30px;
            }

            .login-header h1 {
                color: #fff;
                font-size: 2.5rem;
                margin-bottom: 10px;
            }

            .login-header p {
                color: #cccccc;
                font-size: 1.1rem;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                margin-bottom: 8px;
                color: #fff;
                font-weight: 600;
            }

            .form-group input {
                width: 100%;
                padding: 15px;
                border: 2px solid #444;
                border-radius: 10px;
                font-size: 16px;
                transition: all 0.3s ease;
                background: #222;
                color: #fff;
            }

            .form-group input:focus {
                outline: none;
                border-color: #fff;
                box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
                background: #111;
                color: #fff;
            }

            .login-btn {
                width: 100%;
                padding: 15px;
                background: color(#fff);
                color: #111;
                border: none;
                border-radius: 10px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                margin-top: 10px;
            }

            .login-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
            }

            .error-message {
                background: #444;
                color: #fff;
                padding: 12px;
                border-radius: 10px;
                margin-bottom: 20px;
                text-align: center;
                font-weight: 500;
            }

            .credentials-hint {
                background: #222;
                padding: 15px;
                border-radius: 10px;
                margin-top: 20px;
                border-left: 4px solid #444;
            }

            .credentials-hint h4 {
                color: #fff;
                margin-bottom: 5px;
            }

            .credentials-hint p {
                color: #ccc;
                font-size: 14px;
            }
        </style>
    </head>

    <body>
        <div class="login-container">
            <div class="login-header">
                <img src="img/lock.png" alt="Admin Logo" style="max-width: 80px; display: block; margin: 0 auto 15px auto;">
                <h1>Admin Login</h1>
                <p>Enter your credentials to access the dashboard</p>
            </div>

            <?php if (isset(
                $loginError
            )): ?>
                <div class="error-message">
                    <?php echo $loginError; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="login-btn">Login to Dashboard</button>
            </form>

            <!-- <div class="credentials-hint">
                <h4>Demo Credentials:</h4>
                <p>Username: admin<br>Password: password123</p>
            </div> -->
        </div>
    </body>

    </html>
<?php
    exit();
}

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    session_unset();
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "test";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get user statistics
function getUserStats($conn)
{
    $stats = [];
    // Total users
    $result = $conn->query("SELECT COUNT(*) as total_users FROM users");
    if ($result) {
        $stats['total_users'] = $result->fetch_assoc()['total_users'];
    } else {
        $stats['total_users'] = 0;
    }
    // Active users (logged in within last 30 days)
    $result = $conn->query("SELECT COUNT(*) as active_users FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    if ($result) {
        $stats['active_users'] = $result->fetch_assoc()['active_users'];
    } else {
        $stats['active_users'] = 0;
    }

    // New users this month
    $result = $conn->query("SELECT COUNT(*) as new_users FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    if ($result) {
        $stats['new_users'] = $result->fetch_assoc()['new_users'];
    } else {
        $stats['new_users'] = 0;
    }

    // Users by role
    $result = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $stats['users_by_role'] = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $stats['users_by_role'][$row['role']] = $row['count'];
        }
    }

    return $stats;
}

// Function to get recent users
function getRecentUsers($conn, $limit = 10)
{
    $result = $conn->query("SELECT id, username, email, role, created_at, last_login FROM users ORDER BY created_at DESC LIMIT $limit");
    $users = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    return $users;
}

// Function to get user activity data for chart
function getUserActivityData($conn)
{
    $result = $conn->query("SELECT DATE(created_at) as date, COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY DATE(created_at) ORDER BY date");
    $activity = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $activity[] = $row;
        }
    }
    return $activity;
}

// Get all statistics
$stats = getUserStats($conn);
$recentUsers = getRecentUsers($conn);
$activityData = getUserActivityData($conn);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - User Statistics</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fff 0%, #000 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgba(30, 30, 30, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .header h1 {
            color: #fff;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            color: #cccccc;
            font-size: 1.1em;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(40, 40, 40, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.35);
        }

        .stat-card h3 {
            color: #fff;
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #fff;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #cccccc;
            font-size: 0.9em;
        }

        .charts-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .chart-container {
            background: rgba(40, 40, 40, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .chart-container h3 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 1.3em;
        }

        .role-chart {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .role-bar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .role-name {
            min-width: 80px;
            font-weight: 500;
            color: #fff;
        }

        .role-progress {
            flex: 1;
            height: 20px;
            background: #222;
            border-radius: 10px;
            overflow: hidden;
        }

        .role-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #fff, #333);
            transition: width 0.5s ease;
        }

        .role-count {
            font-weight: bold;
            color: #fff;
        }

        .activity-chart {
            height: 200px;
            background: #222;
            border-radius: 10px;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .activity-bars {
            display: flex;
            align-items: end;
            justify-content: space-between;
            height: 100%;
            gap: 2px;
        }

        .activity-bar {
            background: linear-gradient(to top, #fff, #333);
            min-width: 8px;
            border-radius: 2px 2px 0 0;
            transition: all 0.3s ease;
        }

        .activity-bar:hover {
            background: linear-gradient(to top, #eee, #111);
        }

        .users-table-section {
            background: rgba(40, 40, 40, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .users-table-section h3 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 1.3em;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .users-table th,
        .users-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #333;
            color: #fff;
        }

        .users-table th {
            background: #222;
            font-weight: 600;
            color: #fff;
        }

        .users-table tr:hover {
            background: #333;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 500;
            background: #222;
            color: #fff;
            border: 1px solid #444;
        }

        .status-admin,
        .status-user,
        .status-moderator {
            background: #222 !important;
            color: #fff !important;
            border: 1px solid #444 !important;
        }

        @media (max-width: 768px) {
            .charts-section {
                grid-template-columns: 1fr;
            }

            .users-table {
                font-size: 0.9em;
            }

            .header h1 {
                font-size: 2em;
            }
        }

        .refresh-btn, .logout-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .refresh-btn {
            background: color(#fff);
            color: #111;
        }

        .logout-btn {
            background: linear-gradient(45deg, #fff, #fff);
            color: #111;
        }

        .logout-btn:hover,
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 255, 255, 0.2);
        }


        .refresh-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                <div style="text-align: left;">
                    <h1>Admin Dashboard</h1>
                    
                </div>
                <div style="display: flex; gap: 12px;">
                    <button class="refresh-btn" onclick="window.location.reload()">Refresh Data</button>
                    <a href="?logout=true" class="logout-btn">Logout</a>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="stat-number"><?php echo number_format($stats['total_users']); ?></div>
                <div class="stat-label">Registered accounts</div>
            </div>

            <div class="stat-card">
                <h3>Active Users</h3>
                <div class="stat-number"><?php echo number_format($stats['active_users']); ?></div>
                <div class="stat-label">Active in last 30 days</div>
            </div>

            <div class="stat-card">
                <h3>New Users</h3>
                <div class="stat-number"><?php echo number_format($stats['new_users']); ?></div>
                <div class="stat-label">Joined this month</div>
            </div>

            <div class="stat-card">
                <h3>Growth Rate</h3>
                <div class="stat-number"><?php echo $stats['total_users'] > 0 ? round(($stats['new_users'] / $stats['total_users']) * 100, 1) : 0; ?>%</div>
                <div class="stat-label">Monthly growth</div>
            </div>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <h3>Users by Role</h3>
                <div class="role-chart">
                    <?php if (!empty($stats['users_by_role'])): ?>
                        <?php
                        $maxCount = max($stats['users_by_role']);
                        foreach ($stats['users_by_role'] as $role => $count):
                            $percentage = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                        ?>
                            <div class="role-bar">
                                <div class="role-name"><?php echo ucfirst($role); ?></div>
                                <div class="role-progress">
                                    <div class="role-progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                                <div class="role-count"><?php echo $count; ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No role data available</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="chart-container">
                <h3>User Registration Activity (Last 30 Days)</h3>
                <div class="activity-chart">
                    <div class="activity-bars">
                        <?php if (!empty($activityData)): ?>
                            <?php
                            $maxActivity = max(array_column($activityData, 'count'));
                            foreach ($activityData as $day):
                                $height = $maxActivity > 0 ? ($day['count'] / $maxActivity) * 100 : 0;
                            ?>
                                <div class="activity-bar"
                                    style="height: <?php echo $height; ?>%"
                                    title="<?php echo $day['date'] . ': ' . $day['count'] . ' users'; ?>">
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No activity data available</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="users-table-section">
            <h3>Recent Users</h3>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Last Login</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentUsers)): ?>
                        <?php foreach ($recentUsers as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($user['role']); ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td><?php echo $user['last_login'] ? date('M j, Y', strtotime($user['last_login'])) : 'Never'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #7f8c8d;">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Add smooth animations on page load
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .chart-container, .users-table-section');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Animate progress bars
            setTimeout(() => {
                const progressBars = document.querySelectorAll('.role-progress-fill');
                progressBars.forEach(bar => {
                    const width = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = width;
                    }, 100);
                });
            }, 500);
        });

        // Add tooltips to activity bars
        document.querySelectorAll('.activity-bar').forEach(bar => {
            bar.addEventListener('mouseenter', function() {
                this.style.opacity = '0.8';
            });

            bar.addEventListener('mouseleave', function() {
                this.style.opacity = '1';
            });
        });
    </script>
</body>

</html>