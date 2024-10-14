<?php
session_start();

// Check if the user is a Production Operator
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != '3') {
    header("Location: login.php");
    exit();
}

// Include the database connection file
require_once 'task_connect.php'; 

$errors = [];  
$success = ""; 

// Create new task
if (isset($_POST['create'])) {
    $job_id = $_POST['job_id'];
    $machine_id = $_POST['machine_id'];
    $task_note = $_POST['task_note'];
    $created_by = $_SESSION['user_id']; // Assuming user_id is stored in the session

    // Validate fields
    if (empty($task_note)) {
        $errors[] = "Task Note is required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO tasks (job_id, machine_id, created_by, task_note) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $job_id, $machine_id, $created_by, $task_note);

        if ($stmt->execute()) {
            $success = "Task has been created successfully.";
        } else {
            $errors[] = "There has been an error creating the task: " . $conn->error;
        }
    }
}

// Delete a task
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM tasks WHERE task_id = ?");
    $stmt->bind_param("i", $task_id);

    if ($stmt->execute()) {
        $success = "Task has been deleted successfully.";
    } else {
        $errors[] = "There has been an error deleting the task: " . $conn->error;
    }
}

// Fetch tasks from the database
$sql = "SELECT * FROM tasks";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management - Production Operators</title>
    <link rel="stylesheet" href="task.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">ABC Company</div>
            <div class="nav-links">
                <p>Welcome, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b></p>
                <a href="logout/logout.php">Logout</a>
                <a href="../dashboard.php">Return</a>
            </div>
        </div>
    </header>
    
    <main>
        <h2>Task List</h2>

        <!-- Display error or success messages -->
        <?php if ($errors): ?>
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

        <div class="form-container">
    <form id="createTaskForm" action="" method="POST">
        <h3>Create New Task</h3>
        <label for="job_id">Job ID (Optional):</label>
        <input type="text" id="job_id" name="job_id" placeholder="Enter Job ID">

        <label for="machine_id">Machine ID (Optional):</label>
        <input type="text" id="machine_id" name="machine_id" placeholder="Enter Machine ID">

        <label for="task_note">Task Note:</label>
        <textarea id="task_note" name="task_note" required placeholder="Enter task details..."></textarea>

        <input type="submit" name="create" value="Create Task">
    </form>
</div>


        <!-- Task List Table -->
        <table>
            <thead>
                <tr>
                    <th>Task ID</th>
                    <th>Job ID</th>
                    <th>Machine ID</th>
                    <th>Task Note</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['task_id'] . "</td>";
                        echo "<td>" . ($row['job_id'] ? $row['job_id'] : 'N/A') . "</td>";
                        echo "<td>" . ($row['machine_id'] ? $row['machine_id'] : 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['task_note']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['created_by']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "<td class='action-buttons'>
                                <a href='update_task.php?task_id=" . $row['task_id'] . "' class='btn'>Edit</a>
                                <a href='?delete=" . $row['task_id'] . "' class='btn delete' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No tasks found</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </main>
</body>
</html>
