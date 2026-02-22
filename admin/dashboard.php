<?php
session_start();
include("../config/db.php");

// Simple admin authentication (username: admin, password: admin)
$admin_username = "admin";
$admin_password = "admin";

if (!isset($_SESSION['admin_logged_in'])) {
    $error = "";
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($username === $admin_username && $password === $admin_password) {
            $_SESSION['admin_logged_in'] = true;
        } else {
            $error = "Invalid username or password";
        }
    }
}

if (!isset($_SESSION['admin_logged_in'])):
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - IAMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow p-4 mx-auto" style="max-width: 400px;">
        <h3 class="text-center mb-4">Admin Login</h3>
        <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
</body>
</html>
<?php
    exit();
endif;

// Fetch dashboard counts
$student_count = $conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];
$supervisor_count = $conn->query("SELECT COUNT(*) as total FROM supervisors")->fetch_assoc()['total'];
$attachment_count = $conn->query("SELECT COUNT(*) as total FROM attachments")->fetch_assoc()['total'];
$pending_count = $conn->query("SELECT COUNT(*) as total FROM attachments WHERE status='Pending'")->fetch_assoc()['total'];

// Fetch table data
$students = $conn->query("SELECT * FROM students");
$supervisors = $conn->query("SELECT * FROM supervisors");
$attachments = $conn->query("SELECT * FROM attachments");
$progress_reports = $conn->query("SELECT * FROM progress_reports");
$evaluations = $conn->query("SELECT * FROM evaluations");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - IAMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Admin Dashboard</h2>
        <div>
            <a href="assign_supervisor.php" class="btn btn-primary me-2">Assign Supervisors</a>
            <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <!-- Dashboard Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white p-3">
                <h5>Total Students</h5>
                <h3><?= $student_count ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white p-3">
                <h5>Total Supervisors</h5>
                <h3><?= $supervisor_count ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white p-3">
                <h5>Total Attachments</h5>
                <h3><?= $attachment_count ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white p-3">
                <h5>Pending Approvals</h5>
                <h3><?= $pending_count ?></h3>
            </div>
        </div>
    </div>

    <!-- Tabs for tables -->
    <ul class="nav nav-tabs" id="adminTab" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#students">Students</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#supervisors">Supervisors</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#attachments">Attachments</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#progress_reports">Progress Reports</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#evaluations">Evaluations</button></li>
    </ul>

    <div class="tab-content mt-3">
        <!-- Students Table -->
        <div class="tab-pane fade show active" id="students">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr><th>ID</th><th>Name</th><th>Reg No</th><th>Email</th><th>Course</th></tr>
                </thead>
                <tbody>
                    <?php while($row = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['student_id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['reg_no'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['course'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Supervisors Table -->
        <div class="tab-pane fade" id="supervisors">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created At</th></tr>
                </thead>
                <tbody>
                    <?php while($row = $supervisors->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['supervisor_id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['role'] ?></td>
                            <td><?= $row['created_at'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Attachments Table -->
        <div class="tab-pane fade" id="attachments">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr><th>ID</th><th>Student ID</th><th>Company Name</th><th>Start Date</th><th>End Date</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php while($row = $attachments->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['attachment_id'] ?></td>
                            <td><?= $row['student_id'] ?></td>
                            <td><?= $row['company_name'] ?></td>
                            <td><?= $row['start_date'] ?></td>
                            <td><?= $row['end_date'] ?></td>
                            <td><?= $row['status'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Progress Reports Table -->
        <div class="tab-pane fade" id="progress_reports">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr><th>ID</th><th>Student ID</th><th>Week Number</th><th>Tasks Done</th><th>Supervisor Comments</th><th>Date Submitted</th></tr>
                </thead>
                <tbody>
                    <?php while($row = $progress_reports->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['report_id'] ?></td>
                            <td><?= $row['student_id'] ?></td>
                            <td><?= $row['week_number'] ?></td>
                            <td><?= $row['tasks_done'] ?></td>
                            <td><?= $row['supervisor_comments'] ?></td>
                            <td><?= $row['date_submitted'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Evaluations Table -->
        <div class="tab-pane fade" id="evaluations">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr><th>ID</th><th>Student ID</th><th>Supervisor ID</th><th>Score</th><th>Remarks</th></tr>
                </thead>
                <tbody>
                    <?php while($row = $evaluations->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['evaluation_id'] ?></td>
                            <td><?= $row['student_id'] ?></td>
                            <td><?= $row['supervisor_id'] ?></td>
                            <td><?= $row['performance_score'] ?></td>
                            <td><?= $row['remarks'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>