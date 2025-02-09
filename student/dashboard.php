<?php
session_start();
require_once '../config/database.php';

// Ensure the student_id is available in the session
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

// Get the student data from the database using the student_id stored in the session
$stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->execute([$_SESSION['student_id']]);

// Fetch the student data
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the query returned a valid result
if (!$student) {
    echo "Error: Could not retrieve user data.";
    exit;
}

// Fetch the total hours
$stmt_hours = $pdo->prepare("SELECT SUM(total_hours) AS total_hours FROM duty_logs WHERE student_id = ?");
$stmt_hours->execute([$_SESSION['student_id']]);
$total_hours_data = $stmt_hours->fetch(PDO::FETCH_ASSOC);
$total_hours = $total_hours_data['total_hours'] ?: 0;


// Fetch the pending logs
$stmt_pending = $pdo->prepare("SELECT COUNT(*) AS pending_logs FROM duty_logs WHERE student_id = ? AND status = 'pending'");
$stmt_pending->execute([$_SESSION['student_id']]);
$pending_logs_data = $stmt_pending->fetch(PDO::FETCH_ASSOC);
$pending_logs = $pending_logs_data['pending_logs'] ?: 0;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../assets/student.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css
 ">
</head>

<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <h3>Student Panel</h3>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="duty_logs.php"><i class="fas fa-clock"></i> Duty Logs</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="notifications.php" class="notif-btn"><i class="fas fa-bell"></i> Notifications</a></li>
            <li><a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-content">
        <div class="card">
            <h3>Your Profile</h3>
            <p><strong>Name:</strong> <?php echo $student['name']; ?></p>
            <p><strong>Student ID:</strong> <?php echo $student['student_id']; ?></p>
            <p><strong>Scholarship Type:</strong> <?php echo $student['scholarship_type']; ?></p>
        </div>

        <div class="card">
            <h3>Duty Hours Logged</h3>
            <p><strong>Total Hours:</strong> <?php echo $total_hours; ?></p>
            <p><strong>Last Logged:</strong> <?php echo date("M d, Y"); ?></p>
        </div>

        <div class="card">
            <h3>Pending Approvals</h3>
            <p><strong><?php echo $pending_logs; ?> duty logs</strong> awaiting admin approval.</p>
            <a href="duty_logs.php" class="btn">View Logs</a>
        </div>

        <!-- Add Duty Log Link -->
        <div class="card">
            <h3>Add Duty Log</h3>
            <p>You can log your duty hours here:</p>
            <a href="add_duty.php" class="btn">Add Duty Log</a> <!-- Link to Add Duty Log Page -->
        </div>

        <!-- Duty Hours Progress Bar -->
        <div class="progress-container">
            <span>Duty Hours Completion:</span>
            <div class="progress-bar">
                <div class="progress" style="width: <?php echo min(100, $total_hours * 5); ?>%;"></div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="chart-container">
            <canvas id="dutyChart"></canvas>
        </div>
    </div>

    <script>
    var ctx = document.getElementById('dutyChart').getContext('2d');
    var dutyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            datasets: [{
                label: 'Hours Rendered',
                data: [5, 10, 15, 20, 25],
                backgroundColor: '#3498db'
            }]
        }
    });
    </script>
</body>

</html>