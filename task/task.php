<?php
// session start for user/role authentication and session variable
session_start();

// checks if the user is a Production Operator (role_id == 3) (role-specific)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != '3') {
    header("Location: login.php");
    exit(); 
}

require_once 'task_connect.php';

$errors = [];
$success = "";

// Fetch available machines from the database to populate the machine dropdown in the form
$machines_result = $conn->query("SELECT machine_id, machine_name FROM machines");

if (isset($_POST['create'])) {
    $machine_id = !empty($_POST['machine_id']) ? $_POST['machine_id'] : null;
    $task_note = $_POST['task_note']; 
    $created_by = $_SESSION['id']; //

    if (empty($task_note)) {
        $errors[] = "Task Note is required.";
    } else {
        // SQL query to insert a new task into the tasks table
        $stmt = $conn->prepare("INSERT INTO tasks (machine_id, created_by, task_note) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $machine_id, $created_by, $task_note);

        if ($stmt->execute()) {
            $success = "Task has been created successfully.";
        } else {
            $errors[] = "There was an error creating the task: " . $conn->error;
        }
        $stmt->close();
    }
}

// Check if the 'Delete Task' action was triggered via GET request
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete']; // Get the task ID from the URL

    // Prepare a SQL statement to delete a task based on the task ID
    $stmt = $conn->prepare("DELETE FROM tasks WHERE task_id = ?");
    $stmt->bind_param("i", $task_id);

    if ($stmt->execute()) {
        $success = "Task has been deleted successfully.";
    } else {
        $errors[] = "There was an error deleting the task: " . $conn->error;
    }
    $stmt->close();
}

// Fetch all tasks from the database to display them in a table, with formatted date and machine name
// Use a JOIN to get the username from the users table based on created_by (user_id)
$sql = "SELECT tasks.task_id, tasks.task_note, users.username as created_by, 
        DATE_FORMAT(tasks.created_at, '%d/%m/%Y %H:%i:%s') as formatted_created_at, 
        machines.machine_name 
        FROM tasks 
        JOIN users ON tasks.created_by = users.user_id 
        JOIN machines ON tasks.machine_id = machines.machine_id";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management - Production Operators</title>
    <!-- Link to external stylesheet for styling the page -->
    <link rel="stylesheet" href="task1.css">
</head>
<body>
<header>
    <div class="navbar">
        <div class="logo">ABC Company</div>
        <div class="nav-links">
            <a href="/login/worker/machines/machines.php">View Machines</a>
            <a href="/login/worker/update_machines/machine_update.php">Update Machines</a>
            <a href="/login/worker/jobs/pending_jobs.php">View Pending Jobs</a>
            <a href="/login/dashboard.php">Dashboard</a>

            <div class="username-logout">
                <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>
                <a href="/login/logout.php">Logout</a>
            </div>
    </div>
</header>

<main>
    <h2>Task List</h2>

    <!-- Display error messages if any exist -->
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Display success message if set -->
    <?php if ($success): ?>
        <div class="success">
            <p><?php echo $success; ?></p>
        </div>
    <?php endif; ?>

    <!-- Task Creation Form -->
    <div class="form-container">
        <form id="createTaskForm" action="" method="POST">
            <h3>Create New Task</h3>

            <label for="machine_id">Machine</label>
            <select id="machine_id" name="machine_id">
                <option value="">Select Machine</option>
                <!-- Populate the machine dropdown with data fetched from the database -->
                <?php while ($machine = $machines_result->fetch_assoc()): ?>
                    <option value="<?php echo $machine['machine_id']; ?>"><?php echo htmlspecialchars($machine['machine_name']); ?></option>
                <?php endwhile; ?>
            </select>

            <label for="task_note">Task Note:</label>
            <textarea id="task_note" name="task_note" required placeholder="Enter task details..."></textarea>

            <input type="submit" name="create" value="Create Task">
        </form>
    </div>

    <!-- Display a table with the list of tasks -->
    <table>
        <thead>
            <tr>
                <th>Task ID</th>
                <th>Machine Name</th>
                <th>Task Note</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Loop through the tasks and display each one in the table -->
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['task_id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['machine_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['task_note']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['created_by']) . "</td>";
                    echo "<td>" . $row['formatted_created_at'] . "</td>";
                    echo "<td class='action-buttons'>
                            <a href='update_task.php?task_id=" . $row['task_id'] . "' class='btn'>Edit</a>
                            <a href='?delete=" . $row['task_id'] . "' class='btn delete' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                // If no tasks are found, display a message in the table
                echo "<tr><td colspan='6'>No tasks found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>
</body>
</html>
