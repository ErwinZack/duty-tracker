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

// Check if a duty log is being approved or rejected
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $duty_id = $_POST['duty_id'];
    $status = $_POST['status']; // 'approved' or 'rejected'

    // Update the duty log status
    $query = "UPDATE duty_logs SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$status, $duty_id]);

    header('Location: dashboard.php'); // Redirect to the dashboard after update
    exit();
}

// Fetch pending duty logs for approval
$query = "SELECT * FROM duty_logs WHERE status = 'pending'";
$stmt = $pdo->prepare($query);
$stmt->execute();
$pendingLogs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Duty</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>

<body>
    <div class="container">
        <h2>Approve Duty Logs</h2>

        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Date</th>
                    <th>Hours</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingLogs as $log): ?>
                <tr>
                    <td><?php echo htmlspecialchars($log['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($log['date']); ?></td>
                    <td><?php echo htmlspecialchars($log['hours']); ?></td>
                    <td><?php echo htmlspecialchars($log['status']); ?></td>
                    <td>
                        <form action="approve_duty.php" method="POST">
                            <input type="hidden" name="duty_id" value="<?php echo $log['id']; ?>">
                            <button type="submit" name="status" value="approved">Approve</button>
                            <button type="submit" name="status" value="rejected">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>