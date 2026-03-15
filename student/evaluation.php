<?php
session_start();
include("../config/db.php");

$evaluation = null;

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student') {
    $student_id = $_SESSION['user_id'];

    $sql = "SELECT e.performance_score, e.remarks, e.evaluation_date, s.name AS supervisor_name
            FROM evaluations e
            JOIN supervisors s ON e.supervisor_id = s.supervisor_id
            WHERE e.student_id = ?
            ORDER BY e.evaluation_date DESC
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $evaluation = $result->fetch_assoc();
    }
} else {
    echo "<div class='alert alert-danger text-center mt-5'>Student not logged in.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Evaluation</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    min-height: 100vh;
    margin: 0;
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364, #1e3c72);
    background-size: 400% 400%;
    animation: gradientMove 15s ease infinite;
}

@keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.top-bar {
    background: rgba(0,0,0,0.6);
    padding: 15px 0;
}

.top-bar h2 {
    color: white;
    margin: 0;
}

.custom-card {
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.95);
    transition: 0.3s ease;
}

.custom-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.btn-rounded {
    border-radius: 25px;
}
</style>
</head>

<body>

<!-- TOP HEADER -->
<div class="top-bar">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="dashboard.php" class="btn btn-light btn-sm btn-rounded">
            ← Back to Dashboard
        </a>
        <h2>My Evaluation</h2>
        <div></div>
    </div>
</div>

<div class="container mt-5">

<?php if ($evaluation): ?>

    <div class="card shadow-lg custom-card mx-auto p-4" style="max-width:700px;">

        <h5 class="mb-3 text-primary">
            Supervisor: <?= htmlspecialchars($evaluation['supervisor_name']) ?>
        </h5>

        <p><strong>Performance Score:</strong>
            <?= htmlspecialchars($evaluation['performance_score']) ?>
        </p>

        <p><strong>Remarks:</strong><br>
            <?= nl2br(htmlspecialchars($evaluation['remarks'])) ?>
        </p>

        <p class="text-muted">
            <small>Evaluated on:
                <?= htmlspecialchars($evaluation['evaluation_date']) ?>
            </small>
        </p>

        <div class="text-end mt-4">
            <a href="download_evaluation.php"
               class="btn btn-primary btn-rounded">
               Download PDF
            </a>
        </div>

    </div>

<?php else: ?>

    <div class="alert alert-info text-center shadow mx-auto" style="max-width:500px;">
        No evaluation has been submitted yet.
    </div>

<?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>