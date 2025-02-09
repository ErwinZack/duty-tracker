<?php
session_start();
require_once '../config/database.php';
require_once '../config/session.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Get notifications (duty log statuses)
$stmt = $pdo->prepare("SELECT * FROM duty_logs WHERE student_id = ?");
$stmt->execute([$student_id]);
$notifications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="../assets/student.css">
</head>

<body>
    <div class="notifications-container">
        <header>
            <h2>Duty Log Status Notifications</h2>
            <a href="dashboard.php">Back to Dashboard</a>
        </header>

        <section class="notifications-list">
            <?php if ($notifications): ?>
            <ul>
                <?php foreach ($notifications as $notification): ?>
                <li>
                    <strong>Time In:</strong> <?php echo $notification['time_in']; ?>
                    <strong>Time Out:</strong> <?php echo $notification['time_out']; ?>
                    <strong>Status:</strong> <?php echo $notification['status']; ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p>No duty log notifications.</p>
            <?php endif; ?>
        </section>
    </div>
</body>

</html>