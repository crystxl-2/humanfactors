<?php
session_start();
require 'report_conn.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $report_id = $_POST['report_id'];

    // Delete the report from the database
    $sql = "DELETE FROM REPORT WHERE report_id='$report_id'";

    if (mysqli_query($conn, $sql)) {
        echo "Report deleted successfully.";
        header("Location: report.php"); // Redirect to the report page
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
