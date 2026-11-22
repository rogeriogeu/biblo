<?php
require_once __DIR__ . '/funcoes.php';
exigir_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);
    if ($id === (int) $_SESSION['usuario_id']) {
        flash('danger', 'Você não pode excluir o próprio usuário.');
    } else {
        executar('DELETE FROM usuario WHERE id = ?', 'i', [$id]);
        flash('success', 'Usuário excluído.');
    }
    redirecionar('usuarios.php');
}

$q = trim($_GET['q'] ?? '');
$like = '%' . $q . '%';
$usuarios = consultar(
    'SELECT id, nome, cargo, telefone, email, login FROM usuario
     WHERE nome LIKE ? OR email LIKE ? OR login LIKE ? ORDER BY nome',
    'sss',
    [$like, $like, $like]
);

page_start('Usuários');
?>
<div class="card mb-3">
    <div class="card-header">
        <form class="form-inline" method="GET">
            <input type="text" name="q" class="form-control mr-2" placeholder="Buscar nome, e-mail, login" value="<?php echo e($q); ?>">
            <button class="btn btn-primary mr-2">Buscar</button>
            <a href="usuario_form.php" class="btn btn-success">Novo usuário</a>
        </form>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead><tr><th>Nome</th><th>Cargo</th><th>Telefone</th><th>E-mail</th><th>Login</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($usuarios as $u) { ?>
                <tr>
                    <td><?php echo e($u['nome']); ?></td>
                    <td><?php echo e($u['cargo']); ?></td>
                    <td><?php echo e(telefone_fmt($u['telefone'])); ?></td>
                    <td><?php echo e($u['email']); ?></td>
                    <td><?php echo e($u['login']); ?></td>
                    <td class="text-right">
                        <a class="btn btn-sm btn-info" href="usuario_form.php?id=<?php echo (int) $u['id']; ?>">Editar</a>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Excluir este usuário?');">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo (int) $u['id']; ?>">
                            <button class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            <?php if (!$usuarios) { ?><tr><td colspan="6" class="text-center">Nenhum usuário encontrado.</td></tr><?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php page_end(); ?>
