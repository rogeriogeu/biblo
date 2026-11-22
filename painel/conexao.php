<?php
// Configuração via variáveis de ambiente (opcional); padrões do Laragon.
$servername = getenv('BIBLO_DB_HOST') ?: 'localhost';
$user = getenv('BIBLO_DB_USER') ?: 'root';
$password = getenv('BIBLO_DB_PASS') !== false ? getenv('BIBLO_DB_PASS') : '';
$data = getenv('BIBLO_DB_NAME') ?: 'bd_saul';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexao = mysqli_connect($servername, $user, $password, $data);
    mysqli_set_charset($conexao, 'utf8mb4');
} catch (mysqli_sql_exception $ex) {
    http_response_code(500);
    exit('Não foi possível conectar ao banco "' . htmlspecialchars($data) . '": ' . htmlspecialchars($ex->getMessage()) . '. Verifique o MySQL e importe o schema.sql (veja o README).');
}
