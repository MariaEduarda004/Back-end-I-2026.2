<?php

require __DIR__ . '/../src/bootstrap.php';

$user = require_login();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $action = '';

    if (isset($_POST['acao'])) {
        $action = (string) $_POST['acao'];
    }

    if ($action === 'criar') {
        $title = '';

        if (isset($_POST['titulo'])) {
            $title = (string) $_POST['titulo'];
        }

        $title = trim($title);

        if ($title === '' || mb_strlen($title) > 100) {
            $error = 'Informe um título entre 1 e 100 caracteres.';
        } else {
            update_json('tarefas', function (array $tasks) use ($user, $title): array {
                $tasks[] = [
                    'id' => bin2hex(random_bytes(8)),
                    'usuario' => $user,
                    'titulo' => $title,
                    'concluida' => false,
                ];

                return $tasks;
            });

            redirect('/');
        }
    } elseif ($action === 'concluir') {
        $id = '';

        if (isset($_POST['id'])) {
            $id = (string) $_POST['id'];
        }

        update_json('tarefas', function (array $tasks) use ($user, $id): array {
            foreach ($tasks as &$task) {
                $taskUser = '';

                if (isset($task['usuario'])) {
                    $taskUser = (string) $task['usuario'];
                }

                $taskId = '';

                if (isset($task['id'])) {
                    $taskId = (string) $task['id'];
                }

                $isUserTask = $taskUser === $user;
                $isSelectedTask = $taskId === $id;

                if ($isUserTask && $isSelectedTask) {
                    $task['concluida'] = true;
                }
            }

            unset($task);

            return $tasks;
        });

        redirect('/');
    }
}

// Cada usuário visualiza somente as próprias tarefas.
$allTasks = read_json('tarefas');
$tasks = [];

foreach ($allTasks as $task) {
    $taskUser = '';

    if (isset($task['usuario'])) {
        $taskUser = (string) $task['usuario'];
    }

    if ($taskUser === $user) {
        $tasks[] = $task;
    }
}

page_start('Minhas tarefas');
?>

<nav>
    <span>Conectado como <strong><?= e($user) ?></strong></span>

    <a href="/tema.php">Alternar tema</a>

    <form action="/logout.php" method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
        <button type="submit">Sair</button>
    </form>
</nav>

<h2>Minhas tarefas</h2>

<?php if ($error): ?>
    <p class="erro"><?= e($error) ?></p>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
    <input type="hidden" name="acao" value="criar">

    <label>
        Título da tarefa
        <input name="titulo" maxlength="100" required placeholder="Ex.: revisar cookies">
    </label>

    <button type="submit">Adicionar</button>
</form>

<?php if (!$tasks): ?>
    <p>Nenhuma tarefa cadastrada.</p>
<?php endif; ?>

<ul>
    <?php foreach ($tasks as $task): ?>
        <?php $isCompleted = !empty($task['concluida']); ?>
        <li>
            <span>
                <?= e((string) $task['titulo']) ?>
                <?php if ($isCompleted): ?>
                    ✓
                <?php endif; ?>
            </span>

            <?php if (!$isCompleted): ?>
                <form method="post">
                    <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
                    <input type="hidden" name="acao" value="concluir">
                    <input type="hidden" name="id" value="<?= e((string) $task['id']) ?>">
                    <button type="submit">Concluir</button>
                </form>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>

<?php page_end(); ?>