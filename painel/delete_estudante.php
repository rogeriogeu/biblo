<?php

require_once 'auth.php';
include 'conexao.php';

$idestudante = $_POST['idestudante'];
$nome = $_POST['nome'];


$sql = "DELETE FROM estudante WHERE idestudante = $idestudante";
$delete = mysqli_query($conexao, $sql);

header('Location: listar_alunos.php?msg=2');

?>