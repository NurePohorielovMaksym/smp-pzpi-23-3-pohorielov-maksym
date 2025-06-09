<?php
session_start();
require_once 'products_data.php';
require_once 'functions.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']);

    if (isset($products[$product_id])) {
        $product = $products[$product_id];
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product['title'],
                'price' => (float)$product['price'],
                'quantity' => $quantity
            ];
        }
    } else {
        error_log("Attempted to add invalid product ID: $product_id");
    }
    redirectTo('cart.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'decrease' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    if (decreaseCartItem($product_id)) {
        error_log("Decreased quantity for product ID: $product_id");
        redirectTo('cart.php');
    }
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST data received: " . print_r($_POST, true));
    if (isset($_POST['cancel'])) {
        if (clearCart()) {
            redirectTo('cart.php');
        }
    } elseif (isset($_POST['pay'])) {
        if (clearCart()) {
            displayPaymentSuccess('index.php');
        }
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Кошик</title>
    <link rel="stylesheet" href="style.css">
    <style>
       
        .cart-container {
            max-width: 800px;
            margin: 0 auto; 
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        button[name="decrease_item"] {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 18px;
        }
        .buttons {
            margin-top: 20px;
            text-align: center;
        }
        .buttons form {
            display: inline-block;
            margin: 0 10px;
        }
        .buttons button {
            padding: 5px 15px;
        }
    </style>
</head>
<body>
    <?php include 'header.phtml'; ?>
    <main>
        <h2>Кошик</h2>
        <?php
        if (isset($_SESSION['cart'])) {
            error_log("Cart data: " . print_r($_SESSION['cart'], true));
        }
        ?>
        <?php if (empty($_SESSION['cart'])): ?>
            <p style="text-align: center;">Кошик порожній.</p>
        <?php else: ?>
            <div class="cart-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Назва</th>
                            <th>Ціна</th>
                            <th>Кількість</th>
                            <th>Сума</th>
                            <th>Дія</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($_SESSION['cart'] as $id => $item):
                            if (!is_numeric($id) || !isset($products[(int)$id])) {
                                error_log("Invalid or missing product ID: $id in cart");
                                unset($_SESSION['cart'][$id]);
                                continue;
                            }

                            $product_id = (int)$id;
                            $name = isset($item['name']) ? $item['name'] : ($products[$product_id]['title'] ?? 'Unknown');
                            $price = isset($item['price']) ? (float)$item['price'] : ($products[$product_id]['price'] ?? 0.00);
                            $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;

                            $sum = $price * $quantity;
                            $total += $sum;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars((string)$product_id) ?></td>
                            <td><?= htmlspecialchars($name) ?></td>
                            <td>$<?= number_format($price, 2) ?></td>
                            <td><?= $quantity ?></td>
                            <td>$<?= number_format($sum, 2) ?></td>
                            <td>
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="action" value="decrease">
                                    <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                    <button type="submit" name="decrease_item">🗑️</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td><strong>Загальна сума</strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><strong>$<?= number_format($total, 2) ?></strong></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <div class="buttons">
                    <form method="POST" action="cart.php">
                        <button type="submit" name="cancel">Скасувати</button>
                    </form>
                    <form method="POST" action="cart.php">
                        <button type="submit" name="pay">Оплатити</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </main>
    <?php include 'footer.phtml'; ?>
</body>
</html>
