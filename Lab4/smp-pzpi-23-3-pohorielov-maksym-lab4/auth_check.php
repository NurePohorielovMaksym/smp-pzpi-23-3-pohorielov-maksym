<?php

if (!isset($_SESSION['username'])) {
    header('Location: main.php?page=page404');
    exit();
}
