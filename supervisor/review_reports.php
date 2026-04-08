<?php
session_start();
include("../config/db.php");

// 🔐 Restrict access to supervisors only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../auth/login.php");
    exit();
}

$supervisor_id = $_SESSION['user_id'];
$message = "";

// =================== Handle Supervisor Comment ===================
if (isset($_POST['report_id'])) {
    $report_id = intval($_POST['report_id']);
    $comment = trim($_POST['supervisor_comments']);

    $sql = "UPDATE progress_reports pr
            JOIN attachments a ON pr.student_id = a.student_id
            SET pr.supervisor_comments = ?
            WHERE pr.report_id = ? AND a.supervisor_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $comment, $report_id, $supervisor_id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Comment saved successfully.</div>";
    }

    $stmt->close();
}

// =================== Fetch Reports ===================
$sql = "SELECT pr.*, s.name 
        FROM progress_reports pr
        JOIN students s ON pr.student_id = s.student_id
        JOIN attachments a ON pr.student_id = a.student_id
        WHERE a.supervisor_id = ?
        ORDER BY pr.week_number DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Review Reports - Supervisor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1e3c72, #2a5298); min-height: 100vh; }
        .card { border-radius: 15px; }
        .comment-container { position: relative; }
        .green-tick { position: absolute; bottom: 5px; right: 10px; font-size: 20px; color: #28a745; }
    </style>
</head>
<body>
<div class="container mt-5">

    <a href="dashboard.php" class="btn btn-light mb-3">← Back to Dashboard</a>
    <h3 class="text-white mb-4">Student Progress Reports</h3>

    <?php echo $message; ?>

    <?php while ($row = $result->fetch_assoc()): ?>
    <div class="card shadow mb-4 p-3">
        <h5><?= htmlspecialchars($row['name']); ?> - Week <?= $row['week_number']; ?></h5>

        <p><strong>Tasks Done:</strong><br>
            <?= nl2br(htmlspecialchars($row['tasks_done'])); ?>
        </p>

        <!-- =================== Supervisor Comment =================== -->
        <form method="POST" class="mb-2 comment-container" id="form-<?= $row['report_id']; ?>">
            <input type="hidden" name="report_id" value="<?= $row['report_id']; ?>">

            <div class="mb-2 position-relative">
                <label>Supervisor Comment</label>
                <textarea name="supervisor_comments"
                          class="form-control"
                          rows="3"><?= htmlspecialchars($row['supervisor_comments']); ?></textarea>

                <?php if ($row['supervisor_comments']): ?>
                    <span class="green-tick">&#10004;</span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">
                <?= $row['supervisor_comments'] ? 'Update Comment' : 'Add Comment'; ?>
            </button>
        </form>

        <!-- =================== Export PDF Only =================== -->
        <div class="mb-2">
            <a href="export_report_pdf.php?id=<?= $row['report_id']; ?>" class="btn btn-success btn-sm">
                Export PDF
            </a>
        </div>

        <small class="text-muted">Submitted: <?= $row['date_submitted']; ?></small>
    </div>
    <?php endwhile; ?>

</div>
</body>
</html>