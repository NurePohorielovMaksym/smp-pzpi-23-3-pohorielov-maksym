<?php
session_start();

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

include 'header.phtml';

$protected_pages = ['cart', 'profile', 'products'];

if (in_array($page, $protected_pages) && !isset($_SESSION['username'])) {
    echo "<div style='text-align: center; padding: 20px; color: red; font-weight: bold;'>Потрібно авторизуватись для доступу до цієї сторінки.</div>";
    include 'footer.phtml';
    exit;
}

if ($page === 'home' && !isset($_SESSION['username'])) {
    echo "<div style='text-align: center; padding: 50px; font-size: 24px; font-weight: bold;'>Please login</div>";
    include 'footer.phtml';
    exit;
}

switch ($page) {
    case 'home':
        require_once("index.php");
        break;
    case 'products':
        require_once("index.php");
        break;
    case 'login':
        require_once("login.php");
        break;
    case 'logout':
        require_once("logout.php");
        break;
    case 'cart':
        require_once("cart.php");
        break;
    case 'profile':
        require_once("profile.php");
        break;
    default:
        require_once("page404.php");
        break;
}

include 'footer.phtml';
?>
