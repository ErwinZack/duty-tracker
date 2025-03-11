<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$stmt = $pdo->query("
SELECT s.id, s.student_id, s.name, s.email, 
       IFNULL(SUM(
           TIMESTAMPDIFF(SECOND, 
               STR_TO_DATE(CONCAT(dl.duty_date, ' ', dl.time_in), '%Y-%m-%d %H:%i:%s'), 
               STR_TO_DATE(CONCAT(dl.duty_date, ' ', dl.time_out), '%Y-%m-%d %H:%i:%s')
           ) / 3600
       ), 0) AS total_hours
FROM students s
LEFT JOIN duty_logs dl 
    ON s.student_id = dl.student_id 
    AND dl.time_out IS NOT NULL
    AND dl.status = 'Approved'
GROUP BY s.id, s.student_id, s.name, s.email;
");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
<div class="dashboard-container">
<?php include '../includes/sidebar.php'?>

<!-- Main Content -->
<main class="main-content">
<header class="header-container">
    <div class="header-left">
        <h2><i class="fas fa-users"></i> Total Students</h2>
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
    <div class="table-content">
        <table id="studentsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Rendered Hours</th>
                    <th>View Logs</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['id']); ?></td>
                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                    <td>
                        <?php echo isset($student['total_hours']) ? number_format($student['total_hours'], 2) : '0.00'; ?>
                    </td>
                    <td>
                        <a href="viewstudent_log.php?student_id=<?php echo htmlspecialchars($student['student_id']); ?>" class="view-logs-btn">
                            
                            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="blue">
                                <path d="M8 2C4 2 1 6 1 6s3 4 7 4 7-4 7-4-3-4-7-4Zm0 6.5A2.5 2.5 0 1 1 8 3a2.5 2.5 0 0 1 0 5.5Z"/>
                            </svg>
                        </a>
                    </td>   
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
</main>
</div>

<script>
function toggleDropdown() {
    const dropdown = document.getElementById('dropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('studentsTable');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const sortSelect = document.getElementById('sortSelect');
    const searchInput = document.getElementById('searchInput');

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdown');
        const sortIcon = document.querySelector('.dropdown img');

        if (event.target !== sortIcon && !dropdown.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });

    sortSelect.addEventListener('change', function() {
        const column = this.value;
        if (!column) return;

        const columnIndex = { id: 0, student_id: 1, name: 2 }[column];

        rows.sort((a, b) => {
            const aValue = a.children[columnIndex].textContent.trim();
            const bValue = b.children[columnIndex].textContent.trim();
            return aValue.localeCompare(bValue, undefined, { numeric: true });
        });

        rows.forEach(row => tbody.appendChild(row));
        document.getElementById('dropdown').style.display = 'none';
    });

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
});
</script>

</body>
</html>
