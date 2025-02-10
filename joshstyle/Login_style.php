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
    <title>Hk Duty tracker</title>
    <link rel="icon" href="../images/icontitle.png" />
    <link rel="stylesheet" type="text/css" href="../assets/style.css"/>

</head>
<body>
<div class="container">
        <div class="left">
            <img src="/duty-tracker/images/Rectangle bg.png" alt="University Logo">
        </div>
        <div class="right">
            <img src="/duty-tracker/images/phinmaed-logo.png" alt="Sign In Icon" class="signin-icon">
            <h2>Sign In</h2>
            <form action="" method="POST">
                <label for="student_id">Student ID:</label>
                <input type="text" name="student_id" placeholder="Enter Student ID" required>

                <label for="student_password">Password:</label>
                <input type="password" name= "password" placeholder="Enter Password" required>
                
                <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>

                <button class="btn" type="submit">Sign in</button>
            </form>
            <div class="divider">or</div>
            <a href="../joshstyle/Reg_style.php" class="signup-btn">Sign up</a>
            <div class="terms">
                <a href="#">Terms of Use & Privacy Policy</a>
            </div>
        </div>
    </div>
</body>
</html>