<?php
$host = "localhost";
$dbname = "abc-company-db";  // Name of the database in PHPMySQL
$username = "root";   
$password = "";   

// Creates the connection
$conn = mysqli_connect($host, $username, $password, $dbname);


// Checks the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "Login Sucessful!";
}
?>
