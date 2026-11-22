<?php
require_once __DIR__ . '/funcoes.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$r = consultar(
    'SELECT e.idemprestimo, a.nome AS aluno, l.titulo, e.data_emprestimo, e.data_prevista, e.data_devolucao
     FROM emprestimo e
     JOIN estudante a ON a.idestudante = e.idestudante
     JOIN livro l ON l.idlivro = e.idlivro
     WHERE e.idemprestimo = ?',
    'i',
    [$id]
);
if (!$r) {
    flash('danger', 'Empréstimo não encontrado.');
    redirecionar('emprestimos.php');
}
$r = $r[0];
if ($r['data_devolucao']) {
    flash('warning', 'Empréstimo já devolvido; não é possível renovar.');
    redirecionar('emprestimos.php');
}

$nova = $r['data_prevista'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nova = trim($_POST['data_prevista'] ?? '');
    $d = DateTime::createFromFormat('Y-m-d', $nova);
    if (!$d || $d->format('Y-m-d') !== $nova) {
        flash('danger', 'Informe uma data válida.');
    } elseif ($nova < $r['data_emprestimo']) {
        flash('danger', 'A nova data não pode ser anterior à data do empréstimo (' . data_br($r['data_emprestimo']) . ').');
    } else {
        executar('UPDATE emprestimo SET data_prevista = ? WHERE idemprestimo = ? AND data_devolucao IS NULL', 'si', [$nova, $id]);
        flash('success', 'Empréstimo renovado. Nova data de devolução: ' . data_br($nova) . '.');
        redirecionar('emprestimos.php');
    }
}

page_start('Editar/Renovar empréstimo');
?>
<div class="card mb-3">
    <div class="card-body">
        <p><strong>Aluno:</strong> <?php echo e($r['aluno']); ?><br>
            <strong>Livro:</strong> <?php echo e($r['titulo']); ?><br>
            <strong>Emprestado em:</strong> <?php echo data_br($r['data_emprestimo']); ?><br>
            <strong>Devolução prevista atual:</strong> <?php echo data_br($r['data_prevista']); ?></p>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="form-group col-md-4 px-0"><label>Nova data de devolução prevista</label>
                <input type="date" class="form-control" name="data_prevista" required min="<?php echo e($r['data_emprestimo']); ?>" value="<?php echo e($nova); ?>"></div>
            <button class="btn btn-success">Salvar renovação</button>
            <a href="emprestimos.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
<?php page_end(); ?>
