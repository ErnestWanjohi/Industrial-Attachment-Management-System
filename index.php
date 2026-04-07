<?php
// index.php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_role'])) {
    $role = $_SESSION['user_role'];
    if ($role == 'student') {
        header("Location: student/dashboard.php");
        exit;
    } elseif ($role == 'supervisor') {
        header("Location: supervisor/dashboard.php");
        exit;
    } elseif ($role == 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>IAMS - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-image: url('sak.jpeg');
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
            background-size: cover;
        }

        .card {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            background-color: rgba(255,255,255,0.95);
        }

        .btn-role {
            margin-bottom: 15px;
        }

        h2 {
            margin-bottom: 30px;
            font-weight: 700;
        }

        .about-section {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 15px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card text-center">
        <h2>Welcome to IAMS</h2>
        <p>Please select your login type:</p>

        <!-- Separate Login Buttons -->
        <a href="auth/login.php?role=student" class="btn btn-primary btn-lg w-100 btn-role">
            Student Login
        </a>

        <a href="auth/login.php?role=supervisor" class="btn btn-warning btn-lg w-100 btn-role">
            Supervisor Login
        </a>

        <a href="admin/dashboard.php" class="btn btn-success btn-lg w-100 btn-role">
            Admin Login
        </a>

        <div class="mt-3">
            <p>Don't have an account? 
                
            <a href="auth/register.php?role=student">Register as Student</a><br>
            <a href="auth/register.php?role=supervisor">Register as Supervisor</a>
            </p>
        </div>
    </div>

    <!-- About Section -->
    <div class="about-section text-center">
        <h3>About IAMS</h3>
        <p>
            The Industrial Attachment Management System (IAMS) is a platform designed to streamline 
            the management of student attachments. It allows students to track their progress, 
            supervisors to evaluate performance, and administrators to manage the entire process efficiently.
        </p>
        <p>
            Our goal is to simplify communication, improve transparency, and ensure a smooth 
            attachment experience for all users.
        </p>
    </div>

</div>

</body>
</html>