<?php 
   session_start();

   include("php/config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: index.php");
   }

   if(isset($_GET['delete'])){
    $query = $pdo->prepare("DELETE FROM users WHERE Id = ?");
    $query->execute([$_SESSION['id']]);
    session_unset();
    session_destroy();
    header("Location: index.php");
   }

    $req = $pdo->prepare("SELECT * FROM notifications WHERE receiver = ?");
    $req->execute([$_SESSION['id']]);
    $notifications = $req->fetchAll(PDO::FETCH_ASSOC);
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
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <style>
        body {
            font-family: 'Fira Sans', sans-serif;
        }
        .bold-text {
            font-weight: 600;
        }
    </style>
    <title>Change Profile</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="home.php"><span style="color:#526D82; font-family: Pacifico">T</span>ask <span style="color:#526D82; font-family: Pacifico">K</span>eeper</a></p>
        </div>

        <div class="right-links">
            <?php if ($_SESSION['admin'] != 0) echo "<a href='admin.php' style='text-decoration: none; color:black;'>Admin Panel</a>";?>
            <a href="#" style="text-decoration: none; color:black">Settings</a>
            <a href='notifications.php' style="color: black"><i class="bx bxs-bell bx-tada-hover bx-md" style="padding: 0 1rem 0 1rem">
                        <?php if (count($notifications) > 0) echo "<span style='font-size: 13px; position: absolute; background-color: red; color:white; border-radius: 0.5rem; padding: 2px'></span>";?>
                    </i></a>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>
    <div class="container">
        <div class="box form-box">
            <?php 
               if(isset($_POST['submit'])){

                    $username = $_POST['username'];
                    $email = $_POST['email'];
                    $password = $_POST['password'];

                    $id = $_SESSION['id'];
                    
                    if(empty($password)) {
                        $edit_query = $pdo->prepare("UPDATE users SET Username=?, Email=? WHERE Id=?");
                        $edit_query->execute([$username, $email, $id]);
                    } else {
                        $edit_query = $pdo->prepare("UPDATE users SET Username=?, Email=?, Password=? WHERE Id=?");
                        $edit_query->execute([$username, $email, $password, $id]);
                    }

                if($edit_query){
                    echo "<div class='message'>
                    <p>Profile Updated!</p>
                    </div> <br>";
                    echo "<a href='home.php'><button class='btn'>Go Home</button>";
                } else {
                    echo "Error occurred while updating profile.";
                }
            } else {

                    $id = $_SESSION['id'];
                    $query = $pdo->prepare("SELECT * FROM users WHERE Id=?");
                    $query->execute([$id]);

                    while($result = $query->fetch(PDO::FETCH_ASSOC)){
                        $res_Uname = $result['Username'];
                        $res_Email = $result['Email'];
                    }   
?>
            <header>Change Profile</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" value="<?php echo $res_Uname; ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" value="<?php echo $res_Email; ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off">
                    <p style="color: grey; user-select: none; font-style: italic;">* Leave blank if no changes.</p>
                </div>
                
                    <input type="submit" class="btn" name="submit" value="Update">
                    <a href='?delete=1'><input type="button" class="btn" name="delete" value="Delete Account" style="float: right; background-color: #FF204E"></a>
                
            </form>
        </div>
        <?php } ?>
      </div>
</body>
</html>