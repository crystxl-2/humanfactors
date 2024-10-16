<?php
session_start();

// Check if the user is a Production Operator (role_id == 3)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != '3') {
    header("Location: login.php");
    exit();
}

// Initialize $errors and $success to avoid undefined variable warnings
$errors = [];  
$success = "";

require 'alert_conn.php'; 

// Fetch jobs assigned to the logged-in production operator along with the machine name
$username = $_SESSION['username']; // Assuming 'username' is stored in the session
$jobs = $conn->prepare("
    SELECT jobs.job_id, jobs.job_name, machines.machine_name, jobs.status, jobs.username, jobs.created_at, jobs.updated_at 
    FROM jobs 
    INNER JOIN machines ON jobs.machine_id = machines.machine_id
    WHERE jobs.username = ?
");
$jobs->bind_param("s", $username);
$jobs->execute();
$result = $jobs->get_result();

// Update job status (for jobs assigned to this operator only)
if (isset($_POST['update'])) {
    $job_id = $_POST['job_id'];
    $status = $_POST['status'];

    // Validate the fields
    if (empty($status)) {
        $errors[] = "Status is required to update the job.";
    } else {
        // Update job status for this operator only
        $stmt = $conn->prepare("UPDATE jobs SET status = ?, updated_at = NOW() WHERE job_id = ? AND username = ?");
        $stmt->bind_param("sis", $status, $job_id, $username);

        if ($stmt->execute()) {
            $success = "Job status has been updated successfully.";
        } else {
            $errors[] = "There has been an error updating the job: " . $conn->error;
        }
        $stmt->close();
    }
}

$jobs->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs - Production Operator</title>
    <link rel="stylesheet" href="jobs.css">
</head>
<body>
<header>
<div class="navbar">
        <div class="logo">ABC Company</div>
        <div class="nav-links">
            <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <a href="/login/logout.php">Logout</a>
            <a href="/login/dashboard.php">Return</a>
        </div>
    </div>
</header>

<main>
    <h2>Your Assigned Jobs</h2>

    <!-- Display error or success messages -->
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success">
            <p><?php echo $success; ?></p>
        </div>
    <?php endif; ?>

    <!-- List of Jobs with Update Functionality -->
    <table class="form-table">
        <tr>
            <th>ID</th>
            <th>Job Name</th>
            <th>Machine</th> <!-- Changed from machine_id to machine_name -->
            <th>Status</th>
            <th>Assigned Operator</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Actions</th>
        </tr>
        <?php while ($job = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $job['job_id']; ?></td>
                <td><?php echo htmlspecialchars($job['job_name']); ?></td>
                <td><?php echo htmlspecialchars($job['machine_name']); ?></td> <!-- Display machine name instead of ID -->
                <td><?php echo htmlspecialchars($job['status']); ?></td>
                <td><?php echo htmlspecialchars($job['username']); ?></td>
                <td><?php echo htmlspecialchars($job['created_at']); ?></td>
                <td><?php echo htmlspecialchars($job['updated_at']); ?></td>
                <td>
                    <!-- Form to update job status -->
                    <form method="POST" action="">
                        <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                        <select name="status" required>
                            <option value="Pending" <?php if ($job['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                            <option value="In Progress" <?php if ($job['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                            <option value="Completed" <?php if ($job['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                        </select>
                        <button type="submit" name="update">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</main>
</body>
</html>
