<?php
session_start();

// Check if the user is an admin (role_id == 1)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != '1') {
    header("Location: login.php");
    exit();
}

require 'admin_conn.php'; 

$errors = [];  
$success = ""; 

// To update machine status
if (isset($_POST['update'])) {
    $machine_id = $_POST['machine_id'];
    $status = $_POST['status'];
    $last_maintenance = $_POST['last_maintenance'];

    // this validates to make sure that all fields/text boxes must be filled out
    if (empty($status) || empty($last_maintenance)) {
        $errors[] = "All fields are required to update the machine.";
    } else {
        $stmt = $conn->prepare("UPDATE machines SET status = ?, last_maintenance = ? WHERE machine_id = ?");
        $stmt->bind_param("ssi", $status, $last_maintenance, $machine_id);

        if ($stmt->execute()) {
            $success = "Machine status has been updated successfully.";
        } else {
            $errors[] = "There has been an error updating the machine: " . $conn->error;
        }
    }
}

// Fetch all machines to display
$machines = $conn->query("SELECT * FROM machines");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Machines</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">ABC Company</div>
            <div class="nav-links">
                <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>
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

    <!-- List of Machines with Update Functionality -->
    <h2>Existing Machines</h2>
    <table class="form-table">
        <tr>
            <th>ID</th>
            <th>Machine Name</th>
            <th>Status</th>
            <th>Last Maintenance</th>
            <th>Actions</th>
        </tr>
        <?php while ($machine = $machines->fetch_assoc()): ?>
            <tr>
                <td><?php echo $machine['machine_id']; ?></td>
                <td><?php echo htmlspecialchars($machine['machine_name']); ?></td>
                <td><?php echo htmlspecialchars($machine['status']); ?></td>
                <td><?php echo htmlspecialchars($machine['last_maintenance']); ?></td>
                <td>
                    <!-- Form to update machine status -->
                    <form method="POST" action="">
                        <input type="hidden" name="machine_id" value="<?php echo $machine['machine_id']; ?>">
                        <select name="status" required>
                            <option value="Running" <?php if($machine['status'] == 'Running') echo 'selected'; ?>>Running</option>
                            <option value="Stopped" <?php if($machine['status'] == 'Stopped') echo 'selected'; ?>>Stopped</option>
                            <option value="Under Maintenance" <?php if($machine['status'] == 'Under Maintenance') echo 'selected'; ?>>Under Maintenance</option>
                        </select>
                        <input type="date" name="last_maintenance" value="<?php echo $machine['last_maintenance']; ?>" required>
                        <button type="submit" name="update">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
