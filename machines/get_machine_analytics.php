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

// Check if the 'name' parameter is provided in the URL (via GET request)
if (!isset($_GET['name']) || empty($_GET['name'])) {
    // If the 'name' parameter is missing or empty, return an error in JSON format
    echo json_encode(['error' => 'Machine name is required']);
    exit; 
}

// Escape the machine name to prevent SQL injection attacks
$machineName = $conn->real_escape_string($_GET['name']);

// query to retrieve the machine's analytics data from the table
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
            machine_name = '$machineName'"; 

// Execute the query
$result = $conn->query($sql);

// checks if the query was successful, if error, it will be returned with an error message
if ($result === false) {
    echo json_encode(['error' => 'Query error: ' . $conn->error]);
    exit; 
}

// Check if any rows were returned from the query, data that is fetched is associative array (column names as keys)
if ($result->num_rows > 0) {
    $analytics = $result->fetch_assoc();
    echo json_encode($analytics);
} else {

    echo json_encode(['error' => 'No analytics data found for this machine']);
}

$conn->close();
?>
