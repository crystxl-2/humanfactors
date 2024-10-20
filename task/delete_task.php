<?php
session_start();
require_once 'task_connect.php'; 

if (isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];


    // sql query
    $stmt = $conn->prepare("DELETE FROM tasks WHERE task_id = ?");
    $stmt->bind_param("i", $task_id);


    // executes the statement which then deletes the task off in the dtable
    if ($stmt->execute()) {
        header("Location: tasks.php?success=Task deleted successfully");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
