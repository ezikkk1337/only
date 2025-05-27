<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добро пожаловать</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Добро пожаловать</h1>
        <p>
            <a href="register.php" class="btn btn-primary">Зарегистрироваться</a>
            <a href="login.php" class="btn btn-secondary">Войти</a>
        </p>
    </div>
</body>
</html>