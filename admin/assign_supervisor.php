<?php
session_start();
include("../config/db.php");

// 🔐 Admin authentication
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle supervisor assignment or change
if (isset($_POST['assign']) && isset($_POST['attachment_id']) && isset($_POST['supervisor_id'])) {
    $attachment_id = $_POST['attachment_id'];
    $supervisor_id = $_POST['supervisor_id'];

    $stmt = $conn->prepare("UPDATE attachments SET supervisor_id = ?, status = 'Pending' WHERE attachment_id = ?");
    $stmt->bind_param("ii", $supervisor_id, $attachment_id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Attachment #$attachment_id supervisor assigned/updated successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
}

// Fetch all attachments with student info and assigned supervisor
$sql = "
SELECT a.*, s.name AS student_name, sup.name AS supervisor_name, a.supervisor_id
FROM attachments a
JOIN students s ON s.student_id = a.student_id
LEFT JOIN supervisors sup ON sup.supervisor_id = a.supervisor_id
ORDER BY a.attachment_id DESC
";
$result = $conn->query($sql);

// Fetch all supervisors for the dropdown
$supervisors = $conn->query("SELECT * FROM supervisors ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Supervisor - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between mb-4">
        <h2>Assign Supervisors to Attachments</h2>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if ($message) echo $message; ?>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Student Name</th>
                <th>Company</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Supervisor Assigned</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['attachment_id'] ?></td>
                <td><?= $row['student_name'] ?></td>
                <td><?= $row['company_name'] ?></td>
                <td><?= $row['start_date'] ?></td>
                <td><?= $row['end_date'] ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= $row['supervisor_name'] ?? "Not Assigned" ?></td>
                <td>
                    <form method="POST" class="d-flex gap-1">
                        <input type="hidden" name="attachment_id" value="<?= $row['attachment_id'] ?>">
                        <select name="supervisor_id" class="form-select form-select-sm" required>
                            <option value="">Select Supervisor</option>
                            <?php while($sup = $supervisors->fetch_assoc()): ?>
                                <option value="<?= $sup['supervisor_id'] ?>" 
                                    <?= ($row['supervisor_id'] == $sup['supervisor_id']) ? "selected" : "" ?>>
                                    <?= $sup['name'] ?> (<?= ucfirst($sup['role']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <button type="submit" name="assign" class="btn btn-primary btn-sm">
                            <?= $row['supervisor_id'] ? "Change Supervisor" : "Assign" ?>
                        </button>
                    </form>
                    <?php $supervisors->data_seek(0); // Reset dropdown for next row ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>