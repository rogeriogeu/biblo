<?php
include 'conexao.php';
$idestudante = $_POST['idestudante'];
$nome= $_POST['nome'];
$turma = $_POST['turma'];
$ensino = $_POST['ensino'];
$telefone = $_POST['telefone'];
$condicao = $_POST['condicao'];

$sql = "UPDATE `estudante` SET `nome`='$nome',`turma`='$turma',`ensino`='$ensino',`telefone`='$telefone',`condicao`='$condicao' WHERE idestudante = $idestudante";
$INSERT = mysqli_query($conexao, $sql);
header('location: listar_alunos.php?msg=1');

?>