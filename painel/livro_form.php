<?php
require_once __DIR__ . '/funcoes.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$campos = ['titulo' => '', 'autor' => '', 'editora' => '', 'materia' => '', 'isbn' => '', 'quantidade' => 1];
$l = $campos;

if ($id) {
    $r = consultar('SELECT titulo, autor, editora, materia, isbn, quantidade FROM livro WHERE idlivro = ?', 'i', [$id]);
    if (!$r) {
        flash('danger', 'Livro não encontrado.');
        redirecionar('livros.php');
    }
    $l = $r[0];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach (['titulo', 'autor', 'editora', 'materia', 'isbn'] as $c) {
        $l[$c] = trim($_POST[$c] ?? '');
    }
    $l['quantidade'] = max(0, (int) ($_POST['quantidade'] ?? 0));
    if ($l['titulo'] === '') {
        flash('danger', 'Informe o título do livro.');
    } else {
        $p = [$l['titulo'], $l['autor'], $l['editora'], $l['materia'], $l['isbn'], $l['quantidade']];
        if ($id) {
            executar('UPDATE livro SET titulo=?, autor=?, editora=?, materia=?, isbn=?, quantidade=? WHERE idlivro=?',
                'sssssii', array_merge($p, [$id]));
        } else {
            executar('INSERT INTO livro (titulo, autor, editora, materia, isbn, quantidade) VALUES (?,?,?,?,?,?)',
                'sssssi', $p);
        }
        flash('success', 'Livro salvo com sucesso!');
        redirecionar('livros.php');
    }
}

page_start($id ? 'Editar livro' : 'Novo livro');
$rotulos = ['titulo' => 'Título', 'autor' => 'Autor', 'editora' => 'Editora', 'materia' => 'Matéria', 'isbn' => 'ISBN'];
?>
<div class="card mb-3">
    <div class="card-body">
        <form method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="row">
                <?php foreach ($rotulos as $c => $rot) { ?>
                    <div class="form-group col-md-6"><label><?php echo $rot; ?></label>
                        <input class="form-control" name="<?php echo $c; ?>"<?php echo $c === 'titulo' ? ' required' : ''; ?> value="<?php echo e($l[$c]); ?>"></div>
                <?php } ?>
                <div class="form-group col-md-6"><label>Quantidade de exemplares</label>
                    <input type="number" min="0" class="form-control" name="quantidade" required value="<?php echo (int) $l['quantidade']; ?>"></div>
            </div>
            <button class="btn btn-success">Salvar</button>
            <a href="livros.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
<?php page_end(); ?>
