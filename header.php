<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title><? echo $_SERVER['SERVER_NAME']; ?></title>
	<link rel="stylesheet" href="../css/style.css">
</head>
<body>
	<div class="container">
		
		<header class="header">
			<h1><? echo $_SERVER['SERVER_NAME']; ?></h1>
			<nav class="nav">
				<ul id="service-links">
					<li><a href="http://tv-grid<? echo ($_SERVER['SCRIPT_NAME'] == '/index.php') ? '/list.php' : ''; ?>">Сменить режим</a></li>
					
					<? if(isset($_SESSION['user'])): ?>
					<li><a href="/settings.php">Настройки</a></li>
					<? endif; ?>
				</ul>
				<ul id="user-links">
					<? if(isset($_SESSION['user'])): ?>
					<li style="color: gray; font-style: italic;">Привет, <? echo explode('@', $_SESSION['user'][1])[0]; ?>!</li>
					<li><a href="/logout.php">Выйти</a></li>
					<? else: ?>
					<li><a href="/login.php">Войти</a></li>
					<li><a href="/signup.php">Регистрация</a></li>
					<? endif; ?>
				</ul>
			</nav>
		</header>