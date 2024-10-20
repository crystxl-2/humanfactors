<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Machine Dashboard</title>
    <link rel="stylesheet" href="machines.css">
</head>
<body>
<header>
    <div class="navbar">
        <div class="logo">ABC Company</div>
        <div class="nav-links">
            <a href="/login/worker/task/task.php">View Tasks</a>
            <a href="/login/worker/jobs/pending_jobs.php">View Pending Jobs</a>
            <a href="/login/worker/update_machines/machine_update.php">Update Machines</a>
            <a href="/login/dashboard.php">Dashboard</a>
            
            <div class="username-logout">
                <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>
                <a href="/login/logout.php">Logout</a>
            </div>
        </div>
    </div>
</header>

<main>
    <h1>Machine Overview</h1>
    
    <table id="machineTable">
        <thead>
            <tr>
                <!-- machine table -->
                <th>Machine ID</th>
                <th>Machine Name</th>
                <th>Machine Type</th>
                <th>Status</th>
                <th>Last Maintenance</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be inserted here -->
        </tbody>
    </table>

    <!-- Modal for Machine Analytics -->
    <div id="analyticsModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Analytics for <span id="machineName"></span></h2>

            <!-- Date and time picker -->
            <form id="analyticsForm" onsubmit="fetchFilteredAnalytics(event)">
                <label for="timestamp">Select Date and Time:</label>
                <input type="datetime-local" id="timestamp" name="timestamp" required>

                <!-- Submit button -->
                <button type="submit">Fetch Analytics</button>
            </form>

            <!-- Existing machine analytics data -->
            <table id="analyticsTable">
                <thead>
                    <tr>
                        <th>Temperature</th>
                        <th>Pressure</th>
                        <th>Vibration</th>
                        <th>Humidity</th>
                        <th>Power Consumption</th>
                        <th>Operational Status</th>
                        <th>Error Code</th>
                        <th>Production Count</th>
                        <th>Maintenance Log</th>
                        <th>Speed</th>
                    </tr>
                </thead>
                <tbody id="analyticsBody"></tbody>
            </table>
        </div>
    </div>
</main>

<script>
    // Function to populate the machine table with data fetched from the server
    async function populateMachineTable() {
        try {
            const response = await fetch('get_machines.php');
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            const data = await response.json();
            if (data.error) {
                console.error(data.error);
                return;
            }

            const tbody = document.querySelector("#machineTable tbody");
            tbody.innerHTML = "";

            // Iterate over the list of machines received from the server
            data.machines.forEach(machine => {
                // Create a new row for each machine
                const row = document.createElement("tr");

                // Set the inner HTML of the row with machine data, including a view button for analytics
                row.innerHTML = `
                    <td>${machine.id}</td>
                    <td>${machine.name}</td>
                    <td>${machine.type}</td>
                    <td class="status-${machine.status.toLowerCase()}">${machine.status}</td>
                    <td>${machine.lastMaintenance}</td>
                    <td><button onclick="viewAnalytics('${machine.name}')">View Analytics</button></td>
                `;

                tbody.appendChild(row);
            });
        } catch (error) {
            console.error('Error fetching machines:', error);
        }
    }

    // This Function allows the modal pop up and outputs the machine analytics data
    function viewAnalytics(machineName) {
        // Display the machine name in the modal's header
        document.getElementById("machineName").innerText = machineName;

        // Show the modal
        document.getElementById("analyticsModal").style.display = "block";
    }

    // Fetch filtered analytics data based on the machine name and selected timestamp
    async function fetchFilteredAnalytics(event) {
        event.preventDefault(); // Prevent form from submitting the traditional way

        const machineName = document.getElementById("machineName").innerText;
        const timestamp = document.getElementById("timestamp").value;

        try {
            // Fetch machine analytics filtered by the selected timestamp
            const response = await fetch(`get_machine_analytics.php?name=${encodeURIComponent(machineName)}&timestamp=${encodeURIComponent(timestamp)}`);
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }

            const analytics = await response.json();
            const tbody = document.getElementById("analyticsBody");
            tbody.innerHTML = ""; // Clear previous data

            // Populate table with analytics data
            if (analytics && Object.keys(analytics).length > 0) {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${analytics.temperature}</td>
                    <td>${analytics.pressure}</td>
                    <td>${analytics.vibration}</td>
                    <td>${analytics.humidity}</td>
                    <td>${analytics.power_consumption}</td>
                    <td>${analytics.operational_status}</td>
                    <td>${analytics.error_code}</td>
                    <td>${analytics.production_count}</td>
                    <td>${analytics.maintenance_log}</td>
                    <td>${analytics.speed}</td>
                `;
                tbody.appendChild(row);
            } else {
                tbody.innerHTML = `<tr><td colspan="10">No analytics data available for this machine at the selected time.</td></tr>`;
            }
        } catch (error) {
            console.error('Error fetching analytics:', error);
        }
    }

    // Event listener to close the modal when the close button is clicked
    document.getElementById("closeModal").onclick = function() {
        document.getElementById("analyticsModal").style.display = "none";
    };

    // Event listener to close the modal when clicking outside of the modal content
    window.onclick = function(event) {
        const modal = document.getElementById("analyticsModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };

    // Call the function to load the machine data and populate the table when the page loads
    populateMachineTable();
</script>
</body>
</html>
