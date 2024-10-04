<?php
$host = "localhost";
$dbname = "test_db";  // Name of your database
$username = "root";   // Default XAMPP username
$password = "";       // Default XAMPP password (usually empty)

// Correct order: host, username, password, dbname
$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    echo "Failed to connect, try again later";
    // Optionally, you can include more details with: mysqli_connect_error()
} else {
    echo "Connected successfully!";
}
?>

