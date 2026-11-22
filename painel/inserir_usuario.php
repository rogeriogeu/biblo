<?php
require_once 'auth.php';
include 'conexao.php';

$name = $_POST['nome'] ?? '';
$cargo = $_POST['cargo'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$email = $_POST['email'] ?? '';
$login = $_POST['login'] ?? '';
$senha = $_POST['senha'] ?? '';

if ($senha === '') {
    header('Location: cad_usuarios.php?msg=0');
    exit;
}

$hash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conexao, 'INSERT INTO usuario (nome, cargo, telefone, email, login, senha) VALUES (?, ?, ?, ?, ?, ?)');
mysqli_stmt_bind_param($stmt, 'ssssss', $name, $cargo, $telefone, $email, $login, $hash);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header('Location: cad_usuarios.php?msg=1');
exit;
