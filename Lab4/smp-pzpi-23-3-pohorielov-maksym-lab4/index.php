<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Location: main.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'products_data.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $hasSelected = false;

    if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $product_id => $quantity) {
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;

            if ($product_id > 0 && $quantity > 0 && isset($products[$product_id])) {
                $hasSelected = true;

                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$product_id] = [
                        'name' => $products[$product_id]['name'],
                        'price' => $products[$product_id]['price'],
                        'quantity' => $quantity
                    ];
                }
            }
        }
    }

    if ($hasSelected) {
        header('Location: cart.php');
        exit;
    } else {
        $error = "Оберіть хоча б один товар із кількістю більше нуля.";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Інтернет-магазин</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .custom-submit-button {
            display: block;
            margin-left: 1200px;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <main>
        <h2>Список товарів</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="index.php" id="product-form" class="centered">
            <table>
                <thead>
                    <tr>
                        <th>Назва</th>
                        <th>Кількість</th>
                        <th>Ціна</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td>
                                <input type="number" name="quantities[<?= $product['id'] ?>]" value="0" min="0">
                            </td>
                            <td>$<?= number_format($product['price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
        <button type="button" class="custom-submit-button" onclick="document.getElementById('product-form').submit();">Додати до кошика</button>
    </main>
    <?php include 'footer.phtml'; ?>
</body>
</html>
