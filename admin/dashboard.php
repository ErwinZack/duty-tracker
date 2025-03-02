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

$stmt = $pdo->prepare("SELECT COUNT(*) AS approved_logs FROM duty_logs WHERE status = 'Approved'");
$stmt->execute();
$approved_logs = $stmt->fetch(PDO::FETCH_ASSOC)['approved_logs'];

$stmt = $pdo->prepare("SELECT COUNT(*) AS rejected_logs FROM duty_logs WHERE status = 'Rejected'");
$stmt->execute();
$rejected_logs = $stmt->fetch(PDO::FETCH_ASSOC)['rejected_logs'];

// Fetch students per department
$stmt = $pdo->prepare("SELECT department, COUNT(*) AS total FROM students GROUP BY department");
$stmt->execute();
$students_per_department = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for Chart.js
$departments = [];
$student_counts = [];

foreach ($students_per_department as $row) {
    $departments[] = $row['department'];
    $student_counts[] = $row['total'];
}

// Fetch top performing students ranked by total duty hours rendered
$stmt = $pdo->prepare("
    SELECT s.student_id, s.name, s.course, s.department, SUM(d.hours_worked) AS total_hours
    FROM duty_logs d
    JOIN students s ON d.student_id = s.student_id
    WHERE d.status = 'Approved'
    GROUP BY d.student_id
    ORDER BY total_hours DESC
");
$stmt->execute();
$top_students = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

        <?php include '../admin/includes/sidebar.php'?>

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


            <!-- Analytics Section -->
            <section class="analytics">
                <h2><i class="fa-solid fa-chart-line"></i> Analytics Dashboard</h2>
                <div class="chart-container">
                    <canvas id="analyticsChart"></canvas>
                </div>
            </section>

            <!-- Top Performing Students Leaderboard -->
            <section class="leaderboard">
                <h2><i class="fa-solid fa-trophy"></i> Top Performing Students</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Department</th>
                            <th>Total Hours Rendered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
            $rank = 1;
            foreach ($top_students as $student): ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['course']); ?></td>
                            <td><?php echo htmlspecialchars($student['department']); ?></td>
                            <td><?php echo htmlspecialchars($student['total_hours']); ?> hrs</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById("analyticsChart").getContext("2d");

        // Get PHP data and parse JSON
        const departments = <?php echo json_encode($departments); ?>;
        const totalStudents = <?php echo json_encode($student_counts); ?>;

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: departments,
                datasets: [{
                    label: "Total Students Per Department",
                    data: totalStudents,
                    backgroundColor: "rgba(54, 162, 235, 0.6)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
    </script>

</body>
