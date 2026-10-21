<?php

require __DIR__ . '/../src/bootstrap.php';

if (current_user()) {
    redirect('/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $name = '';

    if (isset($_POST['usuario'])) {
        $name = (string) $_POST['usuario'];
    }

    $name = strtolower(trim($name));
    $password = '';

    if (isset($_POST['senha'])) {
        $password = (string) $_POST['senha'];
    }

    if (!preg_match('/^[a-z0-9_]{3,24}$/', $name) || strlen($password) < 8) {
        $error = 'Use nome de 3 a 24 caracteres (a-z, 0-9, _) e senha com ao menos 8 caracteres.';
    } else {
        $created = false;

        update_json('usuarios', function (array $users) use ($name, $password, &$created): array {
            foreach ($users as $user) {
                $existingName = '';

                if (isset($user['usuario'])) {
                    $existingName = (string) $user['usuario'];
                }

                if ($existingName === $name) {
                    return $users;
                }
            }

            $users[] = [
                'usuario' => $name,
                'senha_hash' => password_hash($password, PASSWORD_DEFAULT),
            ];
            $created = true;

            return $users;
        });

        if ($created) {
            redirect('/login.php?cadastro=ok');
        }

        $error = 'Usuário já cadastrado.';
    }
}

page_start('Cadastro');
?>

<h2>Criar conta</h2>

<?php if ($error): ?>
    <p class="erro"><?= e($error) ?></p>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">

    <label>
        Usuário
        <input name="usuario" autocomplete="username" required>
    </label>

    <label>
        Senha
        <input
            name="senha"
            type="password"
            autocomplete="new-password"
            minlength="8"
            required
        >
    </label>

    <button type="submit">Cadastrar</button>
</form>

<p><a href="/login.php">Já tenho conta</a></p>

<?php page_end(); ?>
