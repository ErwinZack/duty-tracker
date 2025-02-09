<?php
session_start();
require_once('../config/database.php');
require_once('../config/auth.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$auth = new Auth($pdo);
$admin = $auth->getUserData($_SESSION['admin_id']);

// Example query to fetch duty logs (add other metrics as needed)
$query = "SELECT * FROM duty_logs";
$stmt = $pdo->prepare($query);
$stmt->execute();
$dutyLogs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">
        <h2>Welcome, Admin <?php echo htmlspecialchars($admin['username']); ?></h2>

        <div class="dashboard">
            <h3>Duty Logs Overview</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Student Name</th>
                        <th>Hours</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dutyLogs as $log): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['date']); ?></td>
                        <td><?php echo htmlspecialchars($log['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($log['hours']); ?></td>
                        <td><?php echo htmlspecialchars($log['status']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3>Duty Hours Progress</h3>
            <canvas id="dutyChart"></canvas>
        </div>

        <script>
        const ctx = document.getElementById('dutyChart').getContext('2d');
        const dutyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Duty Hours',
                    data: [5, 10, 8, 12], // Example data, use real data from PHP
                    backgroundColor: '#6200ee',
                    borderColor: '#3700b3',
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
        </script>
    </div>
</body>

</html>