<?php
session_start();
include "db_conn.php";

if (isset($_POST['username']) && isset($_POST['password'])) {
    function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $username = validate($_POST['username']);
    $password = validate($_POST['password']);

    if (empty($username)) {
        header("Location: index.php?error=Username is required");
        exit();
    } else if (empty($password)) { 
        header("Location: index.php?error=Password is required");
        exit();
    }

   // Prepared statement to check username and password
   $sql = "SELECT * FROM users WHERE username=? AND password=?";
   $stmt = mysqli_prepare($conn, $sql);
   mysqli_stmt_bind_param($stmt, "ss", $username, $password);
   mysqli_stmt_execute($stmt);
   $result = mysqli_stmt_get_result($stmt);

   if (mysqli_num_rows($result) === 1) {
       $row = mysqli_fetch_assoc($result);
       
       // Now set session variables and redirect
       $_SESSION['username'] = $row['username'];
       $_SESSION['name'] = $row['first_name']; 
       $_SESSION['id'] = $row['user_id'];       
       $_SESSION['role_id'] = $row['role_id'];  


       header("Location: home.php");
       exit();
   } else {
       header("Location: index.php?error=Incorrect Username or Password");
       exit();
   }
}
?>
