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
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,300,0,0" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Reddit+Mono:wght@200..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,1,0" />
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

  <script>
        function newCategory() {
            window.location.href = "categories.php";
        }
    </script>

  <style>
    body {
      font-family: 'Fira Sans', sans-serif;
    }
    .bold-text {
      font-weight: 700;
    }
    .no-decoration {
      text-decoration: none;
      color: inherit; 
    }
    .notifications {
            font-size: 12px;
        }
        .notification-item {
            padding: 10px;
            border: 1px solid #e0e0e0;
            margin-bottom: 10px;
            border-radius: 5px;
        }

  </style>
  <title>Home</title>
</head>
<body>
  <div class="nav">
        <div class="logo">
            <p><a href="home.php"><span style="color:#526D82; font-family: Pacifico">T</span>ask <span style="color:#526D82; font-family: Pacifico">K</span>eeper</a></p>
        </div>

        <div class="right-links" style="display: flex; flex-direction: row; ">
           <?php if ($_SESSION['admin'] != 0) echo "<a href='admin.php' style='text-decoration: none; color:black;'>Admin Panel</a>";?>
            <a href="edit.php" style="text-decoration: none; color:black">Settings</a>
            <div class="icon">
                    <a href='notifications.php' style="color: black"><i class="bx bxs-bell bx-tada-hover bx-md" style="padding: 0 1rem 0 1rem">
                        <?php if (count($notifications) > 0) echo "<span style='font-size: 13px; position: absolute; background-color: red; color:white; border-radius: 0.5rem; padding: 2px'></span>";?>
                    </i></a>
                </div>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>

  <section class="lists-container">
    <?php
      $id = $_SESSION['id'];
      $query1 = $pdo->prepare("SELECT id, cat_name FROM category  WHERE user_id = ? ");
      $query1->execute([$id]);

      while ($arr1 = $query1->fetch(PDO::FETCH_ASSOC)) {
        $query2 = $pdo->prepare("SELECT t.task_id, task_name, task_msg, priority_id FROM tasks t, collab c WHERE t.task_id = c.task_id AND user_id = ? AND cat_id = ? ORDER BY priority_id DESC");
        $query2->execute([$id, $arr1['id']]);
        
        echo "<div class='list'>
            <h3 class='list-title'>" . $arr1['cat_name'] . " <a href='delete.php?category=". $arr1['id'] ."' style='text-decoration:none; color:black'><span class='material-symbols-outlined' style='float: right; font-size: 16px; user-select: none'>close</span></a></h3>";
        echo "<ul class='list-items'>";

        while ($arr2 = $query2->fetch(PDO::FETCH_ASSOC)) {

          if ($arr2['priority_id']==0){
            $color = "#fff600";
            
          }
          elseif ($arr2['priority_id']==1) {
            $color = "#ffc302";
             
           }
           elseif ($arr2['priority_id']==2) {
            $color = "#ff8f00";
             
           }
           else{
            $color = "#ff0505";
           }

          echo "<a href='viewtask.php?id=". $arr2['task_id'] ."' style='text-decoration:none;'><li>" . $arr2['task_name'] . "<span class='material-symbols-outlined' style='float: right; font-size: 19px;color:".$color."'>fiber_manual_record</span></li></a>";
        }
        
        echo "</ul>";
        echo "<button type='submit' class='add-card-btn btn'><a href='/task.php?category=" . $arr1['id'] . "' class='no-decoration'>Add a task ㅤㅤㅤㅤㅤㅤㅤㅤㅤㅤㅤ</a></button>";
        echo "</div>";
      }
    ?>

    <button class="add-list-btn btn" style="margin-left: 1rem;" onclick="newCategory()">Add a category</button>
  </section>
</body>
</html>
