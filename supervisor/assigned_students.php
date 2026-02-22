<?php
session_start();
include("../config/db.php");

// Supervisor session check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    echo "<div class='alert alert-danger text-center mt-5'>Supervisor not logged in.</div>";
    exit;
}

$supervisor_id = $_SESSION['user_id'];

// Fetch students assigned to this supervisor
$sql = "SELECT DISTINCT s.student_id, s.name, s.email
        FROM students s
        INNER JOIN attachments a ON s.student_id = a.student_id
        WHERE a.supervisor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        table thead {
            background: #764ba2;
            color: #fff;
        }
        table tbody tr:hover {
            background: #e0e0e0;
        }
        .btn-back {
            background: #ff6b6b;
            border: none;
            color: #fff;
        }
        .btn-back:hover {
            background: #ff4c4c;
            color: #fff;
        }
        h2 {
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-start mb-4">
        <a href="dashboard.php" class="btn btn-back">&larr; Back to Dashboard</a>
    </div>

    <h2 class="text-center mb-4">Assigned Students</h2>

    <div class="card mx-auto" style="max-width: 900px;">
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>View Attachments</th>
                            <th>View Evaluation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 1;
                        while ($student = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $count++; ?></td>
                                <td><?= htmlspecialchars($student['name']); ?></td>
                                <td><?= htmlspecialchars($student['email']); ?></td>
                                <td>
                                    <a href="view_attachments.php?student_id=<?= $student['student_id']; ?>" class="btn btn-sm btn-info">
                                        View Attachments
                                    </a>
                                </td>
                                <td>
                                    <a href="view_evaluation.php?student_id=<?= $student['student_id']; ?>" class="btn btn-sm btn-primary">
                                        View Evaluation
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                No students are assigned to you yet.
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>