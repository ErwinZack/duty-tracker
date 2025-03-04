<?php
session_start();
require_once '../config/database.php';
require_once '../config/session.php';

// Ensure the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = trim($_POST['student_id']);
    $name = trim($_POST['name']);
    $scholarship_type = trim($_POST['scholarship_type']);
    $course = trim($_POST['course']);
    $department = trim($_POST['department']);
    $year_level = trim($_POST['year_level']);
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
                // Insert student data
                $stmt = $pdo->prepare("INSERT INTO students (student_id, name, scholarship_type, course, department, year_level, hk_duty_status, email, password) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$student_id, $name, $scholarship_type, $course, $department, $year_level, $hk_duty_status, $email, $hashed_password])) {
                    $success = "Student added successfully!";
                } else {
                    $error = "Error occurred while adding student.";
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>
    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'?>

        <main class="main-content">
            
            <div class="form-container">
                <h2>Student Registration</h2>

                <?php if (!empty($error)): ?>
                <div class="error"><?php echo $error; ?></div>
                <?php elseif (!empty($success)): ?>
                <div class="success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="form-grid">
                        <!-- Personal Information -->
                        <div class="form-group">
                            <label for="student_id"><i class="fas fa-id-card"></i> Student ID:</label>
                            <input type="text" name="student_id" id="student_id" placeholder="Enter student ID" required>
                        </div>

                        <div class="form-group">
                            <label for="name"><i class="fas fa-user"></i> Full Name:</label>
                            <input type="text" name="name" id="name" placeholder="Enter full name" required>
                        </div>

                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                            <input type="email" name="email" id="email" placeholder="Enter email address" required>
                        </div>

                        <div class="form-group">
                            <label for="scholarship_type"><i class="fas fa-award"></i> Scholarship Type:</label>
                            <select name="scholarship_type" id="scholarship_type" required>
                                <option value="" disabled selected>Select Scholarship Type</option>
                                <option value="HK 25">HK 25</option>
                                <option value="HK 50">HK 50</option>
                                <option value="HK 75">HK 75</option>
                            </select>
                        </div>

                        <!-- Academic Information -->
                        <div class="form-group">
                            <label for="course"><i class="fas fa-graduation-cap"></i> Course:</label>
                            <select name="course" id="course" required>
                                <option value="" disabled selected>Select Course</option>
                                <option value="BSIT">BS Information Technology</option>
                                <option value="BSCS">BS Computer Science</option>
                                <option value="BSE">BS Education</option>
                                <option value="BBA">BS Business Administration</option>
                                <option value="BSCRIM">BS Criminology</option>
                                <option value="BSA">BS Accountancy</option>
                                <option value="BSN">BS Nursing</option>
                                <option value="BSARCH">BS Architecture</option>
                                <option value="BSCOE">BS Computer Engineering</option>
                                <option value="BSEE">BS Electrical Engineering</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="department"><i class="fas fa-building"></i> Department:</label>
                            <select name="department" id="department" required>
                                <option value="" disabled selected>Select Department</option>
                                <option value="CEA">CEA</option>
                                <option value="CMA">CMA</option>
                                <option value="CAHS">CAHS</option>
                                <option value="CITE">CITE</option>
                                <option value="CCJE">CCJE</option>
                                <option value="CELA">CELA</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="year_level"><i class="fas fa-layer-group"></i> Year Level:</label>
                            <select name="year_level" id="year_level" required>
                                <option value="" disabled selected>Select Year Level</option>
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                                <option value="5th Year">5th Year</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="hk_duty_status"><i class="fas fa-tasks"></i> HK Duty Status:</label>
                            <select name="hk_duty_status" id="hk_duty_status" required>
                                <option value="" disabled selected>Select Duty Status</option>
                                <option value="Module Distributor">Module Distributor</option>
                                <option value="Student Facilitator">Student Facilitator</option>
                                <option value="Library Assistant">Library Assistant</option>
                                <option value="Admin Assistant">External Facilitator</option>
                            </select>
                        </div>

                        <!-- Password Section -->
                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> Password:
                            </label>
                            <input type="password" name="password" id="password" placeholder="Enter password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password"><i class="fas fa-check-circle"></i> Confirm Password:</label>
                            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" required>
                        </div>
                    </div>

                    <button type="submit"><i class="fas fa-user-plus"></i> Register Student</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get form elements for validation
            const form = document.querySelector('form');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            
            // Simple password matching validation
            form.addEventListener('submit', function(event) {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    event.preventDefault();
                    alert('Passwords do not match!');
                }
            });
        });
    </script>
</body>

</html>