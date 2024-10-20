<?php
require_once 'machine_conn.php'; // Include database connection

// Get machine name and timestamp from the request
if (isset($_GET['name']) && isset($_GET['timestamp'])) {
    $machine_name = $_GET['name'];
    $timestamp = $_GET['timestamp'];

    try {
        // Prepare the SQL query to fetch analytics data for the specific machine and timestamp
        $stmt = $conn->prepare("SELECT * FROM factory_logs WHERE machine_name = ? AND timestamp = ?");
        $stmt->bind_param("ss", $machine_name, $timestamp);
        $stmt->execute();

        $result = $stmt->get_result();
        $analytics = $result->fetch_assoc();

        if ($analytics) {
            // Send the analytics data back as JSON
            echo json_encode($analytics);
        } else {
            // No data found
            echo json_encode([]);
        }

    } catch (Exception $e) {
        // Return error message if there's an issue
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    // Return an error if the required parameters are missing
    echo json_encode(['error' => 'Machine name or timestamp not provided']);
}
?>
