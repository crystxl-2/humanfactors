<?php
session_start();
require_once 'task_connect.php'; // Include the database connection

if (isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];

    $stmt = $conn->prepare("DELETE FROM tasks WHERE task_id = ?");
    $stmt->bind_param("i", $task_id);

    if ($stmt->execute()) {
        header("Location: tasks.php?success=Task deleted successfully");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
