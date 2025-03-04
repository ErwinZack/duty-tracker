<?php
session_start();
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);
    $department = trim($_POST['department']);
    $scholarship_type = trim($_POST['scholarship_type']);
    $hk_duty_status = trim($_POST['hk_duty_status']);
    $year_level = trim($_POST['year_level']);

    if (empty($name) || empty($email) || empty($course) || empty($department) || empty($hk_duty_status)) {
        echo "<p style='color:red;'>All fields are required.</p>";
    } else {
        $stmt = $pdo->prepare("UPDATE students SET name = ?, email = ?, course = ?, department = ?, scholarship_type = ?, hk_duty_status = ?, year_level = ? WHERE id = ?");
        if ($stmt->execute([$name, $email, $course, $department, $scholarship_type, $hk_duty_status, $year_level, $student_id])) {
            echo "<p style='color:green;'>Profile updated successfully!</p>";
        } else {
            echo "<p style='color:red;'>Error updating profile.</p>";
        }
    }
}
?>
