<?php
session_start();

$_SESSION = array();

// This kills and delete the session cookies
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}


session_destroy();

// Redirect to login page
header("Location: index.php");
exit();
