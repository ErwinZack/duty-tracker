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
    <script src="../assets/dashboard.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="logo-container">
                <img src="../assets/image/University_of_Pangasinan_logo.png" alt="Logo">
                <!-- Ensure logo path is correct -->
                <h2>Hk Admin</h2>
            </div>
            <hr> <!-- Horizontal line below Admin Panel title -->
            <ul>
                <li><a href="dashboard.php" class="active"><i class="fa fa-dashboard"></i></i> Dashboard</a></li>
                <li><a href="approve_duty.php"><i class="fa fa-check-square-o"></i> Approve
                        Duty Logs</a></li>
                <li><a href="student_profiles.php"><i class="fas fa-user"></i> Student Profiles</a></li>
                <li><a href="add_student.php"><i class="fas fa-user-plus"></i> Add Student</a></li>
                <li><a href="export_report.php"><i class="fas fa-file-export"></i> Export Reports</a></li>
                <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>


        <!-- Main Content -->
        <!-- Main Content -->
        <main class="main-content">
            <!-- Header with Search Filter -->
            <header class="header-container">
                <h2><i class="fa-solid fa-house"></i> Welcome to Admin Dashboard</h2>
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search...">
                </div>
            </header>


            <!-- Stats Section -->
            <section class="stats">

                <a href="total_students.php" class="stat-card blue">
                    <i class="fas fa-users"></i>
                    <div>
                        <h3><?php echo $total_students; ?></h3>
                        <p>Total Students</p>
                    </div>
                </a>

                <a href="pending_duties.php" class="stat-card yellow">
                    <i class="fa-solid fa-hourglass-half"></i>
                    <div>
                        <h3><?php echo $pending_logs; ?></h3>
                        <p>Pending Duty Logs</p>
                    </div>
                </a>

                <a href="approved_duties.php" class="stat-card green">
                    <i class="fa fa-check-square-o"></i>
                    <div>
                        <h3><?php echo $approved_logs; ?></h3>
                        <p>Approved Duties</p>
                    </div>
                </a>

                <a href="rejected_duties.php" class="stat-card red">
                    <i class="fa-solid fa-thumbs-down"></i>
                    <div>
                        <h3><?php echo $rejected_logs; ?></h3>
                        <p>Rejected Duties</p>
                    </div>
                </a>
            </section>


        </main>
    </div>
    <!-- Settings Button (Floating on Right Side) -->
    <div class="settings-button" id="settingsButton">
        <i class="fas fa-cog"></i>
    </div>

    <!-- Settings Sidebar (Hidden by Default) -->
    <div class="settings-sidebar" id="settingsSidebar">
        <div class="settings-header">
            <h3>Settings Panel</h3>
            <button id="closeSettings"><i class="fas fa-times"></i></button>
        </div>

        <div class="settings-content">
            <label for="inputFormat">Input Text Format</label>
            <select id="inputFormat">
                <option value="default">Default</option>
                <option value="uppercase">Uppercase</option>
                <option value="lowercase">Lowercase</option>
            </select>

            <!-- Dark Mode Toggle Button -->
            <div class="theme-selection-container">
                <label class="theme-selection-label">Select Theme</label>
                <div class="theme-selection">
                    <div id="lightModeBox" class="theme-box light-mode" data-theme="light-mode">
                        <i class="fas fa-sun"></i>
                    </div>
                    <div id="darkModeBox" class="theme-box dark-mode" data-theme="dark-mode">
                        <i class="fas fa-moon"></i>
                    </div>
                </div>
            </div>





            <div class="color-theme-container">
                <label class="color-theme-label">Select Sidebar Theme:</label>
                <div class="color-theme-selection">
                    <div class="color-circle" data-color="#343a40" style="background-color: #343a40;"></div>
                    <div class="color-circle" data-color="#1B5379" style="background-color: #1B5379;"></div>
                    <div class="color-circle" data-color="#127328" style="background-color: #127328"></div>
                    <div class="color-circle" data-color="#008B8B" style="background-color: #008B8B;"></div>
                    <div class="color-circle" data-color="#3E1254" style="background-color: #3E1254"></div>
                </div>
            </div>


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

</body>
