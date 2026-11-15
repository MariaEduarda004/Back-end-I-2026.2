<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

define('STORAGE', dirname(__DIR__) . '/storage/');

require_once dirname(__DIR__) . '/funcoes/geral.php';
require_once dirname(__DIR__) . '/funcoes/arquivo.php';
require_once dirname(__DIR__) . '/funcoes/auth.php';
