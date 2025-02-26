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
</head>

<body>
    <div class="student-profiles-container">
        <h2>Student Profiles</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Student ID</th>
                    <th>Scholarship Type</th>
                    <th>Course</th>
                    <th>Department</th>
                    <th>Year Level</th>
                    <th>HK Duty Status</th>
                    <th>Registered Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo $student['id']; ?></td>
                    <td><?php echo $student['name']; ?></td>
                    <td><?php echo $student['student_id']; ?></td>
                    <td><?php echo $student['scholarship_type']; ?></td>
                    <td><?php echo $student['course']; ?></td>
                    <td><?php echo $student['department']; ?></td>
                    <td><?php echo $student['year_level']; ?></td>
                    <td><?php echo $student['hk_duty_status']; ?></td>
                    <td><?php echo $student['created_at']; ?></td>
                    <td>
                        <a href="edit_student_profile.php?id=<?php echo $student['id']; ?>" class="btn">Edit</a>
                        <a href="?delete=<?php echo $student['id']; ?>" class="btn"
                            onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
