<?php
session_start();
require_once 'products_data.php';
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Товари</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.phtml'; ?>
    <h2>Список товарів</h2>
    <a href="cart.php">Перейти до кошика</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Назва</th>
                <th>Опис</th>
                <th>Ціна</th>
                <th>Дія</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= $product['title'] ?></td>
                    <td><?= $product['description'] ?></td>
                    <td>$<?= $product['price'] ?></td>
                    <td>
                        <form method="POST" action="basket.php">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="number" name="quantity" value="1" min="1" style="width: 50px;">
                            <button type="submit">Додати</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
   <?php include 'footer.phtml'; ?>
</body>
</html>

