<?php

declare(strict_types=1);

/*
 * Este arquivo é carregado por todas as páginas.
 * Aqui ficam a configuração da sessão e as funções compartilhadas.
 */

ini_set('session.use_strict_mode', '1');

$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);

session_start();

const STORAGE = __DIR__ . '/../storage/';

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function redirect(string $page): never
{
    header('Location: ' . $page);
    exit;
}

function csrf(): string
{
    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function check_csrf(): void
{
    $token = '';

    if (isset($_POST['csrf'])) {
        $token = (string) $_POST['csrf'];
    }

    if (!hash_equals(csrf(), $token)) {
        http_response_code(403);
        exit('Requisição inválida. Atualize a página e tente novamente.');
    }
}

function read_json(string $name): array
{
    $file = STORAGE . $name . '.json';

    if (!is_file($file)) {
        return [];
    }

    $data = json_decode((string) file_get_contents($file), true);

    if (is_array($data)) {
        return $data;
    }

    return [];
}

function update_json(string $name, callable $update): void
{
    $file = STORAGE . $name . '.json';
    $handle = fopen($file, 'c+');

    if ($handle === false) {
        throw new RuntimeException('Não foi possível abrir o armazenamento.');
    }

    try {
        if (!flock($handle, LOCK_EX)) {
            throw new RuntimeException('Falha ao bloquear o arquivo.');
        }

        rewind($handle);
        $contents = stream_get_contents($handle);

        if ($contents === false || $contents === '') {
            $contents = '[]';
        }

        $data = json_decode($contents, true);

        if (!is_array($data)) {
            $data = [];
        }

        $updatedData = $update($data);

        $json = json_encode(
            $updatedData,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );

        rewind($handle);
        ftruncate($handle, 0);
        fwrite($handle, $json);
        fflush($handle);
        flock($handle, LOCK_UN);
    } finally {
        fclose($handle);
    }
}

function current_user(): ?string
{
    if (isset($_SESSION['user'])) {
        return (string) $_SESSION['user'];
    }

    return null;
}

function require_login(): string
{
    if (!current_user()) {
        redirect('/login.php');
    }

    return (string) current_user();
}

function page_start(string $title): void
{
    $theme = 'claro';

    if (isset($_COOKIE['tema']) && $_COOKIE['tema'] === 'escuro') {
        $theme = 'escuro';
    }

    echo '<!doctype html>';
    echo '<html lang="pt-BR" data-tema="' . e($theme) . '">';
    echo '<head>';
    echo '<meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . e($title) . ' · Agenda de Estudos</title>';
    echo '<link rel="stylesheet" href="/estilo.css">';
    echo '</head>';
    echo '<body>';
    echo '<main>';
    echo '<header>';
    echo '<h1>Agenda de Estudos</h1>';
    echo '<p>Projeto de revisão · Desenvolvimento Back-end I</p>';
    echo '</header>';
}

function page_end(): void
{
    echo '</main>';
    echo '</body>';
    echo '</html>';
}
