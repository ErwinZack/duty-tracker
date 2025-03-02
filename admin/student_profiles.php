<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch students
$stmt = $pdo->prepare("SELECT * FROM students");
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle delete student
if (isset($_GET['delete'])) {
    $student_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    if ($stmt->execute([$student_id])) {
        header('Location: student_profiles.php');
        exit();
    } else {
        echo "Error deleting student profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profiles</title>
    <link rel="stylesheet" href="../assets/admin.css">
    <script src="../assets/dashboard.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Match with approved_duties.php -->

        <?php include '../admin/includes/sidebar.php'; ?>

        <main class="main-content">
            <!-- Match with approved_duties.php -->
            <header class="header-container">
                <h2><i class="fa fa-users"></i> Student Profiles</h2>
            </header>

            <section class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Student ID</th>
                            <th>Scholarship Type</th>
                            <th>Course</th>
                            <th>Department</th>
                            <th>Year Level</th>
                            <th>Total Hours</th>
                            <th>HK Duty Status</th>
                            <th>Registered Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['scholarship_type']); ?></td>
                            <td><?php echo htmlspecialchars($student['course']); ?></td>
                            <td><?php echo htmlspecialchars($student['department']); ?></td>
                            <td><?php echo htmlspecialchars($student['year_level']); ?></td>
                            <td><?php echo htmlspecialchars($student['hk_duty_status']); ?></td>
                            <td><?php echo htmlspecialchars($student['total_hours']); ?> hrs</td>

                            <td><?php echo date('Y-m-d', strtotime($student['created_at'])); ?></td>
                            <td>
                                <a href="edit_student_profile.php?id=<?php echo $student['id']; ?>" class="btn">Edit</a>
                                <a href="?delete=<?php echo $student['id']; ?>" class="btn"
                                    onclick="return confirm('Are you sure?')">Delete</a>
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
