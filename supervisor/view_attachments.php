<?php
session_start();
include("../config/db.php");

// 🔐 Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'supervisor') {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";

/* ===============================
   HANDLE APPROVE / REJECT
================================ */
if (isset($_GET['action']) && isset($_GET['id'])) {

    $attachment_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == "approve") {
        $status = "Approved";
    } elseif ($action == "reject") {
        $status = "Rejected";
    }

    $stmt = $conn->prepare("UPDATE attachments SET status = ? WHERE attachment_id = ?");
    $stmt->bind_param("si", $status, $attachment_id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Status updated successfully.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error updating status.</div>";
    }

    $stmt->close();
}

/* ===============================
   FETCH ATTACHMENTS + STUDENTS
================================ */
$sql = "SELECT a.*, s.name, s.reg_no 
        FROM attachments a
        JOIN students s ON a.student_id = s.student_id
        ORDER BY a.attachment_id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Review Attachments - IAMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">IAMS - Supervisor</span>
        <div>
            <span class="text-white me-3">
                Welcome, <?php echo $_SESSION['name']; ?>
            </span>
            <a href="../auth/logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">

    <h4 class="mb-4">Student Attachments</h4>

    <?php echo $message; ?>

    <div class="card shadow p-3">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Student Name</th>
                    <th>Reg No</th>
                    <th>Company</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['reg_no']; ?></td>
                    <td><?php echo $row['company_name']; ?></td>
                    <td><?php echo $row['start_date']; ?></td>
                    <td><?php echo $row['end_date']; ?></td>
                    <td>
                        <?php
                            if ($row['status'] == "Pending") {
                                echo "<span class='badge bg-warning'>Pending</span>";
                            } elseif ($row['status'] == "Approved") {
                                echo "<span class='badge bg-success'>Approved</span>";
                            } else {
                                echo "<span class='badge bg-danger'>Rejected</span>";
                            }
                        ?>
                    </td>
                    <td>
                        <?php if ($row['status'] == "Pending") { ?>
                            <a href="?action=approve&id=<?php echo $row['attachment_id']; ?>" 
                               class="btn btn-success btn-sm">Approve</a>

                            <a href="?action=reject&id=<?php echo $row['attachment_id']; ?>" 
                               class="btn btn-danger btn-sm">Reject</a>
                        <?php } else { ?>
                            <span class="text-muted">No Action</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>

</div>

</body>
</html>