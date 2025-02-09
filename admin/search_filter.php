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

// Search functionality
$searchResults = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $searchTerm = $_POST['search'];
    
    $query = "SELECT * FROM duty_logs WHERE student_name LIKE ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute(["%$searchTerm%"]);
    $searchResults = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search and Filter Duty Logs</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>

<body>
    <div class="container">
        <h2>Search Duty Logs</h2>

        <form method="POST" action="search_filter.php">
            <input type="text" name="search" placeholder="Search by student name" required>
            <button type="submit">Search</button>
        </form>

        <?php if ($searchResults): ?>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Date</th>
                    <th>Hours</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($searchResults as $log): ?>
                <tr>
                    <td><?php echo htmlspecialchars($log['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($log['date']); ?></td>
                    <td><?php echo htmlspecialchars($log['hours']); ?></td>
                    <td><?php echo htmlspecialchars($log['status']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>

</html>