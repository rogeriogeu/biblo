<?php
// Tela compartilhada de autores/editoras/matérias/turmas/tipos de ensino. Espera $campo ('autor'|'editora'|'materia'|'turma'|'ensino'), $titulo e $arquivo definidos pelo chamador.
require_once __DIR__ . '/funcoes.php';

$livro = ['livro', 'livro', 'livros', 'livros.php', 'livro_form.php', 'Novo livro'];
$aluno = ['estudante', 'aluno', 'alunos', 'alunos.php', 'aluno_form.php', 'Novo aluno'];
$fontes = ['autor' => $livro, 'editora' => $livro, 'materia' => $livro, 'turma' => $aluno, 'ensino' => $aluno];
[$tabela, $un, $pl, $listagem, $form, $novoRot] = $fontes[$campo] ?? [null, null, null, null, null, null];
if ($tabela === null) {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $acao = $_POST['acao'] ?? '';
    $antigo = trim($_POST['antigo'] ?? '');

    if ($antigo === '') {
        flash('danger', 'Registro inválido.');
    } elseif ($acao === 'renomear') {
        $novo = trim(preg_replace('/\s+/u', ' ', $_POST['novo'] ?? ''));
        if ($novo === '') {
            flash('danger', 'Informe o novo nome.');
        } else {
            // Se já existe outro nome igual (sem diferenciar maiúsculas), une usando a grafia existente.
            $existente = consultar("SELECT $campo AS v FROM $tabela WHERE $campo = ? AND BINARY $campo <> BINARY ? LIMIT 1", 'ss', [$novo, $antigo]);
            $mesmo = strcasecmp($novo, $antigo) === 0;
            $destino = (!$mesmo && $existente) ? $existente[0]['v'] : $novo;
            $n = executar("UPDATE $tabela SET $campo = ? WHERE $campo = ?", 'ss', [$destino, $antigo]);
            flash('success', ($destino !== $novo ? 'Unido ao nome já existente "' . $destino . '". ' : '') . "$n $un(s) atualizado(s).");
        }
    } elseif ($acao === 'excluir') {
        $n = executar("UPDATE $tabela SET $campo = '' WHERE $campo = ?", 's', [$antigo]);
        flash('success', "Removido de $n $un(s). Os registros foram mantidos, sem $campo.");
    }
    redirecionar($arquivo);
}

$q = trim($_GET['q'] ?? '');
$itens = consultar(
    "SELECT MIN($campo) AS nome, COUNT(*) AS total FROM $tabela WHERE $campo <> '' GROUP BY $campo ORDER BY nome"
);

page_start($titulo);
?>
<div class="card mb-3">
    <div class="card-header">
        <a href="<?php echo $form; ?>" class="btn btn-success"><?php echo $novoRot; ?></a>
        <small class="text-muted">Os nomes são cadastrados ao salvar um <?php echo $un; ?>. Aqui você pode renomear (atualiza todos os <?php echo $pl; ?>; se o novo nome já existir, são unidos) ou remover.</small>
    </div>
    <div class="card-body table-responsive">
        <table id="tabela-nomes" class="table table-striped">
            <thead><tr><th>Nome</th><th><?php echo ucfirst($pl); ?></th><th style="width:55%">Ações</th></tr></thead>
            <tbody>
            <?php foreach ($itens as $i) { ?>
                <tr>
                    <td><?php echo e($i['nome']); ?></td>
                    <td><a href="<?php echo $listagem; ?>?q=<?php echo urlencode($i['nome']); ?>"><span class="badge badge-primary"><?php echo (int) $i['total']; ?></span></a></td>
                    <td>
                        <form method="POST" class="form-inline d-inline">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="acao" value="renomear">
                            <input type="hidden" name="antigo" value="<?php echo e($i['nome']); ?>">
                            <input class="form-control form-control-sm mr-1" name="novo" required value="<?php echo e($i['nome']); ?>">
                            <button class="btn btn-sm btn-info mr-2">Renomear</button>
                        </form>
                        <form method="POST" class="d-inline" data-confirm="Remover &quot;<?php echo e($i['nome']); ?>&quot; de <?php echo (int) $i['total']; ?> (s)? Os registros não serão excluídos.">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="acao" value="excluir">
                            <input type="hidden" name="antigo" value="<?php echo e($i['nome']); ?>">
                            <button class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php page_end(datatable_script('#tabela-nomes', ['order' => [[0, 'asc']], 'search' => ['search' => $q], 'columnDefs' => [['orderable' => false, 'searchable' => false, 'targets' => 2]]])); ?>
