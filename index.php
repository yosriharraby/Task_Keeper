<?php 
    session_start();
    if(isset($_SESSION['valid'])){
        header("Location: home.php");
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Fira Sans', sans-serif;
        }
        .bold-text {
            font-weight: 600;
        }
    </style>
    <title>Login</title>
</head>
<body>

      <div class="container">
<div class="logo">
            <p style="text-align: center; font-size: 70px"><span style="color:#526D82; font-family: Pacifico">T</span>ask <span style="color:#526D82; font-family: Pacifico">K</span>eeper</p>
        </div>
        <div class="box form-box">
            <?php 
                include("php/config.php");
                if(isset($_POST['submit'])){
                    $username = $_POST['username'];
                    $password = $_POST['password'];

                    $query = $pdo->prepare("SELECT * FROM users WHERE Username=?");
                    $query->execute([$username]);

                    $arr = $query->fetch(PDO::FETCH_ASSOC);

                    if($arr && password_verify($password, $arr['Password'])){
                        $_SESSION['valid'] = $arr['Username'];
                        $_SESSION['email'] = $arr['Email'];
                        $_SESSION['id'] = $arr['Id'];
                        $_SESSION['admin'] = $arr['Admin'];
                        header("Location: home.php");

                    }else{
                        echo "<div class='message bold-text'>
                          <p>Wrong Username or Password</p>
                           </div> <br>";
                       echo "<a href='index.php'><button class='btn'>Go Back</button></a>";
                    }


              }else{

            
            ?>
            <header>Login</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field">
                    
                    <input type="submit" class="btn" name="submit" value="Login" required>
                </div>
                <div class="links">
                    Don't have account? <a href="register.php" style="text-decoration: none; color: #526D82;" class="bold-text">Sign Up Now</a>
                </div>
            </form>
        </div>
        <?php } ?>
      </div>
</body>
</html>