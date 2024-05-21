<?php
session_start();

include("php/config.php");
if ($_SESSION['admin'] < 1) {
    header("Location: home.php");
    exit();
}

if (isset($_GET['grant']) && $_SESSION['admin'] == 2) {
    $userIdToGrant = intval($_GET['grant']);
    if ($userIdToGrant !== $_SESSION['id']) {
        $query = $pdo->prepare("UPDATE users SET Admin = 1 WHERE Id = ?");
        $query->execute([$userIdToGrant]);
    }
}

if (isset($_GET['revoke']) && $_SESSION['admin'] == 2) {
    $userIdToRevoke = intval($_GET['revoke']);
    if ($userIdToRevoke !== $_SESSION['id']) {
        $query = $pdo->prepare("UPDATE users SET Admin = 0 WHERE Id = ?");
        $query->execute([$userIdToRevoke]);
    }
}

// Pagination variables
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$usersPerPage = 4;
$offset = ($page - 1) * $usersPerPage;

$query = $pdo->prepare("SELECT * FROM users LIMIT :offset, :usersPerPage");
$query->bindValue(':offset', $offset, PDO::PARAM_INT);
$query->bindValue(':usersPerPage', $usersPerPage, PDO::PARAM_INT);
$query->execute();

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
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Reddit+Mono:wght@200..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,1,0" />
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <style>
        body {
            font-family: 'Fira Sans', sans-serif;
        }
        .bold-text {
            font-weight: 600;
        }
        th, td {
            border-bottom: 1px solid #ddd;
        }
        th{
            padding-bottom: 8px;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>

    <title>Admin Panel</title>
</head>
<body>
<div class="nav">
    <div class="logo">
        <p><a href="home.php"><span style="color:#526D82; font-family: Pacifico">T</span>ask <span style="color:#526D82; font-family: Pacifico">K</span>eeper</a></p>
    </div>

    <div class="right-links">
        <?php if ($_SESSION['admin'] >= 1) echo "<a href='#' style='text-decoration: none; color:black;'>Admin Panel</a>"; ?>
        <a href="edit.php" style="text-decoration: none; color:black">Settings</a>
                    <a href='notifications.php' style="color: black"><i class="bx bxs-bell bx-tada-hover bx-md" style="padding: 0 1rem 0 1rem">
                        <?php if (count($notifications) > 0) echo "<span style='font-size: 13px; position: absolute; background-color: red; color:white; border-radius: 0.5rem; padding: 2px'></span>";?>
                    </i></a>
        <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
    </div>
</div>
<center>
<?php if (!isset($_GET['show']) || $_GET['show'] != 2) {?>

    <table border="0">
        <tr>
            <td>
    <div class="box form-box" style="margin-top: 5rem; width: 80rem;">
        <header>Accounts</header>
        <table border="0" style="font-size: 13px; text-align: center; border-collapse: collapse;">
            <thead>
            <tr style="margin-bottom: 10px;">
                <th width="5%" style="user-select: none">#</th>
                <th width="20%" style="user-select: none">Username</th>
                <th style="user-select: none">Email</th>
                <th width="10%" style="user-select: none">Admin</th>
                <td></td>
            </tr>
            </thead>
            <tbody>
            <?php
            while ($arr = $query->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td style='padding: 10px;'>" . $arr['Id'] . "</td>";
                echo "<td>" . ucfirst($arr['Username']) . "</td>";
                echo "<td>" . $arr['Email'] . "</td>";
                if ($arr['Admin'] == 0) {
                    echo "<td style='color: red; user-select:none;' class='bold-text'>❌</td>";
                    if ($_SESSION['admin'] == 2 && $arr['Id'] != $_SESSION['id']) {
                        echo "<td width='20%'><a href='?grant=" . $arr['Id'] . "' style='text-decoration:none; color:black;' class='bold-text'>Grant Admin</a></td>";
                    } else {
                        echo "<td></td>";
                    }
                } else {
                    echo "<td style='color: green; user-select:none;' class='bold-text'>✔</td>";
                    if ($_SESSION['admin'] == 2 && $arr['Id'] != $_SESSION['id']) {
                        echo "<td width='20%'><a href='?revoke=" . $arr['Id'] . "' style='text-decoration:none; color:black;' class='bold-text'>Revoke Admin</a></td>";
                    } else {
                        echo "<td></td>";
                    }
                }
                echo "</tr>";
            }
            ?>

            </tbody>
        </table>
        <?php
        $query = $pdo->query("SELECT COUNT(*) FROM users");
        $totalUsers = $query->fetchColumn();
        $totalPages = ceil($totalUsers / $usersPerPage);
        ?>

        <br>
        <div class="pagination" style="text-align: center; font-size: 13px;">
            <?php for ($i = 1; $i <= $totalPages; $i++) {
                echo "<a href='?page=$i' style='text-decoration:none;'>$i</a> ";
            } ?>
        </div>
    </div>
</td>
<td>
        <a href="?show=2"><span class="material-symbols-outlined" style="font-size: 30px; margin-top: 50px; user-select: none">arrow_forward_ios</span></a></td>
</tr>
</table>
<?php } else {?>
<table border="0">
    <tr>
        <td>
        <a href="?show=1"><span class="material-symbols-outlined" style="font-size: 30px; margin-top: 50px; user-select: none">arrow_back_ios</span></a></td>

</td>
        <td>
<div class="box form-box" style="margin-top: 5rem; width: 120rem;">
    <header>Statistics</header>
    <canvas id="tasksChart" width="800" height="400"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Fetch data for the chart
        <?php
        $taskCounts = [];
        $usernames = [];
        $taskQuery = $pdo->query("SELECT u.Username, COUNT(c.user_id) AS task_count FROM users u, collab c WHERE u.Id = c.user_id GROUP BY c.user_id ORDER BY task_count DESC");
        while ($row = $taskQuery->fetch(PDO::FETCH_ASSOC)) {
            $usernames[] = $row['Username'];
            $taskCounts[] = $row['task_count'];
        }
        ?>

        var ctx = document.getElementById('tasksChart').getContext('2d');
        var tasksChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($usernames); ?>,
                datasets: [{
                    label: 'Number of Tasks',
                    data: <?php echo json_encode($taskCounts); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    });
</script>
</td>
</tr></table>
<?php };?>
</center>
<br><br><br><br>
</body>
</html>