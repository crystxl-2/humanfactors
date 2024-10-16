<?php
// session start for user/role authentcation and session variable
session_start();

// checks if the user is a Production Operator (role_id == 3) (role specific)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != '3') {
    // If the user is not logged in or not a Production Operator, redirect to login
    header("Location: login.php");
    exit(); 
}

require_once 'task_connect.php';

// Initialize an array to store error messages and a variable for success messages
$errors = [];
$success = "";

// Fetch available machines from the database to populate the machine dropdown in the form
$machines_result = $conn->query("SELECT machine_id, machine_name FROM machines");

// Check if the 'Create Task' form was submitted
if (isset($_POST['create'])) {
    // Get form data and set default values if fields are optional (e.g., job_id)
    $job_id = !empty($_POST['job_id']) ? $_POST['job_id'] : null; 
    $machine_id = !empty($_POST['machine_id']) ? $_POST['machine_id'] : null;
    $task_note = $_POST['task_note']; 
    $created_by = $_SESSION['user_id']; 

    // Validate input fields
    if (empty($task_note)) {
        // If task note is empty, add an error message to the $errors array
        $errors[] = "Task Note is required.";
    } else {
        // SQL query to insert a new task into the tasks table
        $stmt = $conn->prepare("INSERT INTO tasks (job_id, machine_id, created_by, task_note) VALUES (?, ?, ?, ?)");
        // Bind parameters to the SQL query (iiis stands for two integers and a string)
        $stmt->bind_param("iiis", $job_id, $machine_id, $created_by, $task_note);

        // Execute the prepared statement
        if ($stmt->execute()) {
            // If successful, set a success message
            $success = "Task has been created successfully.";
        } else {
            // If an error occurs, add the error message to the $errors array
            $errors[] = "There was an error creating the task: " . $conn->error;
        }
        // Close the prepared statement
        $stmt->close();
    }
}

// Check if the 'Delete Task' action was triggered via GET request
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete']; // Get the task ID from the URL

    // Prepare a SQL statement to delete a task based on the task ID
    $stmt = $conn->prepare("DELETE FROM tasks WHERE task_id = ?");
    // Bind the task ID to the SQL query (i stands for integer)
    $stmt->bind_param("i", $task_id);

    // Execute the deletion statement
    if ($stmt->execute()) {
        // If successful, set a success message
        $success = "Task has been deleted successfully.";
    } else {
       
        $errors[] = "There was an error deleting the task: " . $conn->error;
    }
    // Close the prepared statement
    $stmt->close();
}

// Fetch all tasks from the database to display them in a table
$sql = "SELECT * FROM tasks";
$result = $conn->query($sql);

// this closes the database connection
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
            <!-- Display the logged-in user's username from the session -->
            <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <a href="/login/logout.php">Logout</a>
            <a href="/login/dashboard.php">Return</a>
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
            <label for="job_id">Job ID (Optional):</label>
            <input type="text" id="job_id" name="job_id" placeholder="Enter Job ID">

            <label for="machine_id">Machine ID:</label>
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
                <th>Job ID</th>
                <th>Machine ID</th>
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
                // If no tasks are found, display a message in the table
                echo "<tr><td colspan='7'>No tasks found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>
</body>
</html>
