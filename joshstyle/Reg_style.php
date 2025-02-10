<?php
session_start();
require_once '../config/database.php';
require_once '../config/session.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'])) {
        $student_id = trim($_POST['student_id']);
        $name = trim($_POST['name']);
        $scholarship_type = trim($_POST['scholarship_type']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);

        if (!empty($student_id) && !empty($name) && !empty($scholarship_type) && !empty($email) && !empty($password) && !empty($confirm_password)) {
            if ($password === $confirm_password) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $pdo->prepare("SELECT id FROM students WHERE student_id = ? OR email = ?");
                $stmt->execute([$student_id, $email]);

                if ($stmt->rowCount() == 0) {
                    $stmt = $pdo->prepare("INSERT INTO students (student_id, name, scholarship_type, email, password) VALUES (?, ?, ?, ?, ?)");
                    if ($stmt->execute([$student_id, $name, $scholarship_type, $email, $hashed_password])) {
                        $_SESSION['student_id'] = $student_id; // Store student_id for the next step
                        header("Location: hk_reg.php"); // Redirect to the next step
                        exit();
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
    <title>Hk Duty Tracker - Sign Up</title>
    <link rel="stylesheet" href="../assets/reg.css">
</head>
<body>
<div class="container">
  <div class="left">
    <img src="/duty-tracker/images/Rectangle bg.png" alt="University Logo">
  </div>
  <div class="right">
    <h2>Sign In</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
      <label for="student_id">Student ID:</label>
      <input type="text" name="student_id" placeholder="Enter Student ID" required>

      <label for="full_name">Full Name:</label>
      <input type="text" name="full_name" placeholder="Enter Fullname" required>

      <label for="email">Email:</label>
      <input type="email" name="email" placeholder="Enter Email" required>

      <label for="password">Password:</label>
      <input type="password" name="password" placeholder="Enter Password" required>

      <label for="confirm_password">Confirm Password:</label>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      
      <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
      <?php endif; ?>
      <button class="btn" type="submit">Next</button>
    </form>
    <div class="account_login">
        <p>Already have an account? <a href="../joshstyle/Login_style.php">Login here</a></p>
      </div>
    </div>
</div>
</body>
</html>
