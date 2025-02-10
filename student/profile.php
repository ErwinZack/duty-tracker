<?php
session_start();
require_once '../config/database.php';
require_once '../config/session.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = trim($_SESSION['student_id']); // Ensure student_id is trimmed

// Debug: Log student_id to check its value
error_log("Debug: Profile page session student_id = " . $student_id);

// Get student information (Make sure 'student_id' is used instead of 'id' if your table column is 'student_id')
$stmt = $pdo->prepare("SELECT * FROM students WHERE BINARY student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Error: Student ID does not exist in the system. Debug ID: " . htmlspecialchars($student_id));
}

// Get duty logs
$stmt = $pdo->prepare("SELECT * FROM duty_logs WHERE student_id = ?");
$stmt->execute([$student_id]);
$duty_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../assets/student.css">
</head>

<body>
    <div class="profile-container">
        <header>
            <h2>Student Profile</h2>
            <a href="dashboard.php">Back to Dashboard</a>
        </header>

        <section class="personal-info">
            <h3>Personal Information</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name'] ?? 'N/A'); ?></p>
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id'] ?? 'N/A'); ?></p>
            <p><strong>Scholarship Type:</strong> <?php echo htmlspecialchars($student['scholarship_type'] ?? 'N/A'); ?>
            </p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($student['course'] ?? 'N/A'); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($student['department'] ?? 'N/A'); ?></p>
            <p><strong>HK Duty Status:</strong> <?php echo htmlspecialchars($student['hk_duty_status'] ?? 'N/A'); ?></p>
        </section>

        <section class="duty-logs">
            <h3>Your Duty Logs</h3>
            <?php if (!empty($duty_logs)): ?>
            <ul>
                <?php foreach ($duty_logs as $log): ?>
                <li>Time In: <?php echo htmlspecialchars($log['time_in']); ?> |
                    Time Out: <?php echo htmlspecialchars($log['time_out']); ?> |
                    Duration: <?php echo number_format($log['duration'], 2); ?> hours |
                    Status: <?php echo htmlspecialchars($log['status']); ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p>No duty logs available.</p>
            <?php endif; ?>
        </section>
    </div>
</body>

</html>
