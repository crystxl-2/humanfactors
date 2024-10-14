<?php
session_start();
require 'report_conn.php'; 

//  This checks to see if  the from was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; 
    $report_title = mysqli_real_escape_string($conn, $_POST['report_title']);
    $report_summary = mysqli_real_escape_string($conn, $_POST['report_summary']);
    $report_date = date("Y-m-d"); 
    $report_status = 'Pending';

    // Insert the report into the database
    $sql = "INSERT INTO REPORT (user_id, report_title, report_summary, report_date, report_status) 
            VALUES ('$user_id', '$report_title', '$report_summary', '$report_date', '$report_status')";

    if (mysqli_query($conn, $sql)) {
        echo "Report submitted successfully.";
        header("Location: report.php"); 
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    mysqli_close($conn);
}
?>
