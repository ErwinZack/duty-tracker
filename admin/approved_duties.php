<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch approved duty logs
$stmt = $pdo->query("
    SELECT d.id, s.student_id, s.name, s.course, s.department, d.duty_date, d.time_in, d.time_out, d.hours_worked, d.total_hours, d.approved_at
    FROM duty_logs d
    JOIN students s ON d.student_id = s.student_id
    WHERE d.status = 'Approved'
    ORDER BY d.approved_at DESC
");
$approvedDuties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Duty Logs</title>
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
                <h2>Hk Admin</h2>
            </div>
            <hr>
            <ul>
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="approve_duty.php"> <i class="fa fa-check-square-o"></i> Approve Duty Logs</a>
                </li>
                <li><a href="student_profiles.php"><i class="fas fa-user"></i> Student Profiles</a></li>
                <li><a href="add_student.php"><i class="fas fa-user-plus"></i> Add Student</a></li>
                <li><a href="export_report.php"><i class="fas fa-file-export"></i> Export Reports</a></li>
                <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header-container">
                <h2><i class="fa fa-check-square-o"></i> Approved Duty Logs</h2>
            </header>

            <section class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Duty Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours Worked</th>
                            <th>Total Hours</th>
                            <th>Approved At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($approvedDuties as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($log['name']); ?></td>
                            <td><?php echo htmlspecialchars($log['course']); ?></td>
                            <td><?php echo htmlspecialchars($log['duty_date']); ?></td>
                            <td><?php echo htmlspecialchars($log['time_in']); ?></td>
                            <td><?php echo htmlspecialchars($log['time_out']); ?></td>
                            <td><?php echo htmlspecialchars($log['hours_worked']); ?></td>
                            <td><?php echo htmlspecialchars($log['total_hours']); ?></td>
                            <td><?php echo htmlspecialchars($log['approved_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>

</html>