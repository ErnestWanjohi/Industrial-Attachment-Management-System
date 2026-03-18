<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

$sql = "SELECT * FROM progress_reports 
        WHERE student_id = ? 
        ORDER BY week_number DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>My Reports</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    min-height: 100vh;
}
.card { border-radius: 15px; }
</style>
</head>
<body>

<div class="container mt-5">
<a href="dashboard.php" class="btn btn-light mb-3">← Back to Dashboard</a>

<h3 class="text-white mb-4">My Progress Reports</h3>

<?php if ($result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>

<div class="card shadow mb-4 p-3">
<h5>Week <?= $row['week_number']; ?></h5>
<p><strong>Tasks Done:</strong><br><?= nl2br(htmlspecialchars($row['tasks_done'])); ?></p>

<p><strong>Supervisor Comments:</strong><br>
<?= $row['supervisor_comments'] ? nl2br(htmlspecialchars($row['supervisor_comments'])) : "<span class='text-muted'>No comments yet</span>"; ?>
</p>

<small class="text-muted">Submitted on: <?= $row['date_submitted']; ?></small>
</div>

<?php endwhile; ?>

<?php else: ?>



<div class="alert alert-info">No reports submitted yet.</div>

<?php endif; ?>

</div>
</body>
</html>
