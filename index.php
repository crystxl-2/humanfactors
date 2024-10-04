<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css\login.css">
    <title>Login Page</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>ABC Company</header>
        
            <form action="login.php" method="POST">
                <?php if (isset($_GET['error'])) { ?>
                <p class="error"> <?php echo $_GET['error']; ?></p>
                <?php } ?>


                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" value="Login">
                </div>
            </form>

            </div>
        </div>
    </div>
</body>
</html>
