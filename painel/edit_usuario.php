<?php
include 'conexao.php';
$id = $_POST['id'];
$nome= $_POST['nome'];
$cargo = $_POST['cargo'];
$telefone = $_POST['telefone'];
$email = $_POST['email'];
$login = $_POST['login'];
$senha = $_POST['senha'];

$sql = "UPDATE `usuariio` SET `nome`='$nome',`cargo`='$cargo',`telefone`='$telefone',`email`='$email',`login`='$login', `senha`='$senha' WHERE id = $id";
$INSERT = mysqli_query($conexao, $sql);
header('location: cad_usuario.php?msg=1');

?>