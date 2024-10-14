<?php
session_start();
require 'report_conn.php'; 


if (!isset($_SESSION['username'])) {
    header("Location: report.php");
    exit;
}


$user_id = $_SESSION['user_id']; 
$sql = "SELECT * FROM REPORT WHERE user_id='$user_id'";
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Page</title>
    <link rel="stylesheet" href="report.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">ABC Company</div>
            <div class="nav-links">
                <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>
                <a href="logout/logout.php">Logout</a>
                <a href="../dashboard.php">Return</a>
            </div>
        </div>
    </header>

    <h1>Factory Worker Report Page</h1>


    <div class="report-form">
        <h2>Submit a New Report</h2>
        <form method="POST" action="submit_report.php">
            <label for="report_title">Report Title:</label>
            <input type="text" id="report_title" name="report_title" placeholder="Enter report title..." required>

            <label for="report_summary">Report Summary:</label>
            <textarea id="report_summary" name="report_summary" placeholder="Enter report summary..." rows="8" required></textarea>

            <input type="submit" value="Submit Report">
        </form>
    </div>

    <!-- Display Previous Reports -->
    <h2>Your Previous Reports</h2>
    <table>
        <tr>
            <th>Report ID</th>
            <th>Title</th>
            <th>Summary</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php
        // Check if there are any reports for this user
        if (mysqli_num_rows($result) > 0) {
            // Loop through each report and display in the table
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>" . $row['report_id'] . "</td>
                        <td>" . htmlspecialchars($row['report_title']) . "</td>
                        <td>" . htmlspecialchars($row['report_summary']) . "</td>
                        <td>" . $row['report_date'] . "</td>
                        <td>" . $row['report_status'] . "</td>
                        <td>";

                // Conditionally display the Send button if the status is 'Pending'
                if ($row['report_status'] == 'Pending') {
                    echo "<form method='POST' action='send_report.php'>
                            <input type='hidden' name='report_id' value='" . $row['report_id'] . "'>
                            <button type='submit' class='send-button'>Send</button>
                          </form>";
                }

                // Delete button for every report
                echo "<form method='POST' action='delete_report.php'>
                        <input type='hidden' name='report_id' value='" . $row['report_id'] . "'>
                        <button type='submit' class='delete-button'>Delete</button>
                      </form>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No reports found.</td></tr>";
        }

        // Close the database connection
        mysqli_close($conn);
        ?>
    </table>

</body>
</html>
