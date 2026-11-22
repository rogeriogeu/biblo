<?php
require_once 'auth.php';
include 'conexao.php';

$id = (int) ($_POST['id'] ?? 0);
$nome = $_POST['nome'] ?? '';
$cargo = $_POST['cargo'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$email = $_POST['email'] ?? '';
$login = $_POST['login'] ?? '';
$senha = $_POST['senha'] ?? '';

if ($senha !== '') {
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conexao, 'UPDATE usuario SET nome = ?, cargo = ?, telefone = ?, email = ?, login = ?, senha = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'ssssssi', $nome, $cargo, $telefone, $email, $login, $hash, $id);
} else {
    $stmt = mysqli_prepare($conexao, 'UPDATE usuario SET nome = ?, cargo = ?, telefone = ?, email = ?, login = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'sssssi', $nome, $cargo, $telefone, $email, $login, $id);
}
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header('Location: cad_usuarios.php?msg=1');
exit;
