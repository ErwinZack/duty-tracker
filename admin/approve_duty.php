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

    // Check if there are updated values from the modal
    $updated_date = isset($_POST['editdate']) ? $_POST['editdate'] : null;
    $updated_time_in = isset($_POST['timein']) ? $_POST['timein'] : null;
    $updated_time_out = isset($_POST['timeout']) ? $_POST['timeout'] : null;

    $pdo->beginTransaction(); // Start transaction

    try {
        if ($action == 'Approved') {
            // Fetch duty log details
            $stmt_log = $pdo->prepare("SELECT time_in, time_out, student_id, duty_date FROM duty_logs WHERE id = ?");
            $stmt_log->execute([$log_id]);
            $log_data = $stmt_log->fetch(PDO::FETCH_ASSOC);

            if ($log_data) {
                // Use updated values if provided, otherwise use existing values
                $duty_date = $updated_date ?: $log_data['duty_date'];
                $time_in = $updated_time_in ?: $log_data['time_in'];
                $time_out = $updated_time_out ?: $log_data['time_out'];
                
                // Calculate hours worked
                $hours_worked = calculateHoursWorked($time_in, $time_out);

                // Update duty log with approved status and calculated hours worked
                $stmt_update = $pdo->prepare("
                    UPDATE duty_logs 
                    SET status = 'Approved', 
                        hours_worked = ?, 
                        admin_id = ?, 
                        approved_at = NOW(),
                        duty_date = ?,
                        time_in = ?,
                        time_out = ?
                    WHERE id = ?
                ");
                $stmt_update->execute([$hours_worked, $admin_id, $duty_date, $time_in, $time_out,  $log_id]);

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
            $stmt_reject = $pdo->prepare("UPDATE duty_logs SET status = 'Rejected' WHERE id = ?");
            $stmt_reject->execute([$log_id]);
        }

        $pdo->commit(); // Commit transaction
        $success = "Duty log successfully updated.";
        header("Location: approve_duty.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
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
    <link rel="icon" href="../assets/image/icontitle.png" />
    <link rel="stylesheet" href="../assets/admin.css">

</head>
    
<body>
    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'?>

        <main class="main-content">
        <header class="header-container">
            <div class="header-left">
                <h2><i class="fas fa-users"></i> Approve Duty Logs</h2>
            </div>
            
            <div class="header-right">
                <div class="search-sort-container">
                    <div class="search-container">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search...">
                    </div>
                    
                    <div class="dropdown">
                        <img src="../assets/image/sort-icon.jpg" alt="Sort" onclick="toggleDropdown()">
                        <div class="dropdown-content" id="dropdown">
                            <select id="sortSelect">
                                <option value="id">ID</option>
                                <option value="student_id">Student ID</option>
                                <option value="name">Name</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p>
        <?php elseif (!empty($success)): ?>
        <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <section class="table-container">
        <div class="table-content">
        <table id="studentsTable">
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
        <td class="<?php echo $log['status'] == 'Pending' ? 'status-pending' : ''; ?>">
            <?php echo htmlspecialchars($log['status']); ?>
        </td>
        <td>
            <button type="button" onclick="openModal(<?php 
                echo htmlspecialchars(json_encode([
                    'id' => $log['id'],
                    'date' => date('Y-m-d', strtotime($log['duty_date'])),
                    'timeIn' => date('H:i', strtotime($log['time_in'])),
                    'timeOut' => $log['time_out'] ? date('H:i', strtotime($log['time_out'])) : '',
                    'student' => $log['student_name'],
                    'student_id' => $log['student_id']
                ])); 
            ?>)">
                <img src="../assets/image/threedots.svg" alt="actionbutton" class="three-dots">
            </button>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
</tbody>
        </table>
        </div>
        </section>
    </main>
</div>

<!-- Modal from hk.php -->
<div class="form-modal" id="form_popup">
    <div class="form-content">
        <h3>Review Time Log</h3>
        <form id="timeForm" method="POST" action="">
            <input type="hidden" id="log_id" name="log_id">
            
            <label for="edit_date">Date</label>
            <input type="date" id="edit_date" name="editdate" required>
            
            <label for="edit_timein">Time in</label>
            <input type="time" id="edit_timein" name="timein" required>
            
            <label for="edit_timeout">Time out</label>
            <input type="time" id="edit_timeout" name="timeout" required>

            <div class="buttons">
                <button type="button" onclick="closeModal()">Cancel</button>
                <button type="submit" name="action" value="Rejected" class="reject-button">Reject</button>
                <button type="submit" name="action" value="Approved" class="approve-button">Approve</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Function to open modal and pre-fill fields
    function openModal(logData) {
        document.getElementById('form_popup').style.display = 'flex';
        document.getElementById('log_id').value = logData.id;
        document.getElementById('edit_date').value = logData.date;
        document.getElementById('edit_timein').value = logData.timeIn;
        document.getElementById('edit_timeout').value = logData.timeOut;
        document.getElementById('student_info').textContent = 'Student: ' + logData.student + ' (' + logData.student_id + ')';
    }

    // Function to close modal
    function closeModal() {
        document.getElementById('form_popup').style.display = 'none';
    }

    // Close modal if user clicks outside of it
    window.onclick = function(event) {
        var modal = document.getElementById('form_popup');
        if (event.target == modal) {
            closeModal();
        }
    }

    // Function to toggle dropdown
    function toggleDropdown() {
        document.getElementById('dropdown').classList.toggle('show');
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const table = document.getElementById('studentsTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            let found = false;
            const cells = rows[i].getElementsByTagName('td');
            
            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.indexOf(searchValue) > -1) {
                    found = true;
                    break;
                }
            }
            
            rows[i].style.display = found ? '' : 'none';
        }
    });
</script>
</body>
</html>