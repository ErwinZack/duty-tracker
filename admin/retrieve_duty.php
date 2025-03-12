<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['duty_id'])) {
    try {
        $dutyId = $_POST['duty_id'];
        
        // Update status to 'Pending'
        $stmt = $pdo->prepare("UPDATE duty_logs SET status = 'Pending' WHERE id = ?");
        $stmt->execute([$dutyId]);
        
        $_SESSION['success'] = "Duty log retrieved successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error retrieving duty log: " . $e->getMessage();
    }
}

header('Location: approve_duty.php');
exit();
?>