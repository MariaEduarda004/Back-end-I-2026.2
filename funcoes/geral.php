<?php

function e(string $texto): string {
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $url): void {
    header('Location: ' . $url, true, 303);
    exit;
}

function flash(string $mensagem): void {
    $_SESSION['flash'] = $mensagem;
}

function csrf(): string {
    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function valid_csrf(): bool {
    $token = $_POST['csrf'] ?? '';

    return is_string($token) && hash_equals(csrf(), $token);
}

function tema(): string {
    return ($_COOKIE['tema'] ?? '') === 'escuro' ? 'escuro' : 'claro';
}
