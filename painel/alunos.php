<?php
require_once __DIR__ . '/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);
    try {
        if (excluir_sem_emprestimo_ativo('estudante', 'idestudante', 'idestudante', $id)) {
            flash('success', 'Aluno excluído (histórico de empréstimos devolvidos removido junto).');
        } else {
            flash('danger', 'Não é possível excluir: o aluno possui empréstimo em aberto. Registre a devolução primeiro.');
        }
    } catch (mysqli_sql_exception $ex) {
        flash('danger', 'Não foi possível excluir o aluno.');
    }
    redirecionar('alunos.php');
}

$q = trim($_GET['q'] ?? '');
$alunos = consultar(
    'SELECT idestudante, nome, turma, ensino, telefone, condicao FROM estudante ORDER BY nome'
);

page_start('Alunos');
?>
<div class="card mb-3">
    <div class="card-header">
        <a href="aluno_form.php" class="btn btn-success">Novo aluno</a>
    </div>
    <div class="card-body table-responsive">
        <table id="tabela-alunos" class="table table-striped">
            <thead><tr><th>Nome</th><th>Turma</th><th>Ensino</th><th>Telefone</th><th>Condição</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($alunos as $a) { ?>
                <tr>
                    <td><?php echo e($a['nome']); ?></td>
                    <td><?php echo e($a['turma']); ?></td>
                    <td><?php echo e($a['ensino']); ?></td>
                    <td><?php echo e(telefone_fmt($a['telefone'])); ?></td>
                    <td><span class="badge badge-<?php echo $a['condicao'] === 'Inativo' ? 'secondary' : 'success'; ?>"><?php echo e($a['condicao']); ?></span></td>
                    <td class="text-right">
                        <a class="btn btn-sm btn-info" href="aluno_form.php?id=<?php echo (int) $a['idestudante']; ?>">Editar</a>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Excluir este aluno?');">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo (int) $a['idestudante']; ?>">
                            <button class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php page_end(datatable_script('#tabela-alunos', ['order' => [[0, 'asc']], 'search' => ['search' => $q], 'columnDefs' => [['orderable' => false, 'searchable' => false, 'targets' => 5]]])); ?>
