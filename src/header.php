<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title><?php $_SERVER['SERVER_NAME'] ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1><a href="/"><?php echo $_SERVER['SERVER_NAME']; ?></a></h1>
        <nav class="nav">
            <ul id="service-links">
                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="/settings.php">Настройки</a></li>
                <?php endif; ?>
            </ul>
            <ul id="user-links">
                <?php if (isset($_SESSION['user'])): ?>
                    <li style="color: gray; font-style: italic;">
                        Привет, <?php echo $_SESSION['user'][1]; ?>!
                    </li>
                    <li><a href="/logout.php">Выйти</a></li>
                <?php else: ?>
                    <li><a href="/login.php">Войти</a></li>
                    <li><a href="/signup.php">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>