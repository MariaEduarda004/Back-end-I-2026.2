<?php
require dirname(__DIR__) . '/config/app.php';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!valid_csrf()) {
        http_response_code(400);
        $erro = 'Requisição inválida. Atualize a página e tente novamente.';
    } else {
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $senha = (string) ($_POST['senha'] ?? '');

        $nomeValido = mb_strlen($nome) >= 2 && mb_strlen($nome) <= 60;
        $emailValido = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        $senhaValida = strlen($senha) >= 8;

        if (!$nomeValido || !$emailValido || !$senhaValida) {
            $erro = 'Informe um nome com 2 a 60 caracteres, um e-mail válido e uma senha com pelo menos 8 caracteres.';
        } else {
            $usuarios = ler_json('usuarios.json');
            $emailJaCadastrado = false;

            foreach ($usuarios as $usuario) {
                if (strcasecmp($usuario['email'], $email) === 0) {
                    $emailJaCadastrado = true;
                    break;
                }
            }

            if ($emailJaCadastrado) {
                http_response_code(409);
                $erro = 'Este e-mail já está cadastrado.';
            } else {
                $usuarios[] = [
                    'id' => bin2hex(random_bytes(8)),
                    'nome' => $nome,
                    'email' => $email,
                    'senha' => password_hash($senha, PASSWORD_DEFAULT),
                ];

                salvar_json('usuarios.json', $usuarios);
                flash('Conta criada! Entre com seus dados.');
                redirect('login.php');
            }
        }
    }
}

$tituloPagina = 'Criar conta';
require dirname(__DIR__) . '/views/header.php';
?>
<div class="auth">
    <section class="authintro">
        <span class="eyebrow">COMECE AGORA</span>
        <h1>Organize seus estudos com clareza.</h1>
        <p>Cadastre suas matérias, acompanhe prazos e avance nas tarefas importantes.</p>
    </section>

    <section class="panel">
        <h2>Criar conta</h2>
        <p class="muted">Já possui conta? <a href="login.php">Faça login</a>.</p>

        <?php if ($erro !== ''): ?>
            <div class="error"><?= e($erro) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">

            <label>
                Nome completo
                <input name="nome" required maxlength="60">
            </label>

            <label>
                E-mail
                <input type="email" name="email" required>
            </label>

            <label>
                Senha
                <input type="password" name="senha" minlength="8" required>
            </label>

            <button class="btn full">Criar minha conta</button>
        </form>
    </section>
</div>
<?php require dirname(__DIR__) . '/views/footer.php'; ?>