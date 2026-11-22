<?php
require_once __DIR__ . '/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);
    try {
        executar('DELETE FROM livro WHERE idlivro = ?', 'i', [$id]);
        flash('success', 'Livro excluído.');
    } catch (mysqli_sql_exception $ex) {
        flash('danger', 'Não é possível excluir: o livro possui empréstimos registrados.');
    }
    redirecionar('livros.php');
}

$q = trim($_GET['q'] ?? '');
$like = '%' . $q . '%';
$livros = consultar(
    'SELECT l.idlivro, l.titulo, l.autor, l.editora, l.materia, l.isbn, l.quantidade,
            l.quantidade - (SELECT COUNT(*) FROM emprestimo e WHERE e.idlivro = l.idlivro AND e.data_devolucao IS NULL) AS disponiveis
     FROM livro l
     WHERE l.titulo LIKE ? OR l.autor LIKE ? OR l.editora LIKE ? OR l.materia LIKE ? OR l.isbn LIKE ?
     ORDER BY l.titulo',
    'sssss',
    [$like, $like, $like, $like, $like]
);

page_start('Livros');
?>
<div class="card mb-3">
    <div class="card-header">
        <form class="form-inline" method="GET">
            <input type="text" name="q" class="form-control mr-2" placeholder="Buscar título, autor, editora, matéria, ISBN" value="<?php echo e($q); ?>">
            <button class="btn btn-primary mr-2">Buscar</button>
            <a href="livro_form.php" class="btn btn-success">Novo livro</a>
        </form>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead><tr><th>Título</th><th>Autor</th><th>Editora</th><th>Matéria</th><th>ISBN</th><th>Qtd.</th><th>Disponíveis</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($livros as $l) { ?>
                <tr>
                    <td><?php echo e($l['titulo']); ?></td>
                    <td><?php echo e($l['autor']); ?></td>
                    <td><?php echo e($l['editora']); ?></td>
                    <td><?php echo e($l['materia']); ?></td>
                    <td><?php echo e($l['isbn']); ?></td>
                    <td><?php echo (int) $l['quantidade']; ?></td>
                    <td><span class="badge badge-<?php echo $l['disponiveis'] > 0 ? 'success' : 'danger'; ?>"><?php echo (int) $l['disponiveis']; ?></span></td>
                    <td class="text-right">
                        <a class="btn btn-sm btn-info" href="livro_form.php?id=<?php echo (int) $l['idlivro']; ?>">Editar</a>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Excluir este livro?');">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo (int) $l['idlivro']; ?>">
                            <button class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            <?php if (!$livros) { ?><tr><td colspan="8" class="text-center">Nenhum livro encontrado.</td></tr><?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php page_end(); ?>
