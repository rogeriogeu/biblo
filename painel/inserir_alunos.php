<?php
require_once 'auth.php';
include 'conexao.php';
$name = $_POST['nome'];
$turma = $_POST['turma'];
$ensino = $_POST['ensino'];
$telefone = $_POST['telefone'];
$condicao = $_POST ['condicao'];


$sql = "INSERT INTO estudante(nome, turma, ensino, telefone, condicao) VALUES ('$name','$turma','$ensino','$telefone','$condicao')";


$insert = mysqli_query($conexao, $sql);
header('Location: cad_alunos.php?msg=1');
?>