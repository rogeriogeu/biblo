<?php
// Uso (linha de comando):  php criar_admin.php "Nome" email@exemplo.com login
// A senha é pedida no terminal; nada é gravado no repositório.
// Variáveis opcionais: BIBLO_DB_HOST, BIBLO_DB_USER, BIBLO_DB_PASS, BIBLO_DB_NAME.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Execute apenas via linha de comando.');
}

$nome = $argv[1] ?? 'Administrador';
$email = $argv[2] ?? null;
$login = $argv[3] ?? 'admin';
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Uso: php criar_admin.php \"Nome\" email@valido.com login\n");
    exit(1);
}

fwrite(STDOUT, 'Senha: ');
$senha = trim((string) fgets(STDIN));
if ($senha === '') {
    fwrite(STDERR, "Senha vazia.\n");
    exit(1);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$host = getenv('BIBLO_DB_HOST') ?: 'localhost';
$dbUser = getenv('BIBLO_DB_USER') ?: 'root';
$dbPass = getenv('BIBLO_DB_PASS') !== false ? getenv('BIBLO_DB_PASS') : '';
$dbName = getenv('BIBLO_DB_NAME') ?: 'bd_saul';

try {
    $c = mysqli_connect($host, $dbUser, $dbPass, $dbName);
    mysqli_set_charset($c, 'utf8mb4');

    $existe = mysqli_query($c, "SHOW TABLES LIKE 'usuario'");
    if (mysqli_num_rows($existe) === 0) {
        throw new RuntimeException("Tabela 'usuario' não existe. Importe o schema.sql antes.");
    }

    // Garante espaço para o hash (bancos antigos tinham VARCHAR(20)).
    $col = mysqli_fetch_assoc(mysqli_query($c, "SHOW COLUMNS FROM usuario LIKE 'senha'"));
    if (!preg_match('/\((\d+)\)/', $col['Type'], $m) || (int) $m[1] < 255) {
        mysqli_query($c, 'ALTER TABLE usuario MODIFY senha VARCHAR(255) NOT NULL');
        echo "Coluna senha ajustada para VARCHAR(255).\n";
    }

    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $cargo = 'Administrador';
    $telefone = '';

    $stmt = mysqli_prepare($c, 'SELECT id FROM usuario WHERE login = ? OR email = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'ss', $login, $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id);
    $achou = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($achou) {
        $stmt = mysqli_prepare($c, 'UPDATE usuario SET nome = ?, cargo = ?, email = ?, login = ?, senha = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'sssssi', $nome, $cargo, $email, $login, $hash, $id);
        $acao = 'atualizado';
    } else {
        $stmt = mysqli_prepare($c, 'INSERT INTO usuario (nome, cargo, telefone, email, login, senha) VALUES (?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'ssssss', $nome, $cargo, $telefone, $email, $login, $hash);
        $acao = 'criado';
    }
    mysqli_stmt_execute($stmt);
    echo "Administrador $acao: $login\n";
} catch (Throwable $ex) {
    fwrite(STDERR, 'Erro: ' . $ex->getMessage() . "\n");
    exit(1);
}
