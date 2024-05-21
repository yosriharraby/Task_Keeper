<?php
    session_start();

    include("php/config.php");
    if(!isset($_SESSION['valid'])){
        header("Location: index.php");
        exit(); 
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
    <title>Create Category</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="home.php"><span style="color:#526D82; font-family: Pacifico">T</span>ask <span style="color:#526D82; font-family: Pacifico">K</span>eeper</a></p>
        </div>

        <div class="right-links">
            <?php if ($_SESSION['admin'] != 0) echo "<a href='admin.php' style='text-decoration: none; color:black;'>Admin Panel</a>";?>
            <a href="edit.php" style="text-decoration: none; color:black">Settings</a>
            <a href='notifications.php' style="color: black"><i class="bx bxs-bell bx-tada-hover bx-md" style="padding: 0 1rem 0 1rem">
                        <?php if (count($notifications) > 0) echo "<span style='font-size: 13px; position: absolute; background-color: red; color:white; border-radius: 0.5rem; padding: 2px'></span>";?>
                    </i></a>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>

    <div class="container">
        <div class="box form-box">
            <header>Create a Category</header>
            <form action="" method="post">
                
                <div class="field input">
                    <label for="category">Category Name<span style="color:red; font-weight: bold;">*</span></label>
                    <input type="text" name="category" id="category" value="" autocomplete="off" required>
                </div>
                
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Create" required>
                </div>
            </form>
            <?php 
    if(isset($_POST['submit'])){
        $name = ucfirst($_POST['category']);
        $id = $_SESSION['id'];

        $check_query = $pdo->prepare("SELECT cat_name FROM category WHERE cat_name = ? AND user_id = ?");
        $check_query->execute([$name,$id]);
        $category_exists = $check_query->rowCount();

        if ($category_exists == 0) {
            $edit_query = $pdo->prepare("INSERT INTO category (cat_name, user_id) VALUES (?, ?)");
            $edit_query->execute([$name, $id]);

            if($edit_query){
                header("Location: ./home.php");
                exit();
            } else {
                echo "Error occurred while inserting task.";
            }
        } else {
            echo "<p class='bold-text' style='font-size: 15px; color:#31363F; text-align:center'>Category already exists.</p>";
        }
    }
?>
        </div>

    </div>
</body>
</html>
