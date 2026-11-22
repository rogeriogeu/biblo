<?php
require_once __DIR__ . '/funcoes.php';

$hoje = date('Y-m-d');
$f = ['idlivro' => (int) ($_GET['livro'] ?? 0), 'idestudante' => 0, 'data_emprestimo' => $hoje, 'data_prevista' => date('Y-m-d', strtotime('+14 days'))];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $f['idlivro'] = (int) ($_POST['idlivro'] ?? 0);
    $f['idestudante'] = (int) ($_POST['idestudante'] ?? 0);
    $f['data_emprestimo'] = $_POST['data_emprestimo'] ?? $hoje;
    $f['data_prevista'] = $_POST['data_prevista'] ?? '';

    $dataOk = preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['data_emprestimo']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['data_prevista']);
    $disp = valor('SELECT l.quantidade - (SELECT COUNT(*) FROM emprestimo e WHERE e.idlivro = l.idlivro AND e.data_devolucao IS NULL)
                   FROM livro l WHERE l.idlivro = ?', 'i', [$f['idlivro']]);
    $ativo = valor("SELECT COUNT(*) FROM estudante WHERE idestudante = ? AND condicao <> 'Inativo'", 'i', [$f['idestudante']]);

    if (!$dataOk || $f['data_prevista'] < $f['data_emprestimo']) {
        flash('danger', 'Datas inválidas: a data prevista não pode ser anterior ao empréstimo.');
    } elseif (!$ativo) {
        flash('danger', 'Selecione um aluno ativo.');
    } elseif ($disp < 1) {
        flash('danger', 'Livro sem exemplares disponíveis.');
    } else {
        executar('INSERT INTO emprestimo (idlivro, idestudante, idusuario, data_emprestimo, data_prevista) VALUES (?,?,?,?,?)',
            'iiiss', [$f['idlivro'], $f['idestudante'], (int) $_SESSION['usuario_id'], $f['data_emprestimo'], $f['data_prevista']]);
        flash('success', 'Empréstimo registrado.');
        redirecionar('emprestimos.php');
    }
}

$alunos = consultar("SELECT idestudante, nome, turma FROM estudante WHERE condicao <> 'Inativo' ORDER BY nome");
$livros = consultar(
    'SELECT l.idlivro, l.titulo, l.quantidade - (SELECT COUNT(*) FROM emprestimo e WHERE e.idlivro = l.idlivro AND e.data_devolucao IS NULL) AS disp
     FROM livro l ORDER BY l.titulo'
);

page_start('Novo empréstimo');
?>
<div class="card mb-3">
    <div class="card-body">
        <form method="POST">
            <?php echo csrf_field(); ?>
            <div class="row">
                <div class="form-group col-md-6"><label>Aluno</label>
                    <select class="form-control" name="idestudante" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($alunos as $a) { ?>
                            <option value="<?php echo (int) $a['idestudante']; ?>"<?php echo $a['idestudante'] == $f['idestudante'] ? ' selected' : ''; ?>><?php echo e($a['nome'] . ($a['turma'] ? ' - ' . $a['turma'] : '')); ?></option>
                        <?php } ?>
                    </select></div>
                <div class="form-group col-md-6"><label>Livro</label>
                    <select class="form-control" name="idlivro" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($livros as $l) { ?>
                            <option value="<?php echo (int) $l['idlivro']; ?>"<?php echo $l['idlivro'] == $f['idlivro'] ? ' selected' : ''; ?><?php echo $l['disp'] < 1 ? ' disabled' : ''; ?>><?php echo e($l['titulo']) . ' (' . (int) $l['disp'] . ' disp.)'; ?></option>
                        <?php } ?>
                    </select></div>
                <div class="form-group col-md-6"><label>Data do empréstimo</label>
                    <input type="date" class="form-control" name="data_emprestimo" required value="<?php echo e($f['data_emprestimo']); ?>"></div>
                <div class="form-group col-md-6"><label>Devolução prevista</label>
                    <input type="date" class="form-control" name="data_prevista" required value="<?php echo e($f['data_prevista']); ?>"></div>
            </div>
            <button class="btn btn-success">Registrar</button>
            <a href="emprestimos.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
<?php page_end(); ?>
