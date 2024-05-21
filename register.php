<?php
session_start();
if (isset($_SESSION['valid'])){
    header("Location: ./home.php");
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" href="icon.png" type="image/x-icon">

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <style>
        body {
            font-family: 'Fira Sans', sans-serif;
        }
        .bold-text {
            font-weight: 600;
        }
    </style>
    <title>Register</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <?php 
            include("php/config.php");

            if(isset($_POST['submit'])){
                $username = $_POST['username'];
                $email = $_POST['email'];
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

                $verifyQuery = $pdo->prepare("SELECT Email FROM users WHERE Email=?");
                $verifyQuery->execute([$email]);
                $verifyResult = $verifyQuery->fetchColumn();

                $verifyQuery1 = $pdo->prepare("SELECT Username FROM users WHERE Username=?");
                $verifyQuery1->execute([$username]);
                $verifyResult1 = $verifyQuery1->fetchColumn();

                if($verifyResult1){
                    echo "<div class='message'>
                        <p>This username is taken, Try another one please!</p>
                    </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
                } elseif($verifyResult) {
                    echo "<div class='message'>
                        <p>This Email is used, Try another one please!</p>
                    </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
                } else {
                    $insertQuery = $pdo->prepare("INSERT INTO users(Username,Email,Password, Admin) VALUES(?,?,?,?)");
                    $insertQuery->execute([$username, $email, $password, 0]);

                    echo "<div class='message success'>
                        <p>Registration successfully!</p>
                    </div> <br>";
                    echo "<a href='index.php'><button class='btn'>Login Now</button>";
                }

            } else {
            ?>

            <header>Sign Up</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Register" required>
                </div>
                <div class="links">
                    Already a member? <a href="index.php" style="text-decoration: none; color: #526D82;" class="bold-text">Sign In</a>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>
</body>
</html>
