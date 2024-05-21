<?php
    session_start();

    include("php/config.php");

    if (!isset($_SESSION['valid'])) {
      header("Location: index.php");
      exit();
    }

    $stmt = $pdo->prepare("
      SELECT n.task_id, t.task_name, u.Username AS sender_username, c.cat_name
      FROM notifications n
      INNER JOIN users u ON n.sender = u.Id
      INNER JOIN tasks t ON n.task_id = t.task_id
      INNER JOIN collab co ON n.task_id = co.task_id
      INNER JOIN category c ON co.cat_id = c.id
      WHERE n.receiver = ?
    ");
    
    $stmt->execute([$_SESSION['id']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_GET['accept']) && !empty($_GET['accept'])) {
      $taskId = $_GET['accept'];

      $stmt = $pdo->prepare("
        SELECT id
        FROM category
        WHERE cat_name = ? AND user_id = ?
      ");
      $stmt->execute([$_GET['cat'], $_SESSION['id']]);

      $categoryId = $stmt->fetchColumn(); 

      if (!$categoryId) {
        $stmt = $pdo->prepare("INSERT INTO category (cat_name, user_id) VALUES (?, ?)");
        $stmt->execute([$_GET['cat'], $_SESSION['id']]);
        $categoryId = $pdo->lastInsertId(); 
      }

       $stmt = $pdo->prepare("
        INSERT INTO collab (user_id, task_id, cat_id) VALUES (?, ?, ?)
      ");
      $stmt->execute([$_SESSION['id'], $taskId, $categoryId]);

      $stmt = $pdo->prepare("DELETE FROM notifications WHERE receiver = ? AND task_id = ?");
      $stmt->execute([$_SESSION['id'], $taskId]);

      header("Location: home.php");
      exit();
    }

    if (isset($_GET['deny']) && !empty($_GET['deny'])) {
      $taskId = $_GET['deny'];

      $stmt = $pdo->prepare("DELETE FROM notifications WHERE receiver = ? AND task_id = ?");
      $stmt->execute([$_SESSION['id'], $taskId]);

        header("Location: home.php");
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <style>
        body {
            font-family: 'Fira Sans', sans-serif;
        }
        .bold-text {
            font-weight: 600;
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
    <title>Notifications</title>
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
                    <i class="bx bxs-bell bx-tada-hover bx-md" style="padding: 0 1rem 0 1rem">
                        <?php if (count($notifications) > 0) echo "<span style='font-size: 13px; position: absolute; background-color: red; color:white; border-radius: 0.5rem; padding: 2px'></span>";?>
                    </i>
                </div>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>

    <div class="container">
        <div class="box form-box">
            <header>Notifications <span class="material-symbols-outlined tooltip" style="float: right; user-select: none;">notifications</span></header>
            
            <div class="notifications">
                <?php
                	if (!empty($notifications)){
                    foreach ($notifications as $notif) {
                        echo "<div class='notification-item'>";
                        $user = ucfirst($notif['sender_username']);
                        echo "<b>{$user}</b> is inviting you to collaborate on <b>{$notif['task_name']}</b>.";
                        echo "<span style='float:right;'>";

                        echo "<a href='?accept=".$notif['task_id']."&cat=".$notif['cat_name']."'><span class='material-symbols-outlined' style=' user-select:none; color:#0A6847; font-size: 20px;'>check_circle</span></a>";

                        echo "<a href='?deny=".$notif['task_id']."'><span class='material-symbols-outlined' style=' user-select:none; color:#FF204E; font-size: 20px;'>cancel</span></a>";
                        
                        echo "</span>";
                        echo "</div>";
                    }
                }else{
                	echo "<br><center><span class='material-symbols-outlined' style='font-size:40px; color:grey; user-select:none'>notifications_active</span><br><span style='color:grey; user-select:none'><b><p style='font-size:14px'>No notifications yet</p></b><p>When you get notifications, they'll show up here</p></span><button onClick='window.location.reload();' class='btn' style='background-color:#E8F2FF; color:#0A79E7; font-weight:600'>Refresh</button></center>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
