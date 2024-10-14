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
                <a href="login/worker/dashboard.php">Return</a>
                <a href="../logout/logout.php">Logout</a>
            </div>
        </div>
    </header>

    <main>
        <h1>Machine Overview</h1>
        
        <table id="machineTable">
            <thead>
                <tr>
                    <th>Machine ID</th>
                    <th>Machine Name</th>
                    <th>Machine Type</th>
                    <th>Status</th>
                    <th>Last Maintenance</th>
                    <th>Analytics</th>
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
                    <tbody id="analyticsBody">
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
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
                tbody.innerHTML = ""; // Clear existing data

                data.machines.forEach(machine => {
                    const row = document.createElement("tr");
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

        // Function to view machine analytics in a modal
        async function viewAnalytics(machineName) {
    document.getElementById("machineName").innerText = machineName;

    try {
        const response = await fetch(`get_machine_analytics.php?name=${encodeURIComponent(machineName)}`);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const analytics = await response.json();
        console.log(analytics); // Log the analytics data to see what you're getting
        const tbody = document.getElementById("analyticsBody");
        tbody.innerHTML = ""; // Clear existing data

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
            tbody.innerHTML = `<tr><td colspan="10">No analytics data available for this machine.</td></tr>`;
        }

        document.getElementById("analyticsModal").style.display = "block"; // Show the modal
    } catch (error) {
        console.error('Error fetching analytics:', error);
    }
}

        populateMachineTable();
    </script>
</body>
</html>
