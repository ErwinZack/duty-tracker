<?php
session_start();
require_once('../config/database.php');
require_once('../config/auth.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$auth = new Auth($pdo);

// Ensure that the duty log ID and status are provided
if (isset($_POST['duty_id'], $_POST['status'])) {
    $duty_id = $_POST['duty_id'];
    $status = $_POST['status']; // 'approved' or 'rejected'

    // Validate status value
    if ($status !== 'approved' && $status !== 'rejected') {
        die("Invalid status value.");
    }

    // Update the duty log status in the database
    $query = "UPDATE duty_logs SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$status, $duty_id]);

    // After updating, redirect to the dashboard with a success message
    $_SESSION['message'] = "Duty log has been $status successfully.";
    header('Location: approve_duty.php');
    exit();
} else {
    // Redirect to the approval page if no duty ID or status is provided
    $_SESSION['error'] = "Failed to process the approval request.";
    header('Location: approve_duty.php');
    exit();
}