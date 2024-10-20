<?php
session_start();

// Checks if the user is logged in
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
                            <input type="submit" class="btn" value="Access Dashboard" required>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </body>
    </html>

<?php
} else {
    header("Location: index.php");
    exit();
}
?>
