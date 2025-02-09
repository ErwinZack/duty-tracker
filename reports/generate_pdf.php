<?php
session_start();
require_once('../config/database.php');
require_once('../libs/fpdf.php');  // Make sure you have FPDF library installed

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../admin/login.php');
    exit();
}

// Query to fetch duty logs
$query = "SELECT * FROM duty_logs";
$stmt = $pdo->prepare($query);
$stmt->execute();
$dutyLogs = $stmt->fetchAll();

// Create a PDF instance
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Table Header
$pdf->Cell(40, 10, 'ID', 1);
$pdf->Cell(40, 10, 'Student ID', 1);
$pdf->Cell(40, 10, 'Date', 1);
$pdf->Cell(40, 10, 'Hours', 1);
$pdf->Cell(40, 10, 'Status', 1);
$pdf->Ln();

// Loop through duty logs and output them to the PDF
foreach ($dutyLogs as $log) {
    $pdf->Cell(40, 10, $log['id'], 1);
    $pdf->Cell(40, 10, $log['student_id'], 1);
    $pdf->Cell(40, 10, $log['date'], 1);
    $pdf->Cell(40, 10, $log['hours'], 1);
    $pdf->Cell(40, 10, $log['status'], 1);
    $pdf->Ln();
}

// Output the PDF file for download
$pdf->Output('D', 'duty_logs_report.pdf');
exit();