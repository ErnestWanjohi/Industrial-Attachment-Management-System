<?php
session_start();
include("../config/db.php");

// 🔴 VERY IMPORTANT: No output before this line
require('../fpdf/fpdf.php'); // make sure path is correct

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$report_id = intval($_GET['id']);

// Fetch report
$sql = "SELECT pr.*, s.name 
        FROM progress_reports pr
        JOIN students s ON pr.student_id = s.student_id
        WHERE pr.report_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Report not found");
}

$row = $result->fetch_assoc();

// ================= PDF =================
$pdf = new FPDF();
$pdf->AddPage();

// Title
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Student Progress Report', 0, 1, 'C');

$pdf->Ln(5);

// Student Info
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Student: ' . $row['name'], 0, 1);
$pdf->Cell(0, 10, 'Week: ' . $row['week_number'], 0, 1);

$pdf->Ln(5);

// Tasks
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Tasks Done:', 0, 1);

$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 8, $row['tasks_done']);

$pdf->Ln(5);

// Comment
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Supervisor Comment:', 0, 1);

$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 8, $row['supervisor_comments']);

// Output PDF (FORCE DOWNLOAD)
$pdf->Output('D', 'report_' . $report_id . '.pdf');
exit();