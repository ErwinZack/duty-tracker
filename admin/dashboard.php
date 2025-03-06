<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch stats
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_students FROM students");
$stmt->execute();
$total_students = $stmt->fetch(PDO::FETCH_ASSOC)['total_students'];

$stmt = $pdo->prepare("SELECT COUNT(*) AS pending_logs FROM duty_logs WHERE status = 'Pending'");
$stmt->execute();
$pending_logs = $stmt->fetch(PDO::FETCH_ASSOC)['pending_logs'];

// Fetch approved duty logs
$stmt = $pdo->prepare("SELECT COUNT(*) AS approved_logs FROM duty_logs WHERE status = 'Approved'");
$stmt->execute();
$approved_logs = $stmt->fetch(PDO::FETCH_ASSOC)['approved_logs'];

// Fetch rejected duty logs
$stmt = $pdo->prepare("SELECT COUNT(*) AS rejected_logs FROM duty_logs WHERE status = 'Rejected'");
$stmt->execute();
$rejected_logs = $stmt->fetch(PDO::FETCH_ASSOC)['rejected_logs'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Add Chart.js library -->
</head>

<body>
<div class="dashboard-container">
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <header class="header-container">
            <div class="header-left">
                <div class="sidebar-toggle" id="menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
                <h2><i class="fa-solid fa-house"></i> Welcome to Admin Dashboard</h2>
            </div>
            
            <div class="header-right">
            <div class="date-picker-container">
            <i class="fa-regular fa-calendar"></i>
                <input type="text" id="dateRange" class="date-input" readonly>
            </div>
            <div class="notification-dropdown">
                <button class="notification-icon" id="notificationToggle">
                    <i class="fa-solid fa-bell"></i>
                    <span class="badge" id="notificationCount"></span>
                </button>

                <div class="dropdown-menu" id="notificationMenu">
                    <div class="notification-header">
                        <h4>Notifications</h4>
                        <button class="mark-all-read">
                            <img src="../assets/image/double-check.svg" alt="doublecheck"><span>Mark all as read</span>
                        </button>

                    </div>
                    <ul>
                        <li class="unread">
                            <img src="../assets/image/user1.jpg" class="profile-notif" alt="default">
                            <div class="notification-content">
                                <p><strong>New student registered</strong> Added a student</p><br>
                                <small>Thursday 4:20pm</small>
                            </div>
                            <span class="time-ago">2 hours ago</span>
                            <span class="unread-dot"></span>
                        </li>
                        
                        <li class="unread">
                            <img src="../assets/image/user2.jpg" class="profile-notif" alt="default">
                            <div class="notification-content">
                                <p><strong>Pending duty log</strong>
                                 needs review</p><br>
                                <small>Thursday 3:12pm</small>
                            </div>
                            <span class="time-ago">3 hours ago</span>
                            <span class="unread-dot"></span>
                        </li>

                        <li class="unread">
                            <img src="../assets/image/user2.jpg" class="profile-notif" alt="default">
                            <div class="notification-content">
                                <p><strong>Duty log approved</strong>
                                View Duty Approved</p><br>
                                <small>Thursday 3:11pm</small>
                            </div>
                            <span class="time-ago">3 hours ago</span>
                            <span class="unread-dot"></span>
                        </li>
                    </ul>
                    <a href="#" class="view-all">View All</a>
                </div>
            </div>
        </div>
        </header>
        <section class="stats">
            <a href="total_students.php" class="stat-card blue">
                <div class="icon-container">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_students; ?></h3>
                    <p>Total Students</p>
                </div>
            </a>
            <a href="pending_duties.php" class="stat-card yellow">
                <div class="icon-container">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $pending_logs; ?></h3>
                    <p>Pending Duty Logs</p>
                </div>
            </a>
            <a href="approved_duties.php" class="stat-card green">
                <div class="icon-container">
                    <i class="fas fa-check-square"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $approved_logs; ?></h3>
                    <p>Approved Duties</p>
                </div>
            </a>
            <a href="rejected_duties.php" class="stat-card red">
                <div class="icon-container">
                    <i class="fa-solid fa-thumbs-down"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $rejected_logs; ?></h3>
                    <p>Rejected Duties</p>
                </div>
            </a>
        </section>
        <!--chart-->
        <h3><i class="fa-solid fa-chart-simple"></i>&nbsp;&nbsp;Scholars in Different Departments</h3>
        <div class="chart-table-container">
            <div class="chart-container">
                <canvas id="scholarChart"></canvas>
            </div>
            <div class="table-box">
                <h3><i class="fa-solid fa-medal"></i>Top Performing Students</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Hours Rendered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Joshua Co</td>
                            <td>CEA</td>
                            <td>90 hrs</td>
                        </tr>
                        <tr>
                            <td>Jonas Enriquez</td>
                            <td>CITE</td>
                            <td>82 hrs</td>
                        </tr>
                        <tr>
                            <td>Bruce Wayne</td>
                            <td>CMA</td>
                            <td>70 hrs</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div class="settings-button" id="settingsButton">
    <i class="fas fa-cog"></i>
</div>

<div class="settings-sidebar" id="settingsSidebar">
    <div class="settings-header">
        <h3>Settings Panel</h3>
        <button id="closeSettings"><i class="fas fa-times"></i></button>
    </div>

    <div class="settings-content">
        <div class="settings-section">
            <label for="inputFormat">Input Text Format</label>
            <select id="inputFormat">
                <option value="default">Default</option>
                <option value="uppercase">Uppercase</option>
                <option value="lowercase">Lowercase</option>
            </select>
        </div>

        <!-- Dark Mode Toggle Button -->
        <div class="settings-section">
            <label class="theme-selection-label">Select Theme</label>
            <div class="theme-selection">
                <div id="lightModeBox" class="theme-box light-mode active" data-theme="light-mode">
                    <i class="fas fa-sun"></i>
                </div>
                <div id="darkModeBox" class="theme-box dark-mode" data-theme="dark-mode">
                    <i class="fas fa-moon"></i>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <label class="color-theme-label">Select Sidebar Theme:</label>
            <div class="color-theme-selection">
                <div class="color-circle active" data-color="#343a40" style="background-color: #343a40;"></div>
                <div class="color-circle" data-color="#1B5379" style="background-color: #1B5379;"></div>
                <div class="color-circle" data-color="#127328" style="background-color: #127328"></div>
                <div class="color-circle" data-color="#008B8B" style="background-color: #008B8B;"></div>
                <div class="color-circle" data-color="#3E1254" style="background-color: #3E1254"></div>
            </div>
        </div>

        <div class="settings-section">
            <label>Select a Background:</label>
            <div id="bgImageContainer">
                <img src="../assets/image/background_image.jpg" class="bg-option" data-image="background1.jpg">
                <img src="../assets/image/background_image.jpg" class="bg-option" data-image="background2.jpg">
                <img src="../assets/image/background_image.jpg" class="bg-option" data-image="background3.jpg">
                <img src="../assets/image/background_image.jpg" class="bg-option" data-image="background4.jpg">
                <img src="../assets/image/background_image.jpg" class="bg-option" data-image="background5.jpg">
                <img src="../assets/image/background_image.jpg" class="bg-option" data-image="background6.jpg">
            </div>
            <button id="removeBgImage">Remove Background</button>
        </div>
    </div>
</div>

<script src="../assets/dashboard.js"></script>
</body>

</html>
