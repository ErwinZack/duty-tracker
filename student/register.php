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
        $year_level = trim($_POST['year_level']); // Added Year Level
        $hk_duty_status = trim($_POST['hk_duty_status']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);

        if (!empty($student_id) && !empty($name) && !empty($scholarship_type) && !empty($course) &&
            !empty($department) && !empty($year_level) && !empty($hk_duty_status) && !empty($email) &&
            !empty($password) && !empty($confirm_password)) {

            if ($password === $confirm_password) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Check if student_id or email already exists
                $stmt = $pdo->prepare("SELECT id FROM students WHERE student_id = ? OR email = ?");
                $stmt->execute([$student_id, $email]);

                if ($stmt->rowCount() == 0) {
                    // Insert the student data
                    $stmt = $pdo->prepare("INSERT INTO students (student_id, name, scholarship_type, course, department, year_level, hk_duty_status, email, password) 
                                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute([$student_id, $name, $scholarship_type, $course, $department, $year_level, $hk_duty_status, $email, $hashed_password])) {
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
                <select name="scholarship_type" required>
                    <option value="HK 25">HK 25</option>
                    <option value="HK 50">HK 50</option>
                    <option value="HK 75">HK 75</option>
                </select>

                <label for="course">Course:</label>
                <select name="course" required>
                    <option value="" disabled selected>Select Course</option>
                    <option value="BSIT">BS Information Technology</option>
                    <option value="BSCS">BS Computer Science</option>
                    <option value="BSE">BS Education</option>
                    <option value="BBA">BS Business Administration</option>
                    <option value="BSCRIM">BS Criminology</option>
                    <option value="BSA">BS Accountancy</option>
                    <option value="BSE">BS Education</option>
                    <option value="BSN">BS Nursing</option>
                    <option value="BSARCH">BS Architecture</option>
                    <option value="BSCOE">BS Computer Engineering</option>
                    <option value="BSEE">BS Electrical Engineering</option>
                    <option value="BSME">BS Mechanical Engineering</option>
                    <option value="BSCE">BS Civil Engineering</option>
                    <option value="BSP">BS Psychology</option>
                </select>

                <label for="department">Department:</label>
                <select name="department" required>
                    <option value="" disabled selected>Select Department</option>
                    <option value="CEA">CEA</option>
                    <option value="CMA">CMA</option>
                    <option value="CAHS">CAHS</option>
                    <option value="CITE">CITE</option>
                    <option value="CCJE">CCJE</option>
                    <option value="CELA">CELA</option>
                </select>

                <label for="year_level">Year Level:</label>
                <select name="year_level" required>
                    <option value="" disabled selected>Select Year Level</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                    <option value="4th Year">5th Year</option>
                </select>

                <label for="hk_duty_status">HK Duty Status:</label>
                <select name="hk_duty_status" required>
                    <option value="" disabled selected>Select Duty Status</option>
                    <option value="Module Distributor">Module Distributor</option>
                    <option value="Student Facilitator">Student Facilitator</option>
                    <option value="Library Assistant">Library Assistant</option>
                    <option value="Admin Assistant">External Facilitator</option>
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
