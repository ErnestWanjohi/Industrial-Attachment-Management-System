<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $week_number = intval($_POST['week_number']);
    $tasks_done = trim($_POST['tasks_done']);

    if (!empty($week_number) && !empty($tasks_done)) {

        $sql = "INSERT INTO progress_reports 
                (student_id, week_number, tasks_done, date_submitted)
                VALUES (?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $student_id, $week_number, $tasks_done);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Report submitted successfully.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error submitting report.</div>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Submit Progress Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    min-height: 100vh;
}
.card {
    border-radius: 15px;
}
</style>
</head>
<body>

<div class="container mt-5">
<a href="dashboard.php" class="btn btn-light mb-3">← Back to Dashboard</a>

<h3 class="text-white mb-4">Submit Weekly Report</h3>

<?php echo $message; ?>

<div class="card p-4 shadow">
<form method="POST">

<div class="mb-3">
<label class="form-label">Week Number</label>
<input type="number" name="week_number" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Tasks Done</label>
<textarea name="tasks_done" class="form-control" rows="5" required></textarea>
</div>

<button type="submit" class="btn btn-primary">Submit Report</button>

</form>
</div>
</div>

</body>
</html>