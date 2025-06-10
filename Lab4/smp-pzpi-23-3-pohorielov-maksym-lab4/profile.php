<?php
session_start();

// Перевірка авторизації
if (!isset($_SESSION['username'])) {
    header('Location: main.php?page=auth_check');
    exit();
}

// Ініціалізація профілю
$profile = [
    'first_name' => '',
    'last_name' => '',
    'birth_date' => '',
    'about' => '',
    'photo' => ''
];

if (file_exists('profile_data.php')) {
    include 'profile_data.php';
}

// Повідомлення про помилку або успіх
$message = '';

// Обробка завантаження фото
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $photo_path = $profile['photo'];
    if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['photo']['type'], $allowed_types)) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $photo_path = $target_dir . basename($_FILES['photo']['name']);
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
                $profile['photo'] = $photo_path;
                file_put_contents('profile_data.php', '<?php $profile = ' . var_export($profile, true) . ';');
                $message = 'Фото успішно завантажено!';
            } else {
                $message = 'Помилка при завантаженні фото.';
            }
        } else {
            $message = 'Дозволені тільки JPG, PNG або GIF зображення.';
        }
    }
}

// Обробка збереження профілю
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['first_name'])) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $birth_date = $_POST['birth_date'] ?? '';
    $about = trim($_POST['about'] ?? '');
    $photo_path = $profile['photo'];

    $errors = [];

    if (empty($first_name) || empty($last_name) || empty($birth_date) || empty($about)) {
        $errors[] = "Усі поля повинні бути заповнені.";
    }

    if (strlen($first_name) < 2 || !is_string($first_name)) {
        $errors[] = "Ім'я повинно містити щонайменше 2 символи.";
    }
    if (strlen($last_name) < 2 || !is_string($last_name)) {
        $errors[] = "Прізвище повинно містити щонайменше 2 символи.";
    }

    $age = (int)((time() - strtotime($birth_date)) / (365.25 * 24 * 60 * 60));
    if ($age < 16 || !$birth_date) {
        $errors[] = "Вам повинно бути не менше 16 років або вкажіть коректну дату.";
    }

    if (strlen($about) < 50) {
        $errors[] = "Стисла інформація повинна містити не менше 50 символів.";
    }

    if (empty($errors)) {
        $profile = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'birth_date' => $birth_date,
            'about' => $about,
            'photo' => $photo_path
        ];

        file_put_contents('profile_data.php', '<?php $profile = ' . var_export($profile, true) . ';');

        $message = 'Профіль успішно оновлено!';
    } else {
        $message = implode('<br>', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Профіль користувача</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding-bottom: 60px;
            background-color: #f5f5f5;
        }
        .profile-container {
            max-width: 1100px;
            margin: 100px auto 20px;
            display: flex;
            gap: 20px;
        }
        .photo-section {
            flex: 1;
            text-align: center;
        }
        .photo-placeholder {
            width: 400px;
            height: 400px;
            border: 2px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        .photo-placeholder img {
            max-width: 100%;
            max-height: 100%;
        }
        .form-section {
            flex: 2;
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group {
            flex: 1;
        }
        .description {
            margin-bottom: 20px;
        }
        .save-btn {
            text-align: right; /* Зміщено праворуч */
        }
        .upload-btn {
            margin-top: 10px;
        }
        header, footer {
            background: #333;
            color: white;
            padding: 10px;
            text-align: center;
        }
        header a, footer a {
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
            color: #fff;
            text-align: center;
            padding: 15px;
        }
        h2 {
            margin-bottom: 30px; /* Доданий відступ нижче заголовка */
        }
    </style>
</head>
<body>
   <header>
        <a href="index.php">Home</a> |
        <a href="index.php">Products</a> |
        <a href="cart.php">Cart</a> |
        <a href="logout.php">Logout</a>
    </header>
    <div class="profile-container">
        <div class="photo-section">
            <div class="photo-placeholder">
                <?php if (!empty($profile['photo']) && file_exists($profile['photo'])): ?>
                    <img src="<?= htmlspecialchars($profile['photo']) ?>" alt="Profile Photo">
                <?php else: ?>
                    <span style="color: #ccc;">✖</span>
                <?php endif; ?>
            </div>
            <form method="POST" enctype="multipart/form-data" style="display: inline;">
                <input type="file" class="form-control" id="photo" name="photo">
                <button type="submit" class="btn btn-primary mt-2 upload-btn">Завантажити</button>
            </form>
        </div>
        <div class="form-section">
            <h2 class="text-center">Профіль користувача</h2>
            <?php if ($message): ?>
                <p class="text-center" style="color: <?= empty($errors) ? 'green' : 'red' ?>;"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">Ім'я:</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($profile['first_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Прізвище:</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($profile['last_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="birth_date">Дата народження:</label>
                        <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?= htmlspecialchars($profile['birth_date']) ?>" required>
                    </div>
                </div>
                <div class="form-group description">
                    <label for="about">Опис користувача:</label>
                    <textarea class="form-control" id="about" name="about" rows="10" required><?= htmlspecialchars($profile['about']) ?></textarea>
                </div>
                <div class="save-btn">
                    <button type="submit" class="btn btn-success">Зберегти</button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'footer.phtml'; ?>
</body>
</html>
