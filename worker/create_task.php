<?php
session_start();
require_once 'task_connect.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_id = $_POST['job_id'];
    $machine_id = $_POST['machine_id'];
    $task_note = $_POST['task_note'];
    $created_by = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO tasks (job_id, machine_id, created_by, task_note) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $job_id, $machine_id, $created_by, $task_note);

    if ($stmt->execute()) {
        header("Location: tasks.php?success=Task created successfully");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
