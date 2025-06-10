<?php
require_once 'credential.php';
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = $_POST['userName'] ?? '';
    $inputPassword = $_POST['password'] ?? '';

    if ($inputUsername === $credentials['userName'] && $inputPassword === $credentials['password']) {
        $_SESSION['username'] = $inputUsername;  
        $_SESSION['login_time'] = date("Y-m-d H:i:s");
        header('Location: index.php');
        exit();
    } else {
        $error = 'Неправильне ім’я користувача або пароль.';
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Вхід</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 200px auto ;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-label {
            font-weight: bold;
        }
        .btn-login {
            width: 100%;
        }
        footer { 
            background: #333; 
            color: white; 
            padding: 10px; 
            text-align: center; 
        }
        footer a { 
            color: white; 
            margin: 0 15px; 
            text-decoration: none; 
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #333;
            color: white;
            text-align: center;
            padding: 15px;
        }
    </style>
</head>
<body>
    

    <div class="login-container">
        <h2 class="text-center mb-4">Авторизація</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="userName" class="form-label">Ім'я користувача:</label>
                <input type="text" class="form-control" id="userName" name="userName" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-login">Увійти</button>
        </form>
    </div>
    <?php include 'footer.phtml'; ?>
</body>
</html>
