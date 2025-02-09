<?php
// Start session
session_start();

// Include necessary files for database connection
require_once('../config/database.php');
require_once('../config/auth.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch all duty logs (You can modify this query to add filters or conditions)
$query = "SELECT duty_logs.date, duty_logs.hours, duty_logs.status, students.username 
          FROM duty_logs 
          INNER JOIN students ON duty_logs.student_id = students.student_id";
$stmt = $pdo->prepare($query);
$stmt->execute();

// Set headers to force download of the CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="duty_logs.csv"');

// Open PHP output stream
$output = fopen('php://output', 'w');

// Add column headers to the CSV
fputcsv($output, ['Student Name', 'Date', 'Hours', 'Status']);

// Fetch each duty log and write to CSV
while ($log = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [$log['username'], $log['date'], $log['hours'], $log['status']]);
}

// Close the output stream
fclose($output);
exit();
?>