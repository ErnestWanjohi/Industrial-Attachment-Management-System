<?php
session_start();
include("../config/db.php");

// 🔐 Restrict access to students only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";
$showForm = true; // Form visibility flag
$student_id = $_SESSION['user_id'];

// Handle resubmission request
if (isset($_GET['update']) && $_GET['update'] == 1) {
    $showForm = true;
}

// Check latest attachment for this student
$check = $conn->prepare("SELECT attachment_id, company_name, start_date, end_date, status FROM attachments WHERE student_id = ? ORDER BY attachment_id DESC LIMIT 1");
$check->bind_param("i", $student_id);
$check->execute();
$result = $check->get_result();

$attachment = null;
if ($result->num_rows > 0) {
    $attachment = $result->fetch_assoc();
    // Pending or Approved submission
    if ($attachment['status'] != 'Rejected' && !isset($_GET['update'])) {
        $message = "<div class='alert alert-info'>
            You have already submitted your attachment details at <strong>{$attachment['company_name']}</strong> 
            from <strong>{$attachment['start_date']}</strong> to <strong>{$attachment['end_date']}</strong>.<br>
            Status: <strong>{$attachment['status']}</strong>.<br>
            You cannot submit another attachment until this one is reviewed.
        </div>";
        $showForm = false;
    } elseif ($attachment['status'] == 'Rejected' && !isset($_GET['update'])) {
        $message = "<div class='alert alert-warning'>
            Your previous attachment submission at <strong>{$attachment['company_name']}</strong> 
            from <strong>{$attachment['start_date']}</strong> to <strong>{$attachment['end_date']}</strong> was <strong>REJECTED</strong>.<br>
            Please update your details and resubmit.
            <div class='mt-3'>
                <a href='add_attachment.php?update=1' class='btn btn-primary'>Update Attachment</a>
            </div>
        </div>";
        $showForm = false;
    }
}

// Handle form submission (new or update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name = $_POST['company_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Insert new record
    $stmt = $conn->prepare("
        INSERT INTO attachments (student_id, company_name, start_date, end_date, status)
        VALUES (?, ?, ?, ?, 'Pending')
    ");
    $stmt->bind_param("isss", $student_id, $company_name, $start_date, $end_date);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>
            Attachment details submitted successfully! Your supervisor will review it shortly.
        </div>";
        $showForm = false;
    } else {
        $message = "<div class='alert alert-danger'>Error: ".$stmt->error."</div>";
    }
    $stmt->close();
}

$check->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Attachment - IAMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const startInput = document.querySelector("input[name='start_date']");
            const endInput = document.querySelector("input[name='end_date']");

            const today = new Date().toISOString().split('T')[0];
            startInput.min = today;

            startInput.addEventListener('change', () => {
                endInput.min = startInput.value;
                if (endInput.value < startInput.value) {
                    endInput.value = "";
                }
            });
        });
    </script>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand">IAMS - Student</span>
        <div>
            <span class="text-white me-3">Welcome, <?php echo $_SESSION['name']; ?></span>
            <a href="../auth/logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
        <h3 class="text-center mb-4">Submit Attachment Details</h3>

        <?php echo $message; ?>

        <?php if ($showForm): ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Submit</button>
        </form>
        <?php endif; ?>

        <div class="text-center mt-3">
            <a href="dashboard.php">← Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>