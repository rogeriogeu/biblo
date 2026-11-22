<?php
require_once __DIR__ . '/funcoes.php';

$id = (int) ($_SESSION['usuario_id'] ?? 0);
$r = consultar('SELECT nome, telefone, email, login, senha FROM usuario WHERE id = ?', 'i', [$id]);
if (!$r) {
    flash('danger', 'Usuário não encontrado.');
    redirecionar('logout.php');
}
$u = $r[0];
$hash = $u['senha'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach (['nome', 'telefone', 'email', 'login'] as $c) {
        $u[$c] = trim($_POST[$c] ?? '');
    }
    $u['telefone'] = telefone_fmt($u['telefone']);
    $atual = $_POST['senha_atual'] ?? '';
    $nova = $_POST['nova_senha'] ?? '';
    $conf = $_POST['confirma_senha'] ?? '';
    $dup = valor('SELECT COUNT(*) FROM usuario WHERE (login = ? OR email = ?) AND id <> ?', 'ssi', [$u['login'], $u['email'], $id]);

    if ($u['nome'] === '' || $u['login'] === '' || !filter_var($u['email'], FILTER_VALIDATE_EMAIL)) {
        flash('danger', 'Informe nome, login e um e-mail válido.');
    } elseif ($dup > 0) {
        flash('danger', 'Login ou e-mail já cadastrado.');
    } elseif ($nova !== '' && !password_verify($atual, $hash)) {
        flash('danger', 'Senha atual incorreta.');
    } elseif ($nova !== '' && strlen($nova) < 6) {
        flash('danger', 'A nova senha deve ter ao menos 6 caracteres.');
    } elseif ($nova !== $conf) {
        flash('danger', 'A confirmação da senha não confere.');
    } else {
        $p = [$u['nome'], $u['telefone'], $u['email'], $u['login']];
        if ($nova !== '') {
            executar('UPDATE usuario SET nome=?, telefone=?, email=?, login=?, senha=? WHERE id=?',
                'sssssi', array_merge($p, [password_hash($nova, PASSWORD_DEFAULT), $id]));
        } else {
            executar('UPDATE usuario SET nome=?, telefone=?, email=?, login=? WHERE id=?', 'ssssi', array_merge($p, [$id]));
        }
        $_SESSION['usuario_nome'] = $u['nome'];
        flash('success', 'Perfil atualizado com sucesso!');
        redirecionar('perfil.php');
    }
}

page_start('Meu perfil');
?>
<div class="card mb-3">
    <div class="card-body">
        <form method="POST" autocomplete="off">
            <?php echo csrf_field(); ?>
            <div class="row">
                <div class="form-group col-md-6"><label>Nome</label>
                    <input class="form-control" name="nome" required value="<?php echo e($u['nome']); ?>"></div>
                <div class="form-group col-md-6"><label>Telefone</label>
                    <input class="form-control" name="telefone" data-mask="tel" maxlength="14" placeholder="(xx)xxxxx-xxxx" value="<?php echo e(telefone_fmt($u['telefone'])); ?>"></div>
                <div class="form-group col-md-6"><label>E-mail</label>
                    <input type="email" class="form-control" name="email" required value="<?php echo e($u['email']); ?>"></div>
                <div class="form-group col-md-6"><label>Login</label>
                    <input class="form-control" name="login" required value="<?php echo e($u['login']); ?>"></div>
            </div>
            <h6 class="mt-2">Trocar senha <small class="text-muted">(deixe em branco para manter a atual)</small></h6>
            <div class="row">
                <div class="form-group col-md-4"><label>Senha atual</label>
                    <input type="password" class="form-control" name="senha_atual" autocomplete="current-password"></div>
                <div class="form-group col-md-4"><label>Nova senha</label>
                    <input type="password" class="form-control" name="nova_senha" autocomplete="new-password"></div>
                <div class="form-group col-md-4"><label>Confirmar nova senha</label>
                    <input type="password" class="form-control" name="confirma_senha" autocomplete="new-password"></div>
            </div>
            <button class="btn btn-success">Salvar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
<?php page_end(); ?>
