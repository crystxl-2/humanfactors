<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['id']) && isset($_SESSION['username'])) {
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/welcome.css">
        <script src="script.js" defer></script>
        <title>Home</title>
    </head>
    <body>
        <div class="container" id="container">  
            <div class="box-1">
                <h1>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
                <p>Welcome Back, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>!</p>

                <h1 id="current-time" class="current-time"></h1>
                <h1 id="current-date" class="current-date"></h1>

                <!-- js to Show current Date and Time -->
                
                <script>
                    function updateDateTime() {
                        const timeElement = document.getElementById("current-time");
                        const dateElement = document.getElementById("current-date");
                        const currentDate = new Date();
                        timeElement.innerHTML = currentDate.toLocaleTimeString();
                        dateElement.innerHTML = currentDate.toLocaleDateString();
                    }
                    updateDateTime();
                    setInterval(updateDateTime, 1000); // Update every second
                </script>

                <!-- Shows the current schedule -->
                <div class="schedule" id="schedule">
                    <p><b>Your schedule:</b></p>
                    <p><b>0800 to 1600</b></p>
                </div>

                <!-- Button Classes  -->
                <div class="button-container">
                    <div class="field">
                        <input type="button" class="btn" id="shift-start-btn" value="Shift Start" required>
                        <dialog id="shift-start-dialog">
                            <p id="shift-start-message"></p>
                            <button id="close-shift-start-dialog" type="button">&#x2715 Return</button>
                        </dialog>
                    </div>
                    <div class="field">
                        <input type="button" class="btn" id="shift-end-btn" value="Shift End" required>
                        <dialog id="shift-end-dialog">
                            <p id="shift-end-message"></p>
                            <button id="close-shift-end-dialog" type="button">&#x2715 Return</button>
                        </dialog>
                    </div>
                    
                    <form action="dashboard.php" method="POST">
    <div class="field">
        <input type="submit" class="btn" value="Access Dashboard"required>
    </div>
</form>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Shift Start and End Dialogs -->
        <script>
            const shiftStartBtn = document.getElementById("shift-start-btn");
            const shiftStartDialog = document.getElementById("shift-start-dialog");
            const shiftStartMessage = document.getElementById("shift-start-message");
            const closeShiftStartDialogBtn = document.getElementById("close-shift-start-dialog");

            const shiftEndBtn = document.getElementById("shift-end-btn");
            const shiftEndDialog = document.getElementById("shift-end-dialog");
            const shiftEndMessage = document.getElementById("shift-end-message");
            const closeShiftEndDialogBtn = document.getElementById("close-shift-end-dialog");

            shiftStartBtn.addEventListener("click", () => {
                const currentTime = new Date().toLocaleTimeString();
                shiftStartMessage.innerText = `Shift started at ${currentTime}`;
                shiftStartDialog.showModal();
            });

            closeShiftStartDialogBtn.addEventListener("click", () => {
                shiftStartDialog.close();
            });

            shiftEndBtn.addEventListener("click", () => {
                const currentTime = new Date().toLocaleTimeString();
                shiftEndMessage.innerText = `Shift ended at ${currentTime}`;
                shiftEndDialog.showModal();
            });

            closeShiftEndDialogBtn.addEventListener("click", () => {
                shiftEndDialog.close();
            });
        </script>
    </body>
    </html>

<?php
} else {
    header("Location: index.php");
    exit();
}
?>
