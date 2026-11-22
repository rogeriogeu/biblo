<?php
require_once __DIR__ . '/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);
    $n = executar('UPDATE emprestimo SET data_devolucao = CURDATE() WHERE idemprestimo = ? AND data_devolucao IS NULL', 'i', [$id]);
    flash($n ? 'success' : 'warning', $n ? 'Devolução registrada.' : 'Empréstimo já devolvido ou inexistente.');
    redirecionar('emprestimos.php');
}

$q = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? '';
$like = '%' . $q . '%';
$where = '(a.nome LIKE ? OR l.titulo LIKE ?)';
if ($status === 'ativos') {
    $where .= ' AND e.data_devolucao IS NULL';
} elseif ($status === 'atrasados') {
    $where .= ' AND e.data_devolucao IS NULL AND e.data_prevista < CURDATE()';
} elseif ($status === 'devolvidos') {
    $where .= ' AND e.data_devolucao IS NOT NULL';
}
$lista = consultar(
    "SELECT e.idemprestimo, a.nome AS aluno, l.titulo, e.data_emprestimo, e.data_prevista, e.data_devolucao,
            CASE WHEN e.data_devolucao IS NULL THEN GREATEST(DATEDIFF(CURDATE(), e.data_prevista), 0)
                 ELSE GREATEST(DATEDIFF(e.data_devolucao, e.data_prevista), 0) END AS dias_atraso
     FROM emprestimo e
     JOIN estudante a ON a.idestudante = e.idestudante
     JOIN livro l ON l.idlivro = e.idlivro
     WHERE $where ORDER BY e.data_devolucao IS NOT NULL, e.data_prevista, e.idemprestimo DESC",
    'ss',
    [$like, $like]
);

page_start('Empréstimos/Devoluções');
?>
<div class="card mb-3">
    <div class="card-header">
        <form class="form-inline" method="GET">
            <input type="text" name="q" class="form-control mr-2" placeholder="Buscar aluno ou livro" value="<?php echo e($q); ?>">
            <select name="status" class="form-control mr-2">
                <?php foreach (['' => 'Todos', 'ativos' => 'Ativos', 'atrasados' => 'Atrasados', 'devolvidos' => 'Devolvidos'] as $v => $r) { ?>
                    <option value="<?php echo $v; ?>"<?php echo $v === $status ? ' selected' : ''; ?>><?php echo $r; ?></option>
                <?php } ?>
            </select>
            <button class="btn btn-primary mr-2">Filtrar</button>
            <a href="emprestimo_form.php" class="btn btn-success">Novo empréstimo</a>
        </form>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead><tr><th>Aluno</th><th>Livro</th><th>Empréstimo</th><th>Previsto</th><th>Devolução</th><th>Situação</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($lista as $r) {
                if ($r['data_devolucao']) {
                    $sit = $r['dias_atraso'] > 0 ? ['warning', 'Devolvido com ' . (int) $r['dias_atraso'] . ' dia(s) de atraso'] : ['success', 'Devolvido'];
                } elseif ($r['dias_atraso'] > 0) {
                    $sit = ['danger', 'Atrasado ' . (int) $r['dias_atraso'] . ' dia(s)'];
                } else {
                    $sit = ['info', 'No prazo'];
                } ?>
                <tr>
                    <td><?php echo e($r['aluno']); ?></td>
                    <td><?php echo e($r['titulo']); ?></td>
                    <td><?php echo data_br($r['data_emprestimo']); ?></td>
                    <td><?php echo data_br($r['data_prevista']); ?></td>
                    <td><?php echo data_br($r['data_devolucao']); ?></td>
                    <td><span class="badge badge-<?php echo $sit[0]; ?>"><?php echo e($sit[1]); ?></span></td>
                    <td class="text-right">
                        <?php if (!$r['data_devolucao']) { ?>
                            <a href="emprestimo_editar.php?id=<?php echo (int) $r['idemprestimo']; ?>" class="btn btn-sm btn-info">Editar/Renovar</a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Registrar devolução?');">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo (int) $r['idemprestimo']; ?>">
                                <button class="btn btn-sm btn-success">Devolver</button>
                            </form>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if (!$lista) { ?><tr><td colspan="7" class="text-center">Nenhum empréstimo encontrado.</td></tr><?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php page_end(); ?>
