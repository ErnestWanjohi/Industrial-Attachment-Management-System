<?php
session_start();

// 🔐 Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - IAMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8fafc;
        }

        .sidebar {
            height: 100vh;
            background-color: #1e293b;
            color: white;
            padding-top: 20px;
            position: fixed;
            width: 240px;
        }

        .sidebar a {
            display: block;
            color: #cbd5f5;
            padding: 12px 20px;
            text-decoration: none;
            transition: 0.2s;
        }

        .sidebar a:hover {
            background-color: #334155;
            color: white;
        }

        .main-content {
            margin-left: 240px;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 12px;
            transition: 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .topbar {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h5 class="text-center mb-4">IAMS</h5>

    <a href="#">Dashboard</a>
    <a href="progress_report.php">Upload Progress Report</a>
    <a href="evaluation.php">View Evaluations</a>
    <a href="add_attachment.php">Attachments</a>
    <a href="my_reports.php">My Reports</a>
    <a href="../auth/logout.php" class="text-danger">Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">

    <!-- Topbar -->
    <div class="topbar d-flex justify-content-between align-items-center shadow-sm">
        <h5 class="mb-0">Student Dashboard</h5>
        <span class="fw-semibold">👋 <?php echo $_SESSION['name']; ?></span>
    </div>

    <!-- Info Cards (NO BUTTONS) -->
    <div class="row g-4">

        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center">
                <h5>Upload Progress Report</h5>
                <p class="text-muted">Submit your internship updates via sidebar</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center">
                <h5>View Evaluations</h5>
                <p class="text-muted">Check supervisor feedback via sidebar</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center">
                <h5>Attachments</h5>
                <p class="text-muted">Manage uploaded documents via sidebar</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center">
                <h5>My Reports</h5>
                <p class="text-muted">View all submitted reports via sidebar</p>
            </div>
        </div>

    </div>

</div>

</body>
</html>