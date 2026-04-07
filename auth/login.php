<?php
session_start();
include("../config/db.php");

$role_from_url = $_GET['role'] ?? ''; // 🔹 Get role from URL
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $message = "<div class='alert alert-danger'>Please enter email and password.</div>";
    } else {
        if ($role_from_url === 'student') {
            // Check Students Table
            $stmt = $conn->prepare("SELECT student_id, name, email, password FROM students WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['student_id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = 'student';
                    header("Location: ../student/dashboard.php");
                    exit();
                } else {
                    $message = "<div class='alert alert-danger'>Invalid email or password.</div>";
                }
            } else {
                $message = "<div class='alert alert-danger'>User not found.</div>";
            }

        } elseif ($role_from_url === 'supervisor') {
            // Check Supervisors Table
            $stmt = $conn->prepare("SELECT supervisor_id, name, email, password, role FROM supervisors WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['supervisor_id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = 'supervisor';
                    $_SESSION['supervisor_type'] = $user['role'];
                    header("Location: ../supervisor/dashboard.php");
                    exit();
                } else {
                    $message = "<div class='alert alert-danger'>Invalid email or password.</div>";
                }
            } else {
                $message = "<div class='alert alert-danger'>User not found.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Role not specified. Use student or supervisor login link.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - IAMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4" style="max-width: 450px; margin:auto;">
        <h3 class="text-center mb-4">Login</h3>

        <?php echo $message; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

      <div class="text-center mt-3">
    <a href="../auth/register.php?role=<?php echo $role_from_url ?: 'student'; ?>">
        Don't have an account? Register
    </a>
</div>
    </div>
</div>

</body>
</html>