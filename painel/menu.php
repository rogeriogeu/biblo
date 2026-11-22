<?php
$pagina = basename($_SERVER['SCRIPT_NAME']);
$itens = [
    ['index.php', 'index', 'fas fa-bars', 'Dashboard'],
    ['emprestimos.php', 'emprestimo', 'fa fa-laptop', 'Empréstimos/Devoluções'],
    ['alunos.php', 'aluno', 'fas fa-user-graduate', 'Alunos'],
    ['livros.php', 'livro', 'fa fa-book', 'Livros'],
];
if (is_admin()) {
    $itens[] = ['usuarios.php', 'usuario', 'fa fa-users', 'Usuários'];
}
?>
<div class="left main-sidebar">
    <div class="sidebar-inner leftscroll">
        <div id="sidebar-menu">
            <ul>
                <?php foreach ($itens as [$arq, $prefixo, $icone, $rotulo]) { ?>
                    <li class="submenu">
                        <a<?php echo strpos($pagina, $prefixo) === 0 ? ' class="active"' : ''; ?> href="<?php echo e($arq); ?>">
                            <i class="<?php echo e($icone); ?>"></i>
                            <span> <?php echo e($rotulo); ?> </span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
