<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch approved duty logs
$stmt = $pdo->query("
    SELECT d.id, s.student_id, s.name, s.course, s.department, 
           d.duty_date, d.time_in, d.time_out, 
           d.hours_worked, d.total_hours, d.approved_at
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

        <?php include '../admin/includes/sidebar.php'; ?>

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
                            <th>Department</th>
                            <th>Duty Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours Worked</th>
                            <th>Total Hours</th>
                            <!-- <th>Approved At</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($approvedDuties as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($log['name']); ?></td>
                            <td><?php echo htmlspecialchars($log['course']); ?></td>
                            <td><?php echo htmlspecialchars($log['department']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($log['duty_date'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($log['time_in'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($log['time_out'])); ?></td>
                            <td><?php echo number_format($log['hours_worked'], 2); ?> hrs</td>
                            <td><?php echo number_format($log['total_hours'], 2); ?> hrs</td>
                            <!-- <td><?php echo date('Y-m-d h:i A', strtotime($log['approved_at'])); ?></td> -->
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>

</html>