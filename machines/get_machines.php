<?php
header('Content-Type: application/json');


$host = "localhost";
$username = "root";
$password = "";
$dbname = "abc-company-db"; 

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query data from the machine table
$sql = "SELECT machine_id, machine_name, machine_type, status, last_maintenance FROM machines"; 

// executes the query above
$result = $conn->query($sql);

$machines = [];

// checks if any rows returned from the query and iterates through each row.
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $machines[] = [
            "id" => $row["machine_id"],
            "name" => $row["machine_name"],
            "type" => $row["machine_type"],
            "status" => $row["status"],
            "lastMaintenance" => $row["last_maintenance"]
        ];
    }
}

// Return the data in JSON format
echo json_encode(["machines" => $machines]);

$conn->close();
?>