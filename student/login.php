<?php
session_start();
require_once '../config/database.php';
require_once '../config/session.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = trim($_POST['student_id']);
    $password = trim($_POST['password']);

    if (!empty($student_id) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch();

        if ($student && password_verify($password, $student['password'])) {
            $_SESSION['student_id'] = trim($student['student_id']); // Store correctly formatted student ID
            $_SESSION['name'] = $student['name'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid Student ID or Password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="../assets/student.css">
</head>

<body>

    <div class="container">
        <div class="form-container">
            <h2>Student Login</h2>
            <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="" method="POST">
                <label for="student_id">Student ID:</label>
                <input type="text" name="student_id" required>

                <label for="password">Password:</label>
                <input type="password" name="password" required>

                <button type="submit">Login</button>
            </form>
            <p class="links">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>

</body>

</html>