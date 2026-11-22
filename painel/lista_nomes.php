<?php
// Tela compartilhada de autores/editoras/matérias. Espera $campo ('autor'|'editora'|'materia'), $titulo e $arquivo definidos pelo chamador.
require_once __DIR__ . '/funcoes.php';

if (!in_array($campo, ['autor', 'editora', 'materia'], true)) {
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
            $existente = consultar("SELECT $campo AS v FROM livro WHERE $campo = ? AND BINARY $campo <> BINARY ? LIMIT 1", 'ss', [$novo, $antigo]);
            $mesmo = strcasecmp($novo, $antigo) === 0;
            $destino = (!$mesmo && $existente) ? $existente[0]['v'] : $novo;
            $n = executar("UPDATE livro SET $campo = ? WHERE $campo = ?", 'ss', [$destino, $antigo]);
            flash('success', ($destino !== $novo ? 'Unido ao nome já existente "' . $destino . '". ' : '') . "$n livro(s) atualizado(s).");
        }
    } elseif ($acao === 'excluir') {
        $n = executar("UPDATE livro SET $campo = '' WHERE $campo = ?", 's', [$antigo]);
        flash('success', "Removido de $n livro(s). Os livros foram mantidos, sem $campo.");
    }
    redirecionar($arquivo);
}

$q = trim($_GET['q'] ?? '');
$itens = consultar(
    "SELECT MIN($campo) AS nome, COUNT(*) AS total FROM livro WHERE $campo <> '' AND $campo LIKE ?
     GROUP BY $campo ORDER BY nome",
    's',
    ['%' . $q . '%']
);

page_start($titulo);
?>
<div class="card mb-3">
    <div class="card-header">
        <form class="form-inline" method="GET">
            <input type="text" name="q" class="form-control mr-2" placeholder="Buscar <?php echo e(strtolower($titulo)); ?>" value="<?php echo e($q); ?>">
            <button class="btn btn-primary mr-2">Buscar</button>
            <a href="livro_form.php" class="btn btn-success">Novo livro</a>
        </form>
        <small class="text-muted">Os nomes são cadastrados ao salvar um livro. Aqui você pode renomear (atualiza todos os livros; se o novo nome já existir, eles são unidos) ou remover.</small>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead><tr><th>Nome</th><th>Livros</th><th style="width:55%">Ações</th></tr></thead>
            <tbody>
            <?php foreach ($itens as $i) { ?>
                <tr>
                    <td><?php echo e($i['nome']); ?></td>
                    <td><a href="livros.php?q=<?php echo urlencode($i['nome']); ?>"><span class="badge badge-primary"><?php echo (int) $i['total']; ?></span></a></td>
                    <td>
                        <form method="POST" class="form-inline d-inline">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="acao" value="renomear">
                            <input type="hidden" name="antigo" value="<?php echo e($i['nome']); ?>">
                            <input class="form-control form-control-sm mr-1" name="novo" required value="<?php echo e($i['nome']); ?>">
                            <button class="btn btn-sm btn-info mr-2">Renomear</button>
                        </form>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Remover &quot;<?php echo e(addslashes($i['nome'])); ?>&quot; de <?php echo (int) $i['total']; ?> livro(s)? Os livros não serão excluídos.');">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="acao" value="excluir">
                            <input type="hidden" name="antigo" value="<?php echo e($i['nome']); ?>">
                            <button class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            <?php if (!$itens) { ?><tr><td colspan="3" class="text-center">Nenhum registro encontrado.</td></tr><?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php page_end(); ?>
