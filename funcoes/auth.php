<?php

function usuario(): ?array {
    return $_SESSION['usuario'] ?? null;
}

function exigir_login(): void {
    if (!usuario()) {
        redirect('login.php');
    }
}

function login(string $email, string $senha): bool {
    $usuarios = ler_json('usuarios.json');

    foreach ($usuarios as $usuario) {
        $emailCorreto = strtolower($usuario['email']) === strtolower($email);
        $senhaCorreta = password_verify($senha, $usuario['senha']);

        if ($emailCorreto && $senhaCorreta) {
            session_regenerate_id(true);
            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'nome' => $usuario['nome'],
            ];

            return true;
        }
    }

    return false;
}
