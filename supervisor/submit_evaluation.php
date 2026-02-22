<?php
session_start();
include("../config/db.php");

// 🔐 Supervisor access only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../auth/login.php");
    exit();
}

$supervisor_id = $_SESSION['user_id'];
$message = "";

// =================== Handle Evaluation Submission / Update ===================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = intval($_POST['student_id']);
    $performance_score = intval($_POST['performance_score']);
    $remarks = trim($_POST['remarks']);

    // Check if evaluation exists
    $stmt_check = $conn->prepare("SELECT evaluation_id FROM evaluations WHERE student_id=? AND supervisor_id=?");
    $stmt_check->bind_param("ii", $student_id, $supervisor_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Update existing evaluation
        $stmt_upd = $conn->prepare("UPDATE evaluations SET performance_score=?, remarks=?, evaluation_date=NOW() WHERE student_id=? AND supervisor_id=?");
        $stmt_upd->bind_param("isii", $performance_score, $remarks, $student_id, $supervisor_id);
        if ($stmt_upd->execute()) {
            $message = "<div class='alert alert-success'>Evaluation updated successfully.</div>";
        }
        $stmt_upd->close();
    } else {
        // Insert new evaluation
        $stmt_ins = $conn->prepare("INSERT INTO evaluations (student_id, supervisor_id, performance_score, remarks, evaluation_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt_ins->bind_param("iiis", $student_id, $supervisor_id, $performance_score, $remarks);
        if ($stmt_ins->execute()) {
            $message = "<div class='alert alert-success'>Evaluation submitted successfully.</div>";
        }
        $stmt_ins->close();
    }
}

// =================== Fetch Assigned Students with Evaluations ===================
$sql = "SELECT s.student_id, s.name, s.reg_no, e.performance_score, e.remarks, e.evaluation_date
        FROM students s
        JOIN attachments a ON s.student_id = a.student_id
        LEFT JOIN evaluations e ON s.student_id = e.student_id AND e.supervisor_id = ?
        WHERE a.supervisor_id = ?
        ORDER BY s.name ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $supervisor_id, $supervisor_id);
$stmt->execute();
$students_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor - Review Evaluations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            min-height: 100vh;
        }
        .card { border-radius: 15px; }
        .green-tick { position: absolute; bottom: 10px; right: 15px; font-size: 22px; color: #28a745; }
        .comment-container { position: relative; }
        .btn-sm { margin-right: 5px; }
    </style>
</head>
<body>
<div class="container mt-5">

    <a href="dashboard.php" class="btn btn-light mb-3">← Back to Dashboard</a>
    <h3 class="text-white mb-4">Student Evaluations</h3>

    <!-- Display success or error messages -->
    <?php 
    if (!empty($message)) { echo $message; }

    if (isset($_SESSION['error'])) {
        echo "<div class='alert alert-danger'>".$_SESSION['error']."</div>";
        unset($_SESSION['error']);
    }
    ?>

    <?php while ($row = $students_result->fetch_assoc()): ?>
    <div class="card shadow mb-4 p-3">
        <h5><?= htmlspecialchars($row['name']) ?> (<?= $row['reg_no'] ?>)</h5>

        <!-- =================== Evaluation Form =================== -->
        <form method="POST" class="mb-2 comment-container position-relative">
            <input type="hidden" name="student_id" value="<?= $row['student_id']; ?>">

            <div class="mb-2">
                <label>Performance Score (1-10)</label>
                <input type="number" name="performance_score" class="form-control" min="1" max="10"
                       value="<?= htmlspecialchars($row['performance_score']); ?>" required>
            </div>

            <div class="mb-2">
                <label>Remarks</label>
                <textarea name="remarks" class="form-control" rows="3" required><?= htmlspecialchars($row['remarks']); ?></textarea>

                <?php if ($row['performance_score'] || $row['remarks']): ?>
                    <span class="green-tick">&#10004;</span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">
                <?= ($row['performance_score'] || $row['remarks']) ? 'Update Evaluation' : 'Submit Evaluation'; ?>
            </button>

            <a href="export_evaluation_pdf.php?student_id=<?= $row['student_id']; ?>" class="btn btn-success btn-sm">
                Export PDF
            </a>
        </form>

        <?php if ($row['evaluation_date']): ?>
            <small class="text-muted">Last Evaluated: <?= $row['evaluation_date']; ?></small>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>

</div>
</body>
</html>