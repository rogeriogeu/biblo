<?php
require_once __DIR__ . '/funcoes.php';

$totLivros = (int) valor('SELECT COUNT(*) FROM livro');
$totExemplares = (int) valor('SELECT COALESCE(SUM(quantidade),0) FROM livro');
$totAlunos = (int) valor("SELECT COUNT(*) FROM estudante WHERE condicao <> 'Inativo'");
$ativos = (int) valor('SELECT COUNT(*) FROM emprestimo WHERE data_devolucao IS NULL');
$atrasados = (int) valor('SELECT COUNT(*) FROM emprestimo WHERE data_devolucao IS NULL AND data_prevista < CURDATE()');
$devolvidos = (int) valor('SELECT COUNT(*) FROM emprestimo WHERE data_devolucao IS NOT NULL');
$disponiveis = max(0, $totExemplares - $ativos);

$ultimos = consultar(
    'SELECT a.nome AS aluno, l.titulo, e.data_emprestimo, e.data_prevista, e.data_devolucao
     FROM emprestimo e JOIN estudante a ON a.idestudante = e.idestudante JOIN livro l ON l.idlivro = e.idlivro
     ORDER BY e.data_emprestimo DESC, e.idemprestimo DESC LIMIT 5'
);
$maisEmprestados = consultar(
    'SELECT l.titulo, COUNT(*) AS total FROM emprestimo e JOIN livro l ON l.idlivro = e.idlivro
     GROUP BY l.idlivro, l.titulo ORDER BY total DESC, l.titulo LIMIT 5'
);

$porMes = [];
for ($i = 5; $i >= 0; $i--) {
    $porMes[date('Y-m', strtotime("first day of -$i month"))] = 0;
}
foreach (consultar(
    "SELECT DATE_FORMAT(data_emprestimo, '%Y-%m') AS mes, COUNT(*) AS total FROM emprestimo
     WHERE data_emprestimo >= DATE_FORMAT(CURDATE() - INTERVAL 5 MONTH, '%Y-%m-01') GROUP BY mes"
) as $r) {
    if (isset($porMes[$r['mes']])) {
        $porMes[$r['mes']] = (int) $r['total'];
    }
}

page_start('Dashboard');

$totUsuarios = (int) valor('SELECT COUNT(*) FROM usuario');
$cards = [
    ['Livros (títulos)', $totLivros, 'fa-book', 'primary', 'livros.php'],
    ['Alunos ativos', $totAlunos, 'fa-user-graduate', 'info', 'alunos.php'],
    ['Usuários', $totUsuarios, 'fa-users', 'dark', is_admin() ? 'usuarios.php' : 'index.php'],
    ['Exemplares disponíveis', $disponiveis, 'fa-bookmark', 'secondary', 'livros.php'],
    ['Empréstimos ativos', $ativos, 'fa-laptop', 'warning', 'emprestimos.php?status=ativos'],
    ['Atrasados', $atrasados, 'fa-exclamation-triangle', 'danger', 'emprestimos.php?status=atrasados'],
    ['Devolvidos', $devolvidos, 'fa-check', 'success', 'emprestimos.php?status=devolvidos'],
    ['Total de exemplares', $totExemplares, 'fa-layer-group', 'primary', 'livros.php'],
];
?>
<div class="row">
    <?php foreach ($cards as [$rot, $val, $ico, $cor, $link]) { ?>
        <div class="col-sm-6 col-xl-3 mb-3">
            <a href="<?php echo e($link); ?>" class="text-white" style="text-decoration:none">
                <div class="card-box noradius noborder bg-<?php echo $cor; ?>" style="margin-bottom:0;min-height:150px">
                    <i class="fa <?php echo $ico; ?> float-right text-white" style="font-size:56px;opacity:.5"></i>
                    <h6 class="text-white text-uppercase m-b-20"><?php echo e($rot); ?></h6>
                    <h1 class="m-b-20 text-white" style="font-size:44px;font-weight:700"><?php echo (int) $val; ?></h1>
                </div>
            </a>
        </div>
    <?php } ?>
</div>

<div class="row">
    <div class="col-lg-7 mb-3">
        <div class="card"><div class="card-header"><h5>Empréstimos por mês</h5></div>
            <div class="card-body"><canvas id="graficoMes" height="120"></canvas></div></div>
    </div>
    <div class="col-lg-5 mb-3">
        <div class="card"><div class="card-header"><h5>Livros mais emprestados</h5></div>
            <div class="card-body">
                <ol class="mb-0">
                    <?php foreach ($maisEmprestados as $m) { ?>
                        <li><?php echo e($m['titulo']); ?> <span class="badge badge-primary"><?php echo (int) $m['total']; ?></span></li>
                    <?php } ?>
                    <?php if (!$maisEmprestados) { ?><li class="text-muted">Sem empréstimos ainda.</li><?php } ?>
                </ol>
            </div></div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><h5>Últimos empréstimos</h5></div>
    <div class="card-body table-responsive">
        <table class="table table-striped mb-0">
            <thead><tr><th>Aluno</th><th>Livro</th><th>Empréstimo</th><th>Previsto</th><th>Situação</th></tr></thead>
            <tbody>
            <?php foreach ($ultimos as $u) {
                $atr = !$u['data_devolucao'] && $u['data_prevista'] < date('Y-m-d');
                $sit = $u['data_devolucao'] ? ['success', 'Devolvido'] : ($atr ? ['danger', 'Atrasado'] : ['info', 'No prazo']); ?>
                <tr>
                    <td><?php echo e($u['aluno']); ?></td>
                    <td><?php echo e($u['titulo']); ?></td>
                    <td><?php echo data_br($u['data_emprestimo']); ?></td>
                    <td><?php echo data_br($u['data_prevista']); ?></td>
                    <td><span class="badge badge-<?php echo $sit[0]; ?>"><?php echo $sit[1]; ?></span></td>
                </tr>
            <?php } ?>
            <?php if (!$ultimos) { ?><tr><td colspan="5" class="text-center">Nenhum empréstimo registrado.</td></tr><?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$labels = array_map(fn($m) => date('m/Y', strtotime($m . '-01')), array_keys($porMes));
page_end('<script>new Chart(document.getElementById("graficoMes"),{type:"bar",data:{labels:'
    . json_encode($labels) . ',datasets:[{label:"Empréstimos",backgroundColor:"#fb7e6d",data:'
    . json_encode(array_values($porMes)) . '}]},options:{scales:{yAxes:[{ticks:{beginAtZero:true,precision:0}}]}}});</script>');
