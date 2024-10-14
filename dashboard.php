<?php
session_start();

// Check if role_id is set, if not, handle the error or redirect to login
if (!isset($_SESSION['role_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if session is not set
    exit();
}

// Now we can safely use role_id
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
                <b><?php echo htmlspecialchars($username); ?></b>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <main>
        <section class="dashboard">
            <h2>Welcome to the Dashboard, <?php echo htmlspecialchars($username); ?></h2>

            <!-- Conditionally display content based on role_id -->
            <?php if ($role_id == '1') : ?>
                <h3>Admin Panel</h3>
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
                </div>
                <div class="card">
                    <a href="admin/audit_reports.php">Audit Reports</a>
                </div>

            <?php elseif ($role_id == '3') : ?>
                <h3>Production Operator Dashboard</h3>
                <!-- Production Operator specific content -->
                <div class="card">
                    <a href="worker/task/task.php">View Tasks</a>
                </div>
                <div class="card">
                    <a href="worker/machines/machines.php">View Machines</a>
                </div>
                <div class="card">
                    <a href="worker/report/report.php">View Worker Reports</a>
                </div>

            <?php elseif ($role_id == '2') : ?>
                <h3>Factory Manager Dashboard</h3>
                <!-- Factory Manager specific content -->
                <div class="card">
                    <a href="manager/manager_dashboard.php">Manager Panel</a>
                </div>

            <?php elseif ($role_id == '4') : ?>
                <h3>Auditor Dashboard</h3>
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
        <p>&copy; <?php echo date("Y"); ?> ABC Company. All rights reserved.</p>
    </footer>
</body>
</html>
