<?php
session_start();
require_once '../config/database.php';
require_once '../config/session.php';

$error = "";
$success = "";

// Only process the form if it is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'])) {
        $student_id = trim($_POST['student_id']);
        $name = trim($_POST['name']);
        $scholarship_type = trim($_POST['scholarship_type']);
        $course = trim($_POST['course']);
        $department = trim($_POST['department']);
        $hk_duty_status = trim($_POST['hk_duty_status']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);

        if (!empty($student_id) && !empty($name) && !empty($scholarship_type) && !empty($course) &&
            !empty($department) && !empty($hk_duty_status) && !empty($email) &&
            !empty($password) && !empty($confirm_password)) {

            if ($password === $confirm_password) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Check if student_id or email already exists
                $stmt = $pdo->prepare("SELECT id FROM students WHERE student_id = ? OR email = ?");
                $stmt->execute([$student_id, $email]);

                if ($stmt->rowCount() == 0) {
                    // Insert the student data
                    $stmt = $pdo->prepare("INSERT INTO students (student_id, name, scholarship_type, course, department, hk_duty_status, email, password) 
                                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute([$student_id, $name, $scholarship_type, $course, $department, $hk_duty_status, $email, $hashed_password])) {
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

                <label for="course">Course:</label>
                <select name="course" required>
                    <option value="" disabled selected>Select Course</option>
                    <option value="BSIT">BS Information Technology</option>
                    <option value="BSCS">BS Computer Science</option>
                    <option value="BSE">BS Education</option>
                    <option value="BBA">BS Business Administration</option>
                </select>

                <label for="department">Department:</label>
                <select name="department" required>
                    <option value="" disabled selected>Select Department</option>
                    <option value="IT">Information Technology</option>
                    <option value="CS">Computer Science</option>
                    <option value="Education">Education</option>
                    <option value="Business">Business</option>
                </select>

                <label for="hk_duty_status">HK Duty Status:</label>
                <select name="hk_duty_status" required>
                    <option value="" disabled selected>Select Duty Status</option>
                    <option value="Module Distributor">Module Distributor</option>
                    <option value="Student Facilitator">Student Facilitator</option>
                    <option value="Library Assistant">Library Assistant</option>
                    <option value="Admin Assistant">Admin Assistant</option>
                </select>

                <label for="email">Email:</label>
                <input type="email" name="email" required>

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
