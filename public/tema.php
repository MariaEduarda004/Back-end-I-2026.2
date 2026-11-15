<?php
require dirname(__DIR__) . '/config/app.php';
exigir_login();

$novoTema = tema() === 'claro' ? 'escuro' : 'claro';

setcookie('tema', $novoTema, [
    'expires' => time() + (30 * 24 * 60 * 60),
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);

redirect('index.php');
