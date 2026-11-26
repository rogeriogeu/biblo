<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/modal.php';

function e($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

function is_admin(): bool
{
    return ($_SESSION['usuario_cargo'] ?? '') === 'Administrador';
}

function exigir_admin(): void
{
    if (!is_admin()) {
        http_response_code(403);
        exit('Acesso restrito ao administrador.');
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function csrf_check(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST'
        && !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Requisição inválida (CSRF). Volte e tente novamente.');
    }
}

function flash(string $tipo, string $msg): void
{
    $_SESSION['flash'] = [$tipo, $msg];
}

function redirecionar(string $url): void
{
    header('Location: ' . $url);
    exit;
}

// Executa uma consulta preparada e devolve todas as linhas.
function consultar(string $sql, string $tipos = '', array $params = []): array
{
    global $conexao;
    $stmt = mysqli_prepare($conexao, $sql);
    if ($tipos !== '') {
        mysqli_stmt_bind_param($stmt, $tipos, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $linhas = $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt);
    return $linhas;
}

// Executa INSERT/UPDATE/DELETE preparado e devolve as linhas afetadas.
function executar(string $sql, string $tipos = '', array $params = []): int
{
    global $conexao;
    $stmt = mysqli_prepare($conexao, $sql);
    if ($tipos !== '') {
        mysqli_stmt_bind_param($stmt, $tipos, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $n = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $n;
}

// Exclui um registro apenas se não houver empréstimo em aberto ($colunaEmp = idlivro|idestudante).
// Empréstimos já devolvidos são apagados junto, em transação. Devolve false se houver empréstimo ativo.
function excluir_sem_emprestimo_ativo(string $tabela, string $colunaId, string $colunaEmp, int $id): bool
{
    global $conexao;
    mysqli_begin_transaction($conexao);
    try {
        $ativos = (int) valor("SELECT COUNT(*) FROM emprestimo WHERE $colunaEmp = ? AND data_devolucao IS NULL FOR UPDATE", 'i', [$id]);
        if ($ativos > 0) {
            mysqli_rollback($conexao);
            return false;
        }
        executar("DELETE FROM emprestimo WHERE $colunaEmp = ?", 'i', [$id]);
        executar("DELETE FROM $tabela WHERE $colunaId = ?", 'i', [$id]);
        mysqli_commit($conexao);
        return true;
    } catch (mysqli_sql_exception $ex) {
        mysqli_rollback($conexao);
        throw $ex;
    }
}

function valor(string $sql, string $tipos = '', array $params = [])
{
    $l = consultar($sql, $tipos, $params);
    return $l ? array_values($l[0])[0] : 0;
}

function data_br(?string $d): string
{
    return $d ? date('d/m/Y', strtotime($d)) : '-';
}

function telefone_fmt(?string $t): string
{
    $d = preg_replace('/\D/', '', (string) $t);
    if (strlen($d) === 11) {
        return sprintf('(%s)%s-%s', substr($d, 0, 2), substr($d, 2, 5), substr($d, 7));
    }
    if (strlen($d) === 10) {
        return sprintf('(%s)%s-%s', substr($d, 0, 2), substr($d, 2, 4), substr($d, 6));
    }
    return trim((string) $t);
}

function page_start(string $titulo, string $ativo = ''): void
{
    csrf_check();
    include __DIR__ . '/header.php';
    echo '<body class="adminbody"><div id="main">';
    include __DIR__ . '/topbar.php';
    include __DIR__ . '/menu.php';
    echo '<div class="content-page"><div class="content"><div class="container-fluid">';
    echo '<div class="row"><div class="col-xl-12"><div class="breadcrumb-holder">'
        . '<h1 class="main-title float-left">' . e($titulo) . '</h1>'
        . '<ol class="breadcrumb float-right"><li class="breadcrumb-item">Biblo</li>'
        . '<li class="breadcrumb-item active">' . e($titulo) . '</li></ol><div class="clearfix"></div>'
        . '</div></div></div>';
    if (!empty($_SESSION['flash'])) {
        [$tipo, $msg] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $GLOBALS['modal_flash'] = [$tipo, $msg];
    }
}

function page_end(string $scriptExtra = ''): void
{
    echo '</div></div></div>';
    include __DIR__ . '/footer_base.php';
    echo modal_render($GLOBALS['modal_flash'] ?? null);
    echo '<style>.select2-container{display:block!important;width:100%!important;max-width:100%!important;box-sizing:border-box}.select2-container .select2-selection{width:100%!important}.select2-container .select2-selection--single{height:calc(2.25rem + 2px);border:1px solid #ced4da;border-radius:.25rem}.select2-container--default .select2-selection--single .select2-selection__rendered{line-height:calc(2.25rem);padding-left:.75rem}.select2-container--default .select2-selection--single .select2-selection__arrow{height:calc(2.25rem + 2px)}.select2-container--default .select2-selection--single .select2-selection__clear{margin-right:1.5rem;line-height:calc(2.25rem)}</style>' . $scriptExtra . '<script>document.addEventListener("input",function(e){var i=e.target;if(!i.matches||!i.matches("input[data-mask=tel]")){return;}var d=i.value.replace(/\D/g,"").slice(0,11),v=d;if(d.length>10){v="("+d.slice(0,2)+")"+d.slice(2,7)+"-"+d.slice(7);}else if(d.length>6){v="("+d.slice(0,2)+")"+d.slice(2,6)+"-"+d.slice(6);}else if(d.length>2){v="("+d.slice(0,2)+")"+d.slice(2);}else if(d.length>0){v="("+d;}i.value=v;});</script></body></html>';
}

function datatable_script(string $seletor, array $opcoes = []): string
{
    $base = [
        'language' => [
            'search' => 'Buscar:', 'lengthMenu' => 'Mostrar _MENU_ registros', 'info' => 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            'infoEmpty' => 'Nenhum registro', 'infoFiltered' => '(filtrado de _MAX_)', 'zeroRecords' => 'Nenhum registro encontrado.',
            'emptyTable' => 'Nenhum registro encontrado.',
            'paginate' => ['first' => 'Primeiro', 'previous' => 'Anterior', 'next' => 'Próximo', 'last' => 'Último'],
        ],
        'pageLength' => 25,
    ];
    $json = json_encode($opcoes + $base, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
    return '<script src="assets/plugins/datatables/datatables.min.js"></script><script>$(function(){$(' . json_encode($seletor) . ').DataTable(' . $json . ');});</script>';
}
