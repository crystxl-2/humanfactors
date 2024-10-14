<?php
header('Content-Type: application/json');


$host = "localhost";
$username = "root";
$password = "";
$dbname = "abc-company-db"; 


$conn = new mysqli($host, $username, $password, $dbname);


if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Check if the machine name is provided
if (!isset($_GET['name']) || empty($_GET['name'])) {
    echo json_encode(['error' => 'Machine name is required']);
    exit;
}

// Escape the machine name to prevent SQL injection
$machineName = $conn->real_escape_string($_GET['name']);

// Query to fetch the machine analytics for the given machine name
$sql = "SELECT 
            temperature, 
            pressure, 
            vibration, 
            humidity, 
            power_consumption, 
            operational_status, 
            error_code, 
            production_count, 
            maintenance_log, 
            speed 
        FROM 
            machine_analytics 
        WHERE 
            machine_name = '$machineName'"; // Assuming machine_name is the correct column

$result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    echo json_encode(['error' => 'Query error: ' . $conn->error]);
    exit;
}

// Check if any analytics data was found for the given machine
if ($result->num_rows > 0) {
    $analytics = $result->fetch_assoc(); // Fetch the analytics data as an associative array
    echo json_encode($analytics); // Return the analytics data as JSON
} else {
    echo json_encode(['error' => 'No analytics data found for this machine']);
}

$conn->close();
?>
