<?php

function clearCart() {
    $_SESSION['cart'] = [];
    return true;
}

function redirectTo($url) {
    header("Location: $url");
    exit();
}

function decreaseCartItem($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = max(0, (int)$_SESSION['cart'][$product_id]['quantity'] - 1);
        if ($_SESSION['cart'][$product_id]['quantity'] === 0) {
            unset($_SESSION['cart'][$product_id]);
        }
        return true;
    }
    error_log("Failed to decrease quantity for product ID: $product_id - not found in cart");
    return false;
}


function displayPaymentSuccess($returnUrl) {
    ?>
    <!DOCTYPE html>
    <html lang="uk">
    <head>
        <meta charset="UTF-8">
        <title>Оплата завершена</title>
        <style>
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background-color: #fff;
                flex-direction: column;
            }
            a {
                color: #007bff;
                text-decoration: none;
                font-size: 18px;
            }
            a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <h2>Оплата успішно завершена!</h2>
        <p><a href="<?= htmlspecialchars($returnUrl) ?>">Перейти до покупок</a></p>
    </body>
    </html>
    <?php
    exit();
}
?>
