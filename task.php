<?php 
     session_start();
     ob_start();

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
    <title>Create Task</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="home.php"><span style="color:#526D82; font-family: Pacifico">T</span>ask <span style="color:#526D82; font-family: Pacifico">K</span>eeper</a></p>
        </div>

        <div class="right-links" style="display: flex; flex-direction: row; ">
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
            <header>Create a Task</header>
            <form action="" method="post">

                <div class="field input">
                    <label for="task">Task Name<span style="color:red; font-weight: bold;">*</span></label>
                    <input type="text" name="task_name" id="task" autocomplete="off" maxlength="20" required>
                </div>

                <div class="field input">
                    <label for="task">Task Description<span style="color:red; font-weight: bold;">*</span></label>
                    <textarea name="task_desc" id="task" rows="3" required></textarea>
                </div>

                <table border="0" width="100%" style="font-size: 13px">
                    <tr>
                        <td width="50%"><label>Labels<span style="color:red; font-weight: bold;">*</span></label></td>
                        <td style="padding-left: 10px;">Collaborators</td>
                    </tr>
                    <tr>
                        <td width="50%">
                            <select name="labels" required>
                                <option value="0" selected>Low Priority</option>
                                <option value="1">Normal Priority </option>
                                <option value="2">High Priority</option>
                                <option value="3">Urgent Priority</option>
                            </select>
                        </td>

                        <td style="padding-left: 10px;">
                            <select name="collab">
                                <option value="" selected>--</option>
                                <?php
                                    $req = $pdo->prepare("SELECT * FROM users WHERE Username != ?");
                                    $req->execute([$_SESSION['valid']]);
                                    while ($arr = $req->fetch()){
                                        echo "<option value='".$arr['Id']."'>".$arr['Username']."</option>";
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                </table>
               
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Create Task" required>
                </div>
            </form>

            <?php 
    if(isset($_POST['submit'])){

        $task_name = $_POST['task_name'];
        $task_desc = $_POST['task_desc'];
        $category = $_GET['category'];
        $userId = $_SESSION['id'];
        $priority = $_POST['labels'];
        $collab = $_POST['collab'];

        $checkQuery = $pdo->prepare("SELECT id FROM category WHERE id = ? AND user_id = ?");
        $checkQuery->execute([$category,$userId]);
        $categoryExists = $checkQuery->rowCount();

        if ($categoryExists > 0) {
            if (strlen($task_name)<=20){
            $req = $pdo->prepare("INSERT INTO tasks (task_name, task_msg, datetime, priority_id) VALUES (?, ?, NOW(), ?)");
            $req->execute([$task_name, $task_desc, $priority]);
            
            $lastId = $pdo->lastInsertId();

            $req = $pdo->prepare("INSERT INTO collab VALUES (?, ?, ?)");
            $req->execute([$userId, $lastId, $category]);

            if (isset($collab) && !empty($collab)){
                $req2 = $pdo->prepare("INSERT INTO notifications VALUES (?, ?, ?)");
                $req2->execute([$_SESSION['id'], $collab, $lastId]);
            }

            if($req){
                ob_end_clean();
                header("Location: home.php");
                exit();
            } else {
                echo "Error occurred while inserting task.";
            }
        } else {
            echo "<p class='bold-text' style='font-size: 15px; color:red; text-align:center'>Task name must be 20 characters or less.</p>"; 
        }
        } else {
            echo "<p class='bold-text' style='font-size: 15px; color:red; text-align:center'>Category ID does not exist.</p>";
        }
    }
?>
        </div>
      </div>
</body>
</html>