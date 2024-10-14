<?php
session_start();

// This checks to see if the user is an admin (role_id == 1)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != '1') {
    header("Location: login.php");
    exit();
}

require 'admin_conn.php'; 

$errors = [];  
$success = ""; 
$username = $first_name = $last_name = $role_id = "";

// To create a new user
if (isset($_POST['create'])) {
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $role_id = $_POST['role_id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // this validates to make sure that all fields/text boxes must be filled out
    if (empty($username) || empty($password) || empty($first_name) || empty($last_name) || empty($role_id)) {
        $errors[] = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role_id, first_name, last_name) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $username, $password, $role_id, $first_name, $last_name);

        if ($stmt->execute()) {
            $success = "User has been created successfully.";
        } else {
            $errors[] = "There has been an error creating the user: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">ABC Company</div>
            <div class="nav-links">
                <b><?php echo htmlspecialchars($_SESSION['username']); ?></b> <!-- Displaying the session username -->
                <a href="logout.php">Logout</a>
                <a href="../login/dashboard.php">Return</a>
            </div>
        </div>
    </header>

    <?php if ($errors): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success">
            <p><?php echo $success; ?></p>
        </div>
    <?php endif; ?>

    <!-- Create User Form -->
    <form method="POST" action="">
    <h2>Create New User</h2>
    <table class="form-table">
        <tr>
            <td><label>Username</label></td>
            <td><input type="text" name="username" required></td>
        </tr>
        <tr>
            <td><label>First Name</label></td>
            <td><input type="text" name="first_name" required></td>
        </tr>
        <tr>
            <td><label>Last Name</label></td>
            <td><input type="text" name="last_name" required></td>
        </tr>
        <tr>
            <td><label>Password</label></td>
            <td><input type="password" name="password" required></td>
        </tr>
        <tr>
            <td><label>Role</label></td>
            <td>
                <select name="role_id">
                    <!-- Dynamically populate roles from the roles table -->
                    <?php
                    $roles = $conn->query("SELECT * FROM roles");
                    while ($role = $roles->fetch_assoc()) {
                        echo "<option value='" . $role['role_id'] . "'>" . $role['role_name'] . "</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="submit-row"><button type="submit" name="create">Create User</button></td>
        </tr>
    </table>
    </form>

</body>
</html>
