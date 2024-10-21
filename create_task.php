<?php
session_start();
require_once 'task_connect.php'; 


// Requesting all these values
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // values that the user submitted
    $job_id = $_POST['job_id'];
    $machine_id = $_POST['machine_id'];
    $task_note = $_POST['task_note'];
    $created_by = $_SESSION['user_id'];


    // query to insert new tasks into the task table database
    $stmt = $conn->prepare("INSERT INTO tasks (job_id, machine_id, created_by, task_note) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $job_id, $machine_id, $created_by, $task_note);

    
    // query will be executed and inserted into the task table database
    if ($stmt->execute()) {
        header("Location: tasks.php?success=Task created successfully");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
