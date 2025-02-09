<?php
session_start();
require_once('../config/database.php');
require_once('../config/auth.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$query = "SELECT * FROM users WHERE role = 'student'";
$stmt = $pdo->prepare($query);
$stmt->execute();
$students = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profiles</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>

<body>
    <div class="container">
        <h2>Student Profiles</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Scholarship</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['id']); ?></td>
                    <td><?php echo htmlspecialchars($student['username']); ?></td>
                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                    <td><?php echo htmlspecialchars($student['course']); ?></td>
                    <td><?php echo htmlspecialchars($student['scholarship']); ?></td>
                    <td>
                        <a href="view_student.php?id=<?php echo $student['id']; ?>">View</a> |
                        <a href="edit_student.php?id=<?php echo $student['id']; ?>">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>