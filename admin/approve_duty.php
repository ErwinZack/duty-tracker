<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$error = "";
$success = "";

// Fetch pending duty logs
$stmt = $pdo->prepare("
    SELECT dl.id, dl.student_id, dl.duty_date, dl.time_in, dl.time_out, dl.hours_worked, dl.total_hours, dl.status, 
           s.name AS student_name
    FROM duty_logs dl
    INNER JOIN students s ON dl.student_id = s.student_id
    WHERE dl.status = 'Pending'
    ORDER BY dl.duty_date DESC, dl.time_in ASC
");
$stmt->execute();
$pending_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to calculate hours worked
function calculateHoursWorked($timeIn, $timeOut) {
    if (!$timeIn || !$timeOut) return 0;
    $time1 = new DateTime($timeIn);
    $time2 = new DateTime($timeOut);
    $interval = $time1->diff($time2);
    return round($interval->h + ($interval->i / 60), 2);
}

// Handle approval/rejection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['log_id'], $_POST['action'])) {
    $log_id = $_POST['log_id'];
    $action = $_POST['action'];
    $admin_id = $_SESSION['admin_id'];

    $pdo->beginTransaction(); // Start transaction

    try {
        if ($action == 'Approved') {
            // Fetch duty log details
            $stmt_log = $pdo->prepare("SELECT time_in, time_out, student_id FROM duty_logs WHERE id = ?");
            $stmt_log->execute([$log_id]);
            $log_data = $stmt_log->fetch(PDO::FETCH_ASSOC);

            if ($log_data) {
                // Calculate hours worked
                $hours_worked = calculateHoursWorked($log_data['time_in'], $log_data['time_out']);

                // Update duty log with approved status and calculated hours worked
                $stmt_update = $pdo->prepare("
                    UPDATE duty_logs 
                    SET status = 'Approved', hours_worked = ?, admin_id = ?, approved_at = NOW()
                    WHERE id = ?
                ");
                $stmt_update->execute([$hours_worked, $admin_id, $log_id]);

                // Get latest total hours of student from duty_logs (only approved logs)
                $stmt_total_hours = $pdo->prepare("
                    SELECT IFNULL(SUM(hours_worked), 0) 
                    FROM duty_logs 
                    WHERE student_id = ? AND status = 'Approved'
                ");
                $stmt_total_hours->execute([$log_data['student_id']]);
                $total_hours_rendered = $stmt_total_hours->fetchColumn();

                // Update total hours in students table
                $stmt_student_update = $pdo->prepare("
                    UPDATE students 
                    SET total_hours = ? 
                    WHERE student_id = ?
                ");
                $stmt_student_update->execute([$total_hours_rendered, $log_data['student_id']]);

                // Update total hours in duty_logs for consistency
                $stmt_log_update = $pdo->prepare("
                    UPDATE duty_logs 
                    SET total_hours = ? 
                    WHERE student_id = ?
                ");
                $stmt_log_update->execute([$total_hours_rendered, $log_data['student_id']]);
            }
        } else {
            // If rejected, only update status
            $stmt_reject = $pdo->prepare("UPDATE duty_logs SET status = 'Rejected' WHERE id = ?");
            $stmt_reject->execute([$log_id]);
        }

        $pdo->commit(); // Commit transaction
        $success = "Duty log successfully updated.";
        header("Location: approve_duty.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack(); // Rollback if error occurs
        $error = "Error updating duty log: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Duty Logs</title>
    <link rel="stylesheet" href="">
</head>

<body>
    <div class="approve-log-container">
        <h2>Approve or Reject Duty Logs</h2>

        <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p>
        <?php elseif (!empty($success)): ?>
        <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Duty Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hours Worked</th>
                    <th>Total Hours Rendered</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pending_logs)): ?>
                <tr>
                    <td colspan="10">No pending duty logs.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($pending_logs as $log): ?>
                <tr>
                    <td><?php echo $log['id']; ?></td>
                    <td><?php echo htmlspecialchars($log['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($log['student_id']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($log['duty_date'])); ?></td>
                    <td><?php echo date('h:i A', strtotime($log['time_in'])); ?></td>
                    <td><?php echo $log['time_out'] ? date('h:i A', strtotime($log['time_out'])) : 'N/A'; ?></td>

                    <td><?php echo number_format($log['hours_worked'], 2); ?> hrs</td>
                    <td><?php echo number_format($log['total_hours'], 2); ?> hrs</td>
                    <td><?php echo htmlspecialchars($log['status']); ?></td>
                    <td>
                        <form action="" method="POST">
                            <input type="hidden" name="log_id" value="<?php echo $log['id']; ?>">
                            <select name="action">
                                <option value="Approved">Approve</option>
                                <option value="Rejected">Reject</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>