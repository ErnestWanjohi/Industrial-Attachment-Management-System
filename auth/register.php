<?php
session_start();
include("../config/db.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $role_type = $_POST["role"] ?? '';
    $name = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password_raw = $_POST["password"] ?? '';

    if (empty($role_type) || empty($name) || empty($email) || empty($password_raw)) {
        $message = "<div class='alert alert-danger'>All fields are required.</div>";
    } else {

        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        try {

            /* ===============================
               STUDENT REGISTRATION
            =============================== */
            if ($role_type == "student") {

                $reg_no = trim($_POST["reg_no"] ?? '');
                $course = trim($_POST["course"] ?? '');

                if (empty($reg_no) || empty($course)) {
                    $message = "<div class='alert alert-danger'>All student fields are required.</div>";
                } else {

                    // 🔎 Check duplicate reg_no
                    $check = $conn->prepare("SELECT student_id FROM students WHERE reg_no = ?");
                    $check->bind_param("s", $reg_no);
                    $check->execute();
                    $check->store_result();

                    if ($check->num_rows > 0) {
                        $message = "<div class='alert alert-danger'>Registration number already exists.</div>";
                    } else {

                        // 🔎 Check duplicate email
                        $checkEmail = $conn->prepare("SELECT student_id FROM students WHERE email = ?");
                        $checkEmail->bind_param("s", $email);
                        $checkEmail->execute();
                        $checkEmail->store_result();

                        if ($checkEmail->num_rows > 0) {
                            $message = "<div class='alert alert-danger'>Email already registered.</div>";
                        } else {

                            $sql = "INSERT INTO students (name, reg_no, email, password, course)
                                    VALUES (?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("sssss", $name, $reg_no, $email, $password, $course);

                            if ($stmt->execute()) {
                                $message = "<div class='alert alert-success'>
                                                Registration successful! 
                                                <a href='login.php'>Login here</a>
                                            </div>";
                            } else {
                                $message = "<div class='alert alert-danger'>Registration failed.</div>";
                            }

                            $stmt->close();
                        }

                        $checkEmail->close();
                    }

                    $check->close();
                }

            /* ===============================
               SUPERVISOR REGISTRATION
            =============================== */
            } elseif ($role_type == "supervisor") {

                $supervisor_role = $_POST["supervisor_role"] ?? '';

                if (empty($supervisor_role)) {
                    $message = "<div class='alert alert-danger'>Select supervisor type.</div>";
                } else {

                    // 🔎 Check duplicate email
                    $check = $conn->prepare("SELECT supervisor_id FROM supervisors WHERE email = ?");
                    $check->bind_param("s", $email);
                    $check->execute();
                    $check->store_result();

                    if ($check->num_rows > 0) {
                        $message = "<div class='alert alert-danger'>Email already registered.</div>";
                    } else {

                        $sql = "INSERT INTO supervisors (name, email, password, role)
                                VALUES (?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssss", $name, $email, $password, $supervisor_role);

                        if ($stmt->execute()) {
                            $message = "<div class='alert alert-success'>
                                            Registration successful! 
                                            <a href='login.php'>Login here</a>
                                        </div>";
                        } else {
                            $message = "<div class='alert alert-danger'>Registration failed.</div>";
                        }

                        $stmt->close();
                    }

                    $check->close();
                }
            }

        } catch (mysqli_sql_exception $e) {
            $message = "<div class='alert alert-danger'>System error. Please try again later.</div>";
        }
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