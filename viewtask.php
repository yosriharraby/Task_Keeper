<?php 
   session_start();

   include("php/config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: index.php");
   }

    if(isset($_POST['submit'])){
        $q = $pdo->prepare("DELETE FROM collab WHERE task_id = ? AND user_id = ?");
        $q->execute([$_GET['id'], $_SESSION['id']]);
        
        if ($q->rowCount() > 0){
        $q1 = $pdo->prepare("DELETE FROM tasks where task_id = ?");
        $q1->execute([$_GET['id']]);            
        }

        header("Location: home.php");
        exit();
    }

    if (isset($_GET['id'])){
        $query = $pdo->prepare("SELECT t.datetime, t.task_name, t.task_msg, u.Username, c.cat_name, l.type, l.description 
                        FROM tasks t
                        JOIN collab co ON co.task_id = t.task_id
                        JOIN users u ON co.user_id = u.Id
                        JOIN category c ON co.cat_id = c.id
                        JOIN labels l ON l.type = t.priority_id
                        WHERE co.task_id = ? 
                          AND co.user_id = ?");
        $query->execute([$_GET['id'], $_SESSION['id']]);


        while($arr = $query->fetch(PDO::FETCH_ASSOC)){
            $name = $arr['task_name'];
            $datetime = $arr['datetime']; 
            $msg = $arr['task_msg']; 
            $user = $arr['Username']; 
            $cat = $arr['cat_name']; 
            $prio_id =  $arr['type'];
            $priority = $arr['description']; 
        }
    } 

    $req = $pdo->prepare("SELECT Username FROM users u, collab c WHERE u.Id = c.user_id AND task_id = ? AND user_id != ?");
    $req->execute([$_GET['id'], $_SESSION['id']]);
    $arr = $req->fetch();

    $req2 = $pdo->prepare("SELECT Username FROM users u, collab c WHERE u.Id = c.user_id AND task_id = ? ORDER BY cat_id ASC");
    
    $reqq = $pdo->prepare("SELECT * FROM notifications WHERE receiver = ?");
    $reqq->execute([$_SESSION['id']]);
    $notifications = $reqq->fetchAll(PDO::FETCH_ASSOC);

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

    </style>
    <title>View Task</title>
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
            <header>Task Information <span class="material-symbols-outlined tooltip" style="float: right; user-select: none;">info</span></header>
            <form action="" method="post">
                <table border="0" style="font-size: 14px; text-align: center; width: 100%">
                    <tr>
                        <th>Created by</th>
                        <th>Creation Date</th>
                        <th>Category</th>
                        
                    </tr>
                    <tr>

                        <td><?php if (isset($user)) echo ucfirst($user); ?></td>
                        <td ><?php if (isset($datetime)) echo $datetime;?></td>
                        <td><?php if (isset($cat)) echo $cat; ?></td>
                        
                    </tr>
                    <tr>
                        <td colspan="3"><br></td>
                        
                    </tr>
                    <tr>
                        <th>
                            Task Name
                        </th>
                        <th>
                           Priority 
                        </th>
                        <th>
                           Collaborators 
                        </th>
                    </tr>
                    <?php 
                        if ($prio_id==0){
                            $color = "#fff600"; 
                        }

                          elseif ($prio_id==1) {
                            $color = "#ffc302";
                        }

                           elseif ($prio_id==2) {
                            $color = "#ff8f00";
                        }

                           else{
                            $color = "#ff0505";
                           }
                    ?>
                    <tr>
                        <td ><?php if (isset($name)) echo $name; ?></td>
                        <td><?php if (isset($priority)) echo "<span class='material-symbols-outlined' style=' font-size: 14px; user-select:none; text-align:center; color:".$color."'>fiber_manual_record</span> ".$priority; ?></td>
                        <td><?php if (isset($arr['Username'])) echo ucfirst($arr['Username']); else echo "--"; ?></td>
                    </tr>
                    <tr>
                        <td colspan="3"><br></td>
                    </tr>
                    <tr>
                        <th colspan="3">Description</th>
                    </tr>
                    <tr>
                        <td colspan="3" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap;"><?php if (isset($msg)) echo $msg; ?></td>
                    </tr>
                    </table>
                    <form action="" method="POST">
                        <center><input type="submit" name="submit" style="background-color: #FF204E; margin-top:3rem;" class="btn" value="Terminate Task"></center>
                    </form>
        </div>
      </div>
</body>
</html>