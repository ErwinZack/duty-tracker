<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$stmt = $pdo->query("SELECT id, student_id, name, email FROM students");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/table.css">
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
                                <th class="sortable" data-column="id">ID</th>
                                <th class="sortable" data-column="student_id">Student ID</th>
                                <th class="sortable" data-column="name">Name </th>
                                <th class="sortable" data-column="email">Email </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td data-label="ID"><?php echo htmlspecialchars($student['id']); ?></td>
                                <td data-label="Student ID"><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td data-label="Name"><?php echo htmlspecialchars($student['name']); ?></td>
                                <td data-label="Email"><?php echo htmlspecialchars($student['email']); ?></td>
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
    if (dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
    } else {
        dropdown.style.display = 'block';
    }
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