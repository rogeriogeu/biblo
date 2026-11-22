<?php
require_once __DIR__ . '/funcoes.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$ensinos = ['Fundamental I', 'Fundamental II', 'Ensino Médio', 'Ensino Médio Integral', 'EJA'];
$a = ['nome' => '', 'turma' => '', 'ensino' => $ensinos[0], 'telefone' => '', 'condicao' => 'Ativo'];

if ($id) {
    $l = consultar('SELECT nome, turma, ensino, telefone, condicao FROM estudante WHERE idestudante = ?', 'i', [$id]);
    if (!$l) {
        flash('danger', 'Aluno não encontrado.');
        redirecionar('alunos.php');
    }
    $a = $l[0];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $a = [
        'nome' => trim($_POST['nome'] ?? ''),
        'turma' => trim($_POST['turma'] ?? ''),
        'ensino' => $_POST['ensino'] ?? '',
        'telefone' => trim($_POST['telefone'] ?? ''),
        'condicao' => ($_POST['condicao'] ?? '') === 'Inativo' ? 'Inativo' : 'Ativo',
    ];
    if ($a['nome'] === '') {
        flash('danger', 'Informe o nome do aluno.');
    } else {
        if ($id) {
            executar('UPDATE estudante SET nome=?, turma=?, ensino=?, telefone=?, condicao=? WHERE idestudante=?',
                'sssssi', [$a['nome'], $a['turma'], $a['ensino'], $a['telefone'], $a['condicao'], $id]);
        } else {
            executar('INSERT INTO estudante (nome, turma, ensino, telefone, condicao) VALUES (?,?,?,?,?)',
                'sssss', [$a['nome'], $a['turma'], $a['ensino'], $a['telefone'], $a['condicao']]);
        }
        flash('success', 'Aluno salvo com sucesso!');
        redirecionar('alunos.php');
    }
}

page_start($id ? 'Editar aluno' : 'Novo aluno');
?>
<div class="card mb-3">
    <div class="card-body">
        <form method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="row">
                <div class="form-group col-md-6"><label>Nome</label>
                    <input class="form-control" name="nome" required value="<?php echo e($a['nome']); ?>"></div>
                <div class="form-group col-md-6"><label>Turma</label>
                    <input class="form-control" name="turma" value="<?php echo e($a['turma']); ?>"></div>
                <div class="form-group col-md-6"><label>Tipo de ensino</label>
                    <select class="form-control" name="ensino">
                        <?php foreach ($ensinos as $en) { ?>
                            <option<?php echo $en === $a['ensino'] ? ' selected' : ''; ?>><?php echo e($en); ?></option>
                        <?php } ?>
                    </select></div>
                <div class="form-group col-md-6"><label>Telefone</label>
                    <input class="form-control" name="telefone" value="<?php echo e($a['telefone']); ?>"></div>
                <div class="form-group col-md-6"><label>Condição</label>
                    <select class="form-control" name="condicao">
                        <?php foreach (['Ativo', 'Inativo'] as $c) { ?>
                            <option<?php echo $c === $a['condicao'] ? ' selected' : ''; ?>><?php echo $c; ?></option>
                        <?php } ?>
                    </select></div>
            </div>
            <button class="btn btn-success">Salvar</button>
            <a href="alunos.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
<?php page_end(); ?>
