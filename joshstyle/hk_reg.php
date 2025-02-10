<?php
session_start();
require_once '../config/database.php';
require_once '../config/session.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: Reg_style.php"); // Redirect if user bypassed step 1
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['scholarship_type'], $_POST['course_type'], $_POST['department'], $_POST['hk_duty_status'])) {
        $scholarship_type = trim($_POST['scholarship_type']);
        $course_type      = trim($_POST['course_type']);
        $department       = trim($_POST['department']);
        $hk_duty_status   = trim($_POST['hk_duty_status']);

        if (!empty($scholarship_type) && !empty($course_type) && !empty($department) && !empty($hk_duty_status)) {
            $stmt = $pdo->prepare("UPDATE students SET scholarship_type = ?, course_type = ?, department = ?, hk_duty_status = ? WHERE student_id = ?");
            if ($stmt->execute([$scholarship_type, $course_type, $department, $hk_duty_status, $_SESSION['student_id']])) {
                $success = "Scholarship information updated successfully.";
                header("Location: dashboard.php"); // Redirect to the dashboard
                exit();
            } else {
                $error = "Error updating scholarship information. Please try again.";
            }
        } else {
            $error = "Please fill in all fields.";
        }
    } else {
        $error = "Invalid form submission.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HK Duty Tracker - Scholarship Information</title>
    <link rel="stylesheet" href="../assets/reg.css">
</head>
<body>
    <div class="container">
        <div class="left">
            <img src="/duty-tracker/images/Rectangle bg.png" alt="University Logo">
        </div>
        <div class="right">
            <h2>Scholarship Information</h2>
            <form action="" method="POST">
                <label for="scholarship_type">Scholarship Type:</label>
                <input type="text" name="scholarship_type" placeholder="Enter Scholarship Type" required>

                <label for="course_type">Course Type:</label>
                <input type="text" name="course_type" placeholder="Enter Course Type" required>

                <label for="department">Department:</label>
                <input type="text" name="department" placeholder="Enter Department" required>
                
                <label for="hk_duty_status">HK Duty Status:</label>
                <input type="text" name="hk_duty_status" placeholder="Enter HK Duty Status" required>

                <?php if (!empty($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="success"><?php echo $success; ?></div>
                <?php endif; ?>

                <button class="btn" type="submit">Submit</button>
            </form>
        </div>
    </div>
</body>
</html> 