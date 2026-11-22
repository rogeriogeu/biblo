<?php
// Uso (linha de comando, uma vez):
//   php criar_admin.php "Nome" email@exemplo.com login
// A senha é pedida no terminal e nunca é gravada no repositório.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Execute apenas via linha de comando.');
}

require __DIR__ . '/../painel/conexao.php';

$nome = $argv[1] ?? 'Administrador';
$email = $argv[2] ?? null;
$login = $argv[3] ?? 'admin';
if (!$email) {
    fwrite(STDERR, "Uso: php criar_admin.php \"Nome\" email login\n");
    exit(1);
}

fwrite(STDOUT, 'Senha: ');
$senha = trim((string) fgets(STDIN));
if ($senha === '') {
    fwrite(STDERR, "Senha vazia.\n");
    exit(1);
}

$hash = password_hash($senha, PASSWORD_DEFAULT);
$cargo = 'Administrador';
$telefone = '';

$stmt = mysqli_prepare($conexao, 'SELECT id FROM usuario WHERE login = ? OR email = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'ss', $login, $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id);
if (mysqli_stmt_fetch($stmt)) {
    mysqli_stmt_close($stmt);
    $stmt = mysqli_prepare($conexao, 'UPDATE usuario SET nome = ?, cargo = ?, email = ?, login = ?, senha = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'sssssi', $nome, $cargo, $email, $login, $hash, $id);
    $acao = 'atualizado';
} else {
    mysqli_stmt_close($stmt);
    $stmt = mysqli_prepare($conexao, 'INSERT INTO usuario (nome, cargo, telefone, email, login, senha) VALUES (?, ?, ?, ?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'ssssss', $nome, $cargo, $telefone, $email, $login, $hash);
    $acao = 'criado';
}
mysqli_stmt_execute($stmt);
echo "Administrador $acao: $login\n";
