<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id']) || !isset($_POST['id'])){
    exit("Unauthorized request");
}

$student_id = $_POST['id'];
$stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
if ($stmt->execute([$student_id])){
    exit("success");
} else {
    exit("error");
}
?>
