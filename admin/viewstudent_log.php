<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])){
    header('Location: login.php');
    exit();
}

if (!isset($_GET['student_id'])){
    echo "Student ID not provided.";
    exit();
}

$student_id = $_GET['student_id'];

$stmt = $pdo->prepare("SELECT * FROM duty_logs WHERE student_id = ? ORDER BY duty_date DESC, time_in DESC");
$stmt->execute([$student_id]);
$duty_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_hours = 0; 

foreach ($duty_logs as $log){
    if ($log['time_out']) {
        $time_in = strtotime($log['time_in']);
        $time_out = strtotime($log['time_out']);
        $hours = round(($time_out - $time_in) / 3600, 2); 
        $total_hours += $hours;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Logs</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>
        <main class="main-content">
            <header class="header-container">
                <div class="header-left">
                    <h2>
                        <i class="fa-solid fa-arrow-left" onclick="window.location.href='total_students.php'" style="cursor: pointer;"></i>
                        <i class="fa-regular fa-clock"></i>
                        Student View Logs
                    </h2>
                </div>
                <div class="header-right">
                    <p>Total Hours Worked: <strong><?php echo $total_hours; ?></strong></p>
                </div>
            </header>
            <section class="table-container">
                <table id="studentsTable">
        <thead>
            <tr>
                <th>Duty Date</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Hours Worked</th>
                <th>Status</th>
            </tr>
        </thead>
<tbody>
    <?php if (!empty($duty_logs)): ?>
        <?php foreach ($duty_logs as $log): ?>
            <tr>
                <td><?php echo date('Y-m-d', strtotime($log['duty_date'])); ?></td>
                <td><?php echo date('h:i A', strtotime($log['time_in'])); ?></td>
<td><?php echo ($log['time_out']) ? date('h:i A', strtotime($log['time_out'])) : 'N/A'; ?></td>

                <td>
                    <?php 
                        if ($log['time_out']) {
                            $time_in = strtotime($log['time_in']);
                            $time_out = strtotime($log['time_out']);
                            $hours = round(($time_out - $time_in) / 3600, 2);
                            echo $hours;
                        } else {
                            echo "N/A";
                        }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($log['status']); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">No duty logs found.</td>
        </tr>
    <?php endif; ?>
</tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>