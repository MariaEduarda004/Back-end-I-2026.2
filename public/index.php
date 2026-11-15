<?php
require dirname(__DIR__) . '/config/app.php';
exigir_login();

$usuarioAtual = usuario();
$usuarioId = $usuarioAtual['id'];
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!valid_csrf()) {
        http_response_code(400);
        $erro = 'Requisição inválida. Atualize a página e tente novamente.';
    } else {
        $acao = (string) ($_POST['acao'] ?? '');

        if ($acao === 'criar') {
            $titulo = trim((string) ($_POST['titulo'] ?? ''));
            $materia = trim((string) ($_POST['materia'] ?? ''));
            $prazo = (string) ($_POST['prazo'] ?? '');
            $prioridade = (string) ($_POST['prioridade'] ?? '');

            $dataValida = preg_match('/^\d{4}-\d{2}-\d{2}$/', $prazo) === 1;
            if ($dataValida) {
                [$ano, $mes, $dia] = array_map('intval', explode('-', $prazo));
                $dataValida = checkdate($mes, $dia, $ano);
            }

            $tituloValido = $titulo !== '' && mb_strlen($titulo) <= 90;
            $materiaValida = $materia !== '' && mb_strlen($materia) <= 50;
            $prioridadeValida = in_array($prioridade, ['baixa', 'media', 'alta'], true);

            if (!$tituloValido || !$materiaValida || !$dataValida || !$prioridadeValida) {
                http_response_code(400);
                $erro = 'Preencha todos os campos com valores válidos.';
            } else {
                $tarefas = ler_json('tarefas.json');
                $tarefas[] = [
                    'id' => bin2hex(random_bytes(8)),
                    'titulo' => $titulo,
                    'materia' => $materia,
                    'prazo' => $prazo,
                    'prioridade' => $prioridade,
                    'concluida' => false,
                    'usuario_id' => $usuarioId,
                ];

                salvar_json('tarefas.json', $tarefas);
                flash('Tarefa adicionada com sucesso.');
                redirect('index.php');
            }
        } elseif ($acao === 'concluir') {
            $id = (string) ($_POST['id'] ?? '');
            $tarefas = ler_json('tarefas.json');
            $tarefaEncontrada = false;

            foreach ($tarefas as &$tarefa) {
                if ($tarefa['id'] === $id && $tarefa['usuario_id'] === $usuarioId) {
                    $tarefa['concluida'] = true;
                    $tarefaEncontrada = true;
                    break;
                }
            }
            unset($tarefa);

            if ($tarefaEncontrada) {
                salvar_json('tarefas.json', $tarefas);
                flash('Tarefa concluída.');
                redirect('index.php');
            }

            http_response_code(404);
            $erro = 'Tarefa não encontrada.';
        } else {
            http_response_code(400);
            $erro = 'Ação inválida.';
        }
    }
}

$tarefasDoUsuario = [];
foreach (ler_json('tarefas.json') as $tarefa) {
    if (($tarefa['usuario_id'] ?? null) === $usuarioId) {
        $tarefasDoUsuario[] = $tarefa;
    }
}

$totalTarefas = count($tarefasDoUsuario);
$tarefasConcluidas = 0;

foreach ($tarefasDoUsuario as $tarefa) {
    if ($tarefa['concluida']) {
        $tarefasConcluidas++;
    }
}

$tarefasPendentes = $totalTarefas - $tarefasConcluidas;
$tituloPagina = 'Meu painel';
require dirname(__DIR__) . '/views/header.php';
?>
<div class="heading">
    <div>
        <span class="eyebrow">SEU ESPAÇO DE ORGANIZAÇÃO</span>
        <h1>Olá, <?= e($usuarioAtual['nome']) ?></h1>
        <p>Seu próximo passo começa com uma tarefa.</p>
    </div>
    <a class="btn ghost" href="tema.php">Alternar tema</a>
</div>

<div class="stats">
    <div class="stat">
        <span>Tarefas totais</span>
        <strong><?= $totalTarefas ?></strong>
        <small>Itens cadastrados</small>
    </div>
    <div class="stat">
        <span>Em andamento</span>
        <strong><?= $tarefasPendentes ?></strong>
        <small>Próximos desafios</small>
    </div>
    <div class="stat">
        <span>Concluídas</span>
        <strong><?= $tarefasConcluidas ?></strong>
        <small>Seu progresso</small>
    </div>
</div>

<div class="columns">
    <section class="panel">
        <span class="eyebrow">PLANEJAR</span>
        <h2>Nova tarefa</h2>
        <p class="muted">Adicione uma atividade à sua agenda.</p>

        <?php if ($erro !== ''): ?>
            <div class="error"><?= e($erro) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
            <input type="hidden" name="acao" value="criar">

            <label>
                Título da tarefa
                <input name="titulo" maxlength="90" required placeholder="Ex.: Revisar PHP">
            </label>

            <label>
                Matéria
                <input name="materia" maxlength="50" required placeholder="Ex.: Back-end I">
            </label>

            <label>
                Prazo
                <input name="prazo" type="date" required>
            </label>

            <label>
                Prioridade
                <select name="prioridade">
                    <option value="media">Média</option>
                    <option value="alta">Alta</option>
                    <option value="baixa">Baixa</option>
                </select>
            </label>

            <button class="btn full">Adicionar tarefa</button>
        </form>
    </section>

    <section class="panel listpanel">
        <div class="listheader">
            <div>
                <span class="eyebrow">ACOMPANHAR</span>
                <h2>Minhas tarefas</h2>
            </div>
            <span class="count"><?= $totalTarefas ?> registros</span>
        </div>

        <?php if (count($tarefasDoUsuario) === 0): ?>
            <div class="empty">
                <h3>Nenhuma tarefa ainda</h3>
                <p>Comece cadastrando sua primeira atividade.</p>
            </div>
        <?php endif; ?>

        <?php foreach (array_reverse($tarefasDoUsuario) as $tarefa): ?>
            <article class="task">
                <div class="taskbody">
                    <strong><?= e($tarefa['titulo']) ?></strong>
                    <div class="taskmeta">
                        <?= e($tarefa['materia']) ?> · <?= e($tarefa['prazo']) ?>
                        <span class="pill <?= e($tarefa['prioridade']) ?>">
                            <?= e($tarefa['prioridade']) ?>
                        </span>
                    </div>
                </div>

                <?php if (!$tarefa['concluida']): ?>
                    <form method="post">
                        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
                        <input type="hidden" name="acao" value="concluir">
                        <input type="hidden" name="id" value="<?= e($tarefa['id']) ?>">
                        <button class="smallbtn">Concluir</button>
                    </form>
                <?php else: ?>
                    <span class="complete">Feita</span>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </section>
</div>
<?php require dirname(__DIR__) . '/views/footer.php'; ?>