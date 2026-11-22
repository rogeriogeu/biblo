<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexao.php';

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

function valor(string $sql, string $tipos = '', array $params = [])
{
    $l = consultar($sql, $tipos, $params);
    return $l ? array_values($l[0])[0] : 0;
}

function data_br(?string $d): string
{
    return $d ? date('d/m/Y', strtotime($d)) : '-';
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
        echo '<div class="alert alert-' . e($tipo) . '" role="alert">' . e($msg) . '</div>';
    }
}

function page_end(string $scriptExtra = ''): void
{
    echo '</div></div></div>';
    include __DIR__ . '/footer_base.php';
    echo $scriptExtra . '</body></html>';
}
