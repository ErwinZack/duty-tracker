<?php
session_start();
require_once('../config/database.php');
require_once('../config/auth.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$auth = new Auth($pdo);

// Query to fetch duty log data for analytics
$query = "SELECT COUNT(*) as total_logs, SUM(hours) as total_hours, status FROM duty_logs GROUP BY status";
$stmt = $pdo->prepare($query);
$stmt->execute();
$analyticsData = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Analytics</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">
        <h2>Analytics Dashboard</h2>

        <canvas id="analyticsChart"></canvas>

        <script>
        const ctx = document.getElementById('analyticsChart').getContext('2d');
        const analyticsChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Approved', 'Rejected', 'Pending'],
                datasets: [{
                    label: 'Duty Log Status',
                    data: [<?php echo $analyticsData[0]['status'] == 'approved' ? $analyticsData[0]['total_logs'] : 0; ?>,
                        <?php echo $analyticsData[1]['status'] == 'rejected' ? $analyticsData[1]['total_logs'] : 0; ?>,
                        <?php echo $analyticsData[2]['status'] == 'pending' ? $analyticsData[2]['total_logs'] : 0; ?>
                    ],
                    backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
                    borderColor: ['#155724', '#721c24', '#856404'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });
        </script>
    </div>
</body>

</html>