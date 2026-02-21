<?php
session_start();

// 🔐 Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard - IAMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand">IAMS - Student</span>
        <div>
            <span class="text-white me-3">
                Welcome, <?php echo $_SESSION['name']; ?>
            </span>
            <a href="../auth/logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">

    <div class="row g-4">

        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center">
                <h5>Upload Progress Report</h5>
                <p class="text-muted">Submit your internship updates</p>
                <a href="#" class="btn btn-primary">Upload</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center">
                <h5>View Evaluations</h5>
                <p class="text-muted">Check supervisor feedback</p>
                <a href="#" class="btn btn-success">View</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center">
                <h5>Attachments</h5>
                <p class="text-muted">Manage uploaded documents</p>
                <a href="#" class="btn btn-dark">Open</a>
            </div>
        </div>

    </div>

</div>

</body>
</html>