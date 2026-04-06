<?php
session_start();

// 🔐 Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'supervisor') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor Dashboard - IAMS</title>
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
    <a href="assigned_students.php">Assigned Students</a>
    <a href="review_reports.php">Progress Reports</a>
    <a href="submit_evaluation.php">Evaluations</a>
    <a href="view_attachments.php">Attachments</a>
    <a href="../auth/logout.php" class="text-danger">Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">

    <!-- Topbar -->
    <div class="topbar d-flex justify-content-between align-items-center shadow-sm">
        <h5 class="mb-0">
            <?php echo ucfirst($_SESSION['supervisor_type']); ?> Supervisor Dashboard
        </h5>

        <span class="fw-semibold">
            👋 <?php echo $_SESSION['name']; ?>
        </span>
    </div>

    <!-- Info Cards (NO LINKS) -->
    <div class="row g-4">

        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center">
                <h5>Assigned Students</h5>
                <p class="text-muted">View and manage students from the sidebar</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center">
                <h5>Progress Reports</h5>
                <p class="text-muted">Review reports via the sidebar menu</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center">
                <h5>Evaluations</h5>
                <p class="text-muted">Submit evaluations from the sidebar</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center">
                <h5>Attachments</h5>
                <p class="text-muted">Approve or reject placements via sidebar</p>
            </div>
        </div>

    </div>

</div>

</body>
</html>