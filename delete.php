<?php

	include("php/config.php");

	session_start();
	if (!isset($_SESSION['valid']) || !isset($_GET['category'])){
		header('Location: index.php');
		exit();
	}

	$query = $pdo->prepare("DELETE FROM category WHERE id = ? AND user_id = ?");
	$query->execute([$_GET['category'],$_SESSION['id']]);

	$req = $pdo->prepare("DELETE FROM tasks WHERE task_id NOT IN (SELECT task_id FROM collab)");
	$req->execute();

	header('Location: home.php');
?>