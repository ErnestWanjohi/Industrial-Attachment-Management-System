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
            background: #f8f9fa;
        }
        .card {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .btn-role {
            margin-bottom: 15px;
        }
        h2 {
            margin-bottom: 30px;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card text-center">
        <h2>Welcome to IAMS</h2>
        <p>Please select your login type:</p>

        <a href="auth/login.php" class="btn btn-primary btn-lg w-100 btn-role">Student / Supervisor Login</a>
        <a href="admin/dashboard.php" class="btn btn-success btn-lg w-100 btn-role">Admin Login</a>

        <div class="mt-3">
            <p>Don't have an account? <a href="auth/register.php">Register here</a></p>
        </div>
    </div>
</div>

</body>
</html>