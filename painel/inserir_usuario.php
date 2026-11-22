<?php
include 'conexao.php';
$name = $_POST['nome'];
$cargo = $_POST['cargo'];
$telefone = $_POST['telefone'];
$email = $_POST['email'];
$login = $_POST ['login'];
$senha = $_POST ['senha'];


$sql = "INSERT INTO usuario(nome, cargo, telefone, email, login, senha) VALUES ('$name','$cargo','$telefone','$email','$login', '$senha')";


$insert = mysqli_query($conexao, $sql);
header('Location: cad_usuarios.php?msg=1');
?>