<?php


$host = "localhost";
$username = "root";
$password = "";
$dbname = "abc-company-db"; 


$conn = new mysqli($host, $username, $password, $dbname);

// Get data from the form
$task_id = $_POST['task_id'];
$job_id = !empty($_POST['job_id']) ? $_POST['job_id'] : NULL;
$machine_id = !empty($_POST['machine_id']) ? $_POST['machine_id'] : NULL;
$task_note = $_POST['task_note'];

// Update task in the database
$sql = "UPDATE tasks SET job_id = ?, machine_id = ?, task_note = ? WHERE task_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisi", $job_id, $machine_id, $task_note, $task_id);

if ($stmt->execute()) {
    echo "Task updated successfully!";
} else {
    echo "Error updating task: " . $conn->error;
}

$stmt->close();
$conn->close();
?>