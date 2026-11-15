<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($tituloPagina ?? 'Agenda de Estudos') ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="<?= e(tema()) ?>">
    <header class="top">
        <a class="brand" href="index.php">
            <span class="mark">A</span>
            <span>
                Agenda de Estudos
                <small>Organize suas tarefas</small>
            </span>
        </a>

        <nav>
            <?php if (usuario()): ?>
                <a href="index.php">Meu painel</a>
                <a href="tema.php">Alternar tema</a>
                <a href="logout.php">Sair</a>
            <?php else: ?>
                <a href="login.php">Entrar</a>
                <a class="navcta" href="cadastro.php">Criar conta</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="container">
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="notice"><?= e($_SESSION['flash']) ?></div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
