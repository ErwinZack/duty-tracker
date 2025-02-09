<?php
session_start();
require_once '../config/database.php';
require_once '../config/session.php';

$error = "";
$success = "";

// Only process the form if it is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Make sure the email field is included in the form submission
    if (isset($_POST['email'])) {
        $student_id = trim($_POST['student_id']);
        $name = trim($_POST['name']);
        $scholarship_type = trim($_POST['scholarship_type']);
        $email = trim($_POST['email']);  // Ensure the email is accessed after the form is submitted
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);

        if (!empty($student_id) && !empty($name) && !empty($scholarship_type) && !empty($email) && !empty($password) && !empty($confirm_password)) {
            if ($password === $confirm_password) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Check if student_id or email already exists
                $stmt = $pdo->prepare("SELECT id FROM students WHERE student_id = ? OR email = ?");
                $stmt->execute([$student_id, $email]);

                if ($stmt->rowCount() == 0) {
                    // Insert the student data
                    $stmt = $pdo->prepare("INSERT INTO students (student_id, name, scholarship_type, email, password) VALUES (?, ?, ?, ?, ?)");
                    if ($stmt->execute([$student_id, $name, $scholarship_type, $email, $hashed_password])) {
                        $success = "Registration successful! You can now <a href='login.php'>login</a>.";
                    } else {
                        $error = "Error occurred during registration.";
                    }
                } else {
                    $error = "Student ID or Email already exists.";
                }
            } else {
                $error = "Passwords do not match.";
            }
        } else {
            $error = "Please fill in all fields.";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="../assets/student.css">
</head>

<body>

    <div class="container">
        <div class="form-container">
            <h2>Student Registration</h2>
            <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
            <?php elseif ($success): ?>
            <p class="success"><?php echo $success; ?></p>
            <?php endif; ?>
            <form action="" method="POST">
                <label for="student_id">Student ID:</label>
                <input type="text" name="student_id" required>

                <label for="name">Full Name:</label>
                <input type="text" name="name" required>

                <label for="scholarship_type">Scholarship Type:</label>
                <input type="text" name="scholarship_type" required>

                <label for="email">Email:</label>
                <input type="email" name="email" required> <!-- Ensure the field name is 'email' -->

                <label for="password">Password:</label>
                <input type="password" name="password" required>

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" required>

                <button type="submit">Register</button>
            </form>

            <p class="links">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

</body>

</html>