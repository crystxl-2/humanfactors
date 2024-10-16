<?php
session_start();

// Initialize $errors and $success to avoid undefined variable warnings
$errors = [];  
$success = "";

// Check if the user is an admin (role_id == 1)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != '1') {
    header("Location: login.php");
    exit();
}

require 'admin_conn.php'; 

// Fetch all users for dropdown (optional step)
$users = $conn->query("SELECT username FROM users");

// To create a new job
if (isset($_POST['create'])) {
    $job_name = $_POST['job_name'];
    $machine_id = $_POST['machine_id'];
    $status = $_POST['status'];
    $username = $_POST['username'];  // Replaced 'assigned_operator' with 'username'

    // Check if the username exists in the users table
    $user_check = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $user_check->bind_param("s", $username);
    $user_check->execute();
    $user_check->bind_result($count);
    $user_check->fetch();
    $user_check->close();

    if ($count == 0) {
        $errors[] = "Assigned operator (username) does not exist.";
    } else {
        // Validate fields
        if (empty($job_name) || empty($machine_id) || empty($status) || empty($username)) {
            $errors[] = "All fields are required to create a job.";
        } else {
            $stmt = $conn->prepare("INSERT INTO jobs (job_name, machine_id, status, username) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siss", $job_name, $machine_id, $status, $username);  // Updated to 'username'

            if ($stmt->execute()) {
                $success = "Job has been created successfully.";
            } else {
                $errors[] = "There has been an error creating the job: " . $conn->error;
            }
        }
    }
}

// To update job details
if (isset($_POST['update'])) {
    $job_id = $_POST['job_id'];
    $status = $_POST['status'];
    $username = $_POST['username'];  // Replaced 'assigned_operator' with 'username'
    $machine_id = $_POST['machine_id'];

    // Validate fields
    if (empty($status) || empty($username) || empty($machine_id)) {
        $errors[] = "All fields are required to update the job.";
    } else {
        $stmt = $conn->prepare("UPDATE jobs SET status = ?, username = ?, machine_id = ?, updated_at = NOW() WHERE job_id = ?");
        $stmt->bind_param("ssii", $status, $username, $machine_id, $job_id);  // Updated to 'username'

        if ($stmt->execute()) {
            $success = "Job details have been updated successfully.";
        } else {
            $errors[] = "There has been an error updating the job: " . $conn->error;
        }
    }
}

// To delete a job
if (isset($_POST['delete'])) {
    $job_id = $_POST['job_id'];

    $stmt = $conn->prepare("DELETE FROM jobs WHERE job_id = ?");
    $stmt->bind_param("i", $job_id);

    if ($stmt->execute()) {
        $success = "Job has been deleted successfully.";
    } else {
        $errors[] = "There has been an error deleting the job: " . $conn->error;
    }
}

// Fetch all jobs to display
$jobs = $conn->query("SELECT * FROM jobs");

// Fetch all machines for dropdown
$machines = $conn->query("SELECT * FROM machines");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">ABC Company</div>
            <div class="nav-links">
                <b><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?></b>
                <a href="logout.php">Logout</a>
                <a href="/login/dashboard.php">Return</a>
            </div>
        </div>
    </header>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success">
            <p><?php echo $success; ?></p>
        </div>
    <?php endif; ?>

    <!-- Create Job Form -->
    <form method="POST" action="">
        <h2>Create New Job</h2>
        <table class="form-table">
            <tr>
                <td><label>Issue</label></td>
                <td><input type="text" name="job_name" required></td>
            </tr>
            <tr>
                <td><label>Machine</label></td>
                <td>
                    <select name="machine_id" required>
                        <?php while ($machine = $machines->fetch_assoc()): ?>
                            <option value="<?php echo $machine['machine_id']; ?>"><?php echo $machine['machine_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Status</label></td>
                <td>
                    <select name="status" required>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Assigned Operator</label></td>
                <td>
                    <select name="username" required> <!-- Changed to dropdown -->
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($user['username']); ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="submit-row"><button type="submit" name="create">Create Job</button></td>
            </tr>
        </table>
    </form>

    <!-- List of Jobs with Update and Delete Functionality -->
    <h2>Existing Jobs</h2>
    <table class="form-table">
        <tr>
            <th>ID</th>
            <th>Job Name</th>
            <th>Machine</th>
            <th>Status</th>
            <th>Assigned Operator</th> <!-- Updated to Assigned Operator (Username) -->
            <th>Created At</th>
            <th>Updated At</th>
            <th>Actions</th>
        </tr>
        <?php while ($job = $jobs->fetch_assoc()): ?>
            <tr>
                <td><?php echo $job['job_id']; ?></td>
                <td><?php echo htmlspecialchars($job['job_name']); ?></td>
                <td><?php echo htmlspecialchars($job['machine_id']); ?></td>
                <td><?php echo htmlspecialchars($job['status']); ?></td>
                <td><?php echo htmlspecialchars($job['username']); ?></td> <!-- Updated to display username -->
                <td><?php echo htmlspecialchars($job['created_at']); ?></td>
                <td><?php echo htmlspecialchars($job['updated_at']); ?></td>
                <td>
                    <!-- Form to update job status -->
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                        <select name="status" required>
                            <option value="Pending" <?php if ($job['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                            <option value="In Progress" <?php if ($job['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                            <option value="Completed" <?php if ($job['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                        </select>
                        <input type="text" name="username" value="<?php echo $job['username']; ?>" required> <!-- Changed to username -->
                        <input type="hidden" name="machine_id" value="<?php echo $job['machine_id']; ?>">
                        <button type="submit" name="update">Update</button>
                    </form>

                    <!-- Form to delete job -->
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                        <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this job?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
