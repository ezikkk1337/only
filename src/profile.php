<?php
session_start();

$host = '172.18.0.1';
$username = 'root';
$password = 'admin';
$database = 'only';
$port = 3306;

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die('Ошибка подключения: ' . $e->getMessage());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';
$user = [
    'name' => $_SESSION['user_name'],
    'phone' => $_SESSION['user_phone'],
    'email' => $_SESSION['user_email']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE (phone = ? OR email = ?) AND id != ?");
    $stmt->execute([$phone, $email, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        $error = 'Телефон или почта уже используются';
    } else {
        $update_fields = ['name' => $name, 'phone' => $phone, 'email' => $email];
        $query = "UPDATE users SET name = ?, phone = ?, email = ?";
        $params = [$name, $phone, $email];

        if (!empty($password)) {
            $update_fields['password'] = password_hash($password, PASSWORD_DEFAULT);
            $query .= ", password = ?";
            $params[] = $update_fields['password'];
        }

        $query .= " WHERE id = ?";
        $params[] = $_SESSION['user_id'];

        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $_SESSION['user_name'] = $name;
            $_SESSION['user_phone'] = $phone;
            $_SESSION['user_email'] = $email;
            $success = 'Профиль обновлен';
            $user = ['name' => $name, 'phone' => $phone, 'email' => $email];
        } catch (PDOException $e) {
            $error = 'Ошибка обновления: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Профиль</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <input type="password" name="password" placeholder="Новый пароль (оставьте пустым, чтобы не менять)">
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
        <a href="index.php" class="btn btn-secondary">Выйти</a>
    </div>
</body>
</html>