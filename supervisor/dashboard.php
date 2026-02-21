<?php
session_start();

// 🔐 Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'supervisor') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Supervisor Dashboard - IAMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">
            IAMS - <?php echo ucfirst($_SESSION['supervisor_type']); ?> Supervisor
        </span>
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
                <h5>View Assigned Students</h5>
                <p class="text-muted">Manage internship students</p>
                <a href="#" class="btn btn-primary">View</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center">
                <h5>Review Progress Reports</h5>
                <p class="text-muted">Evaluate submitted reports</p>
                <a href="#" class="btn btn-success">Review</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center">
                <h5>Submit Evaluations</h5>
                <p class="text-muted">Provide performance assessment</p>
                <a href="#" class="btn btn-warning">Evaluate</a>
            </div>
        </div>

    </div>

</div>

</body>
</html>