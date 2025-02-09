<?php
session_start();
require_once('../config/database.php');
require_once('../config/auth.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['format'])) {
    $format = $_GET['format'];
    
    // Query to get duty logs
    $query = "SELECT * FROM duty_logs";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $dutyLogs = $stmt->fetchAll();

    if ($format == 'csv') {
        // Export to CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=duty_logs.csv');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Student ID', 'Date', 'Hours', 'Status']);
        foreach ($dutyLogs as $log) {
            fputcsv($output, $log);
        }
        fclose($output);
    } elseif ($format == 'pdf') {
        // Export to PDF (using TCPDF library)
        require_once('../libs/tcpdf/tcpdf.php');
        
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        $pdf->Cell(40, 10, 'ID', 1);
        $pdf->Cell(40, 10, 'Student ID', 1);
        $pdf->Cell(40, 10, 'Date', 1);
        $pdf->Cell(40, 10, 'Hours', 1);
        $pdf->Cell(40, 10, 'Status', 1);
        $pdf->Ln();

        foreach ($dutyLogs as $log) {
            $pdf->Cell(40, 10, $log['id'], 1);
            $pdf->Cell(40, 10, $log['student_id'], 1);
            $pdf->Cell(40, 10, $log['date'], 1);
            $pdf->Cell(40, 10, $log['hours'], 1);
            $pdf->Cell(40, 10, $log['status'], 1);
            $pdf->Ln();
        }

        $pdf->Output('duty_logs.pdf', 'I');
    }
}