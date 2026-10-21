<?php

require __DIR__ . '/../src/bootstrap.php';

require_login();

$currentTheme = 'claro';

if (isset($_COOKIE['tema'])) {
    $currentTheme = $_COOKIE['tema'];
}

$theme = 'escuro';

if ($currentTheme === 'escuro') {
    $theme = 'claro';
}

$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

setcookie('tema', $theme, [
    'expires' => time() + 30 * 86400,
    'path' => '/',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);

redirect('/');
