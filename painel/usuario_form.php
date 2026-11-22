<?php
require_once __DIR__ . '/funcoes.php';
exigir_admin();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$cargos = ['Administrador', 'Bibliotecário', 'Professor'];
$u = ['nome' => '', 'cargo' => 'Bibliotecário', 'telefone' => '', 'email' => '', 'login' => ''];

if ($id) {
    $r = consultar('SELECT nome, cargo, telefone, email, login FROM usuario WHERE id = ?', 'i', [$id]);
    if (!$r) {
        flash('danger', 'Usuário não encontrado.');
        redirecionar('usuarios.php');
    }
    $u = $r[0];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach (['nome', 'telefone', 'email', 'login'] as $c) {
        $u[$c] = trim($_POST[$c] ?? '');
    }
    $u['telefone'] = telefone_fmt($u['telefone']);
    $u['cargo'] = in_array($_POST['cargo'] ?? '', $cargos, true) ? $_POST['cargo'] : 'Bibliotecário';
    $senha = $_POST['senha'] ?? '';

    $dup = valor('SELECT COUNT(*) FROM usuario WHERE (login = ? OR email = ?) AND id <> ?', 'ssi', [$u['login'], $u['email'], $id]);

    if ($u['nome'] === '' || $u['login'] === '' || !filter_var($u['email'], FILTER_VALIDATE_EMAIL)) {
        flash('danger', 'Informe nome, login e um e-mail válido.');
    } elseif (!$id && $senha === '') {
        flash('danger', 'Informe a senha do novo usuário.');
    } elseif ($dup > 0) {
        flash('danger', 'Login ou e-mail já cadastrado.');
    } else {
        $p = [$u['nome'], $u['cargo'], $u['telefone'], $u['email'], $u['login']];
        if ($id) {
            if ($senha !== '') {
                executar('UPDATE usuario SET nome=?, cargo=?, telefone=?, email=?, login=?, senha=? WHERE id=?',
                    'ssssssi', array_merge($p, [password_hash($senha, PASSWORD_DEFAULT), $id]));
            } else {
                executar('UPDATE usuario SET nome=?, cargo=?, telefone=?, email=?, login=? WHERE id=?',
                    'sssssi', array_merge($p, [$id]));
            }
        } else {
            executar('INSERT INTO usuario (nome, cargo, telefone, email, login, senha) VALUES (?,?,?,?,?,?)',
                'ssssss', array_merge($p, [password_hash($senha, PASSWORD_DEFAULT)]));
        }
        flash('success', 'Usuário salvo com sucesso!');
        redirecionar('usuarios.php');
    }
}

page_start($id ? 'Editar usuário' : 'Novo usuário');
?>
<div class="card mb-3">
    <div class="card-body">
        <form method="POST" autocomplete="off">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="row">
                <div class="form-group col-md-6"><label>Nome</label>
                    <input class="form-control" name="nome" required value="<?php echo e($u['nome']); ?>"></div>
                <div class="form-group col-md-6"><label>Cargo</label>
                    <select class="form-control" name="cargo">
                        <?php foreach ($cargos as $c) { ?>
                            <option<?php echo $c === $u['cargo'] ? ' selected' : ''; ?>><?php echo e($c); ?></option>
                        <?php } ?>
                    </select></div>
                <div class="form-group col-md-6"><label>Telefone</label>
                    <input class="form-control" name="telefone" data-mask="tel" maxlength="14" placeholder="(xx)xxxxx-xxxx" value="<?php echo e(telefone_fmt($u['telefone'])); ?>"></div>
                <div class="form-group col-md-6"><label>E-mail</label>
                    <input type="email" class="form-control" name="email" required value="<?php echo e($u['email']); ?>"></div>
                <div class="form-group col-md-6"><label>Login</label>
                    <input class="form-control" name="login" required value="<?php echo e($u['login']); ?>"></div>
                <div class="form-group col-md-6"><label>Senha<?php echo $id ? ' (deixe em branco para manter)' : ''; ?></label>
                    <input type="password" class="form-control" name="senha" autocomplete="new-password"<?php echo $id ? '' : ' required'; ?>></div>
            </div>
            <button class="btn btn-success">Salvar</button>
            <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
<?php page_end(); ?>
