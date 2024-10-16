<?php
session_start();
require 'report_conn.php';  // Adjust the path as necessary

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['alert_id'])) {
    $alert_id = $_POST['alert_id'];

    $sql = "DELETE FROM alerts WHERE alert_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $alert_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
    
    header("Location: pending_alerts.php");
    exit;
}
?>
