<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../auth/login.php");
    exit();
}

$supervisor_id = $_SESSION['user_id'];
$message = "";

// Get report ID
$report_id = intval($_GET['id'] ?? 0);
if ($report_id === 0) {
    die("Invalid report ID");
}

// Fetch report and verify supervisor
$sql = "SELECT pr.*, s.name AS student_name 
        FROM progress_reports pr
        JOIN attachments a ON pr.student_id = a.student_id
        JOIN students s ON pr.student_id = s.student_id
        WHERE pr.report_id = ? AND a.supervisor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $report_id, $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Report not found or you do not have permission to edit it.");
}

$report = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tasks_done = trim($_POST['tasks_done']);
    if (!empty($tasks_done)) {
        $sql_update = "UPDATE progress_reports SET tasks_done = ? WHERE report_id = ?";
        $stmt_upd = $conn->prepare($sql_update);
        $stmt_upd->bind_param("si", $tasks_done, $report_id);
        if ($stmt_upd->execute()) {
            $message = "<div class='alert alert-success'>Report updated successfully.</div>";
            $report['tasks_done'] = $tasks_done; // Update local variable
        } else {
            $message = "<div class='alert alert-danger'>Error updating report.</div>";
        }
        $stmt_upd->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg,#1e3c72,#2a5298); min-height: 100vh; }
        .card { border-radius: 15px; }
    </style>
</head>
<body>

<div class="container mt-5">
    <a href="review_reports.php" class="btn btn-light mb-3">← Back to Reports</a>

    <h3 class="text-white mb-4">Edit Report - <?= htmlspecialchars($report['student_name']); ?> (Week <?= $report['week_number']; ?>)</h3>

    <?php echo $message; ?>

    <div class="card p-4 shadow">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tasks Done</label>
                <textarea name="tasks_done" class="form-control" rows="5" required><?= htmlspecialchars($report['tasks_done']); ?></textarea>
            </div>

            <button class="btn btn-primary">Update Report</button>
        </form>
    </div>
</div>

</body>
</html>