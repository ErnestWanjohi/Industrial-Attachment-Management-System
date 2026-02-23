<?php
session_start();
include("../config/db.php");

// 🔐 Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'supervisor') {
    header("Location: ../auth/login.php");
    exit();
}

$supervisor_id = $_SESSION['user_id'];
$message = "";

/* ===============================
   HANDLE APPROVE / REJECT
================================ */
// if (isset($_GET['action']) && isset($_GET['id'])) {

//     $attachment_id = intval($_GET['id']);
//     $action = $_GET['action'];

//     if ($action == "approve") {
//         $status = "Approved";
//     } elseif ($action == "reject") {
//         $status = "Rejected";
//     }

//     // Update only if attachment belongs to THIS supervisor
//     $update_sql = "UPDATE attachments 
//                    SET status = ? 
//                    WHERE attachment_id = ? 
//                    AND supervisor_id = ?";

//     $stmt = $conn->prepare($update_sql);
//     $stmt->bind_param("sii", $status, $attachment_id, $supervisor_id);

//     if ($stmt->execute()) {
//         $message = "<div class='alert alert-success'>Status updated successfully.</div>";
//     } else {
//         $message = "<div class='alert alert-danger'>Error updating status.</div>";
//     }

//     $stmt->close();
// }

/* ===============================
   FETCH ONLY THIS SUPERVISOR'S ATTACHMENTS
================================ */
$sql = "SELECT e.*, s.name, s.reg_no
        FROM evaluations e
        JOIN students s ON e.student_id = s.student_id
        WHERE e.supervisor_id = ?
        ORDER BY e.evaluation_id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Evaluations - IAMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            min-height: 100vh;
        }

        .main-card {
            border-radius: 15px;
            background: #ffffff;
        }

        .table thead {
            background: #1e3c72;
            color: white;
        }

        .btn-custom {
            border-radius: 20px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark shadow">
    <div class="container">
        <span class="navbar-brand">IAMS - Supervisor Panel</span>
        <div>
            <span class="text-white me-3">
                Welcome, <?php echo $_SESSION['name']; ?>
            </span>
            <a href="../auth/logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">My Evaluations</h3>
        <a href="dashboard.php" class="btn btn-light btn-sm">← Back to Dashboard</a>
    </div>

    <?php echo $message; ?>

    <div class="card shadow-lg p-4 main-card">

        <?php if ($result->num_rows > 0) { ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Reg No</th>
                        <th>Pefromance score</th>
                        <th>Remarks</th>
                        <th>Evaluation Date</th>
                        
                    </tr>
                </thead>
                <tbody>

                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['reg_no']; ?></td>
                        <td><?php echo $row['performance_score']; ?></td>
                        <td><?php echo $row['remarks']; ?></td>
                        <td><?php echo $row['evaluation_date']; ?></td>
                    </tr>
                <?php } ?>

                </tbody>
            </table>
        </div>

        <?php } else { ?>

            <div class="alert alert-info text-center">
                No evaluation done.
            </div>

        <?php } ?>

    </div>
</div>

</body>
</html>