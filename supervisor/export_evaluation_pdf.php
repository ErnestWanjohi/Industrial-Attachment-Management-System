<?php
session_start();
include("../config/db.php");

// 🔐 Supervisor access only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../auth/login.php");
    exit();
}

$supervisor_id = $_SESSION['user_id'];

// =================== Check Student ID ===================
if (!isset($_GET['student_id']) || empty($_GET['student_id'])) {
    $_SESSION['error'] = "No student specified for PDF export.";
    header("Location: review_evaluations.php");
    exit();
}

$student_id = intval($_GET['student_id']);

// =================== Fetch Student + Evaluation ===================
$sql = "SELECT s.name AS student_name, s.reg_no, e.performance_score, e.remarks, e.evaluation_date
        FROM students s
        JOIN attachments a ON s.student_id = a.student_id
        LEFT JOIN evaluations e ON s.student_id = e.student_id AND e.supervisor_id = ?
        WHERE s.student_id = ? AND a.supervisor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $supervisor_id, $student_id, $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Evaluation not found or you are not authorized to export this PDF.";
    header("Location: review_evaluations.php");
    exit();
}

$row = $result->fetch_assoc();

// =================== Generate PDF ===================
// Make sure FPDF library is installed in vendor/fpdf/
require('../fpdf/fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();

// Title
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,"Student Evaluation",0,1,'C');
$pdf->Ln(5);

// Student Info
$pdf->SetFont('Arial','',12);
$pdf->Cell(50,10,"Student Name:",0,0);
$pdf->Cell(0,10, $row['student_name'],0,1);

$pdf->Cell(50,10,"Reg No:",0,0);
$pdf->Cell(0,10, $row['reg_no'],0,1);

$pdf->Cell(50,10,"Performance Score:",0,0);
$pdf->Cell(0,10, $row['performance_score'] ?? 'N/A',0,1);

$pdf->Cell(50,10,"Evaluation Date:",0,0);
$pdf->Cell(0,10, $row['evaluation_date'] ?? 'N/A',0,1);

$pdf->Ln(5);
$pdf->MultiCell(0,10,"Remarks:\n" . ($row['remarks'] ?? 'N/A'));

// Force download
$pdf->Output('D', $row['student_name'] . '_Evaluation.pdf');
exit;