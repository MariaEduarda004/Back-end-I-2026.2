<?php
require dirname(__DIR__) . '/config/app.php';

if (usuario()) {
    redirect('index.php');
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!valid_csrf()) {
        http_response_code(400);
        $erro = 'Requisição inválida. Atualize a página e tente novamente.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $senha = (string) ($_POST['senha'] ?? '');

        if (login($email, $senha)) {
            redirect('index.php');
        }

        http_response_code(401);
        $erro = 'E-mail ou senha incorretos.';
    }
}

$tituloPagina = 'Entrar';
require dirname(__DIR__) . '/views/header.php';
?>
<div class="auth">
    <section class="authintro">
        <span class="eyebrow">BEM-VINDO DE VOLTA</span>
        <h1>Seus estudos, no seu ritmo.</h1>
        <p>Um espaço para suas prioridades, matérias e tarefas.</p>
    </section>

    <section class="panel">
        <h2>Acessar conta</h2>
        <p class="muted">Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a>.</p>

        <?php if ($erro !== ''): ?>
            <div class="error"><?= e($erro) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">

            <label>
                E-mail
                <input type="email" name="email" required>
            </label>

            <label>
                Senha
                <input type="password" name="senha" required>
            </label>

            <button class="btn full">Entrar no painel</button>
        </form>
    </section>
</div>
<?php require dirname(__DIR__) . '/views/footer.php'; ?>