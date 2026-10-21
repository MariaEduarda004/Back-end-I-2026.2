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

    foreach (read_json('usuarios') as $user) {
        $storedName = '';

        if (isset($user['usuario'])) {
            $storedName = (string) $user['usuario'];
        }

        $isSameUser = $storedName === $name;
        $passwordHash = '';

        if (isset($user['senha_hash'])) {
            $passwordHash = (string) $user['senha_hash'];
        }

        $hasValidPassword = password_verify(
            $password,
            $passwordHash
        );

        if ($isSameUser && $hasValidPassword) {
            // Após o login, gera um novo ID para evitar o reaproveitamento da sessão anterior.
            //Isso ajuda a evitar o ataque chamado session fixation, no qual alguém poderia tentar fazer o usuário utilizar uma sessão cujo identificador já era conhecido pelo atacante.
            session_regenerate_id(true);
            $_SESSION['user'] = $name;
            redirect('/');
        }
    }

    $error = 'Credenciais inválidas.';
}

page_start('Entrar');
?>

<h2>Entrar</h2>

<?php if (isset($_GET['cadastro'])): ?>
    <p>Cadastro realizado. Faça login.</p>
<?php endif; ?>

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
        <input name="senha" type="password" autocomplete="current-password" required>
    </label>

    <button type="submit">Entrar</button>
</form>

<p><a href="/cadastro.php">Criar uma conta</a></p>

<?php page_end(); ?>