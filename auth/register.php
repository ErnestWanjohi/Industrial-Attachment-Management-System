<?php
session_start();
include("../config/db.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $role_type = $_POST["role"]; // student or supervisor
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    if ($role_type == "student") {
        $reg_no = $_POST["reg_no"];
        $course = $_POST["course"];

        $sql = "INSERT INTO students (name, reg_no, email, password, course)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $reg_no, $email, $password, $course);

    } elseif ($role_type == "supervisor") {
        $supervisor_role = $_POST["supervisor_role"]; // university or industry

        $sql = "INSERT INTO supervisors (name, email, password, role)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $password, $supervisor_role);
    }

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Registration successful! <a href='login.php'>Login here</a></div>";
        // Optional: redirect after 2 seconds
        // header("refresh:2; url=login.php");
    } else {
        $message = "<div class='alert alert-danger'>Error: ".$stmt->error."</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration - IAMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function toggleFields() {
            var role = document.getElementById("role").value;
            document.getElementById("studentFields").style.display = role === "student" ? "block" : "none";
            document.getElementById("supervisorFields").style.display = role === "supervisor" ? "block" : "none";

            // Set required dynamically
            document.querySelector("input[name='reg_no']").required = role === "student";
            document.querySelector("input[name='course']").required = role === "student";
            document.querySelector("select[name='supervisor_role']").required = role === "supervisor";
        }
    </script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4">
        <h3 class="text-center mb-4">Register</h3>

        <?php echo $message; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Register As</label>
                <select name="role" id="role" class="form-select" onchange="toggleFields()" required>
                    <option value="">Select Role</option>
                    <option value="student">Student</option>
                    <option value="supervisor">Supervisor</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <!-- Student Fields -->
            <div id="studentFields" style="display:none;">
                <div class="mb-3">
                    <label class="form-label">Registration Number</label>
                    <input type="text" name="reg_no" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Course</label>
                    <input type="text" name="course" class="form-control">
                </div>
            </div>

            <!-- Supervisor Fields -->
            <div id="supervisorFields" style="display:none;">
                <div class="mb-3">
                    <label class="form-label">Supervisor Role</label>
                    <select name="supervisor_role" class="form-select">
                        <option value="">Select Supervisor Type</option>
                        <option value="university">University</option>
                        <option value="industry">Industry</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </div>
</div>

</body>
</html>