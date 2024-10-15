<?php
session_start();

// Check if role_id and username are set, if not, handle the error or redirect to login
if (!isset($_SESSION['role_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if session is not set
    exit();
}

$role_id = $_SESSION['role_id'];
$username = $_SESSION['username'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
<header>
    <div class="navbar">
        <div class="logo">ABC Company</div>
        <div class="nav-links">
            <p><?php echo htmlspecialchars($username); ?></p> <!-- Displaying the session username -->
            <a href="/login/logout.php">Logout</a>
            <a href="dashboard.php">Return</a> <!-- Return always points to dashboard.php -->
        </div>
    </div>
</header>

<main>
    <section class="dashboard">
        <!-- Conditionally display content based on role_id -->
        <?php if ($role_id == '1') : ?>
            <!-- Admin specific content -->
            <div class="card">
                <a href="admin/manage_users.php">User Management</a>
            </div>
            <div class="card">
                <a href="admin/manage_machines.php">Machine Management</a>
            </div>
            <div class="card">
                <a href="admin/manage_jobs.php">Job Management</a>
            </div>
            <div class="card">
                <a href="admin/audit_reports.php">Audit Reports</a>
            </div>

        <?php elseif ($role_id == '3') : ?>
            <!-- Production Operator specific content -->
            <div class="card">
                <a href="worker/task/task.php">View Tasks</a>
            </div>
            <div class="card">
                <a href="worker/machines/machines.php">View Machines</a>
            </div>
            <div class="card">
                <a href="worker/jobs/pending_jobs.php">Pending Jobs</a>
            </div>

        <?php elseif ($role_id == '2') : ?>
            <!-- Factory Manager specific content -->
            <div class="card">
                <a href="manager/manager_dashboard.php">Manager Panel</a>
            </div>

        <?php elseif ($role_id == '4') : ?>
            <!-- Auditor specific content -->
            <div class="card">
                <a href="audit/audit_reports.php">View Audit Reports</a>
            </div>

        <?php else : ?>
            <p>Role not recognized. Please contact an administrator.</p>
        <?php endif; ?>
    </section>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> ABC Company
</footer>
</body>
</html>
