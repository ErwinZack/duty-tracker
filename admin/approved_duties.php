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
           d.hours_worked, d.total_hours, d.approved_at,  d.status
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

        <?php include '../includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-content">
        <header class="header-container">
            <div class="approved-page">
                <div class="header-left">
                    <h2><i class="fas fa-check-square"></i> Approved Duty Logs</h2>
                </div>
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
            <section class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Duty Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours Worked</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($approvedDuties as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($log['name']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($log['duty_date'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($log['time_in'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($log['time_out'])); ?></td>
                            <td><?php echo number_format($log['hours_worked'], 2); ?> hrs</td>
                            <td class="<?php echo ($log['status'] == 'Approved') ? 'status-approved' : ''; ?>">
                                <?php 
                                    if ($log['status'] == 'Approved') {
                                        echo '<i class="fa-solid fa-times-circle"></i> Approved';
                                    } else {
                                        echo htmlspecialchars($log['status']);
                                    }
                                ?>
                            </td>
                            <td>
                                <form method="post" action="retrieve_duty.php" class="retrieve-form">
                                    <input type="hidden" name="duty_id" value="<?php echo $log['id']; ?>">
                                    <button type="submit" class="retrieve-btn">
                                        <i class="fas fa-undo"></i> Retrieve
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>

</html>