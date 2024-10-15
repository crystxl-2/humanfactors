<?php
$host = "localhost";
$dbname = "abc-company-db";  // Name of the database in PHPMySQL
$username = "root";   
$password = "";   

// Creats the connection to the database
$conn = mysqli_connect($host, $username, $password, $dbname);


// Verifies and checks the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "Login Sucessful!";
}
?>
