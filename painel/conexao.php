<?php
$servername = 'localhost';
$user = 'root';
$password = '';
$data = 'bd_saul';

$conexao = mysqli_connect($servername, $user, $password, $data);
if ($conexao) {
    mysqli_set_charset($conexao, 'utf8mb4');
}
