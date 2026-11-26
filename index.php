<?php
session_start();
if (!empty($_SESSION['usuario_id'])) {
    header('Location: painel/index.php');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identificador = trim($_POST['identificador'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($identificador === '' || $senha === '') {
        $erro = 'Informe e-mail/login e senha.';
    } else {
        include 'painel/conexao.php';
        $stmt = mysqli_prepare($conexao, 'SELECT id, nome, cargo, senha FROM usuario WHERE email = ? OR login = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'ss', $identificador, $identificador);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $uid, $unome, $ucargo, $uhash);
        $achou = mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($achou && password_verify($senha, $uhash)) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $uid;
            $_SESSION['usuario_nome'] = $unome;
            $_SESSION['usuario_cargo'] = $ucargo;
            header('Location: painel/index.php');
            exit;
        }
        $erro = 'Login ou senha inválidos.';
    }
}
?>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">


  <title>Login - Dashboard</title>
  <style type="text/css">
    #cor {
      color:#fff;
    }
  </style>
</head>
<body style="background-color: #2a3542">
  <div class="container">
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-4" style="margin-top: 120px">
        
        <br><br>
       <form action="index.php" method="POST">
        <?php if ($erro !== '') { ?>
          
        <?php } ?>
        <div class="form-group">
          <label for="exampleInputEmail1" id="cor">E-Mail ou Login</label>
          <input type="text" class="form-control" id="exampleInputEmail1" name="identificador" required autofocus>
          
        </div>
        <div class="form-group">
          <label for="exampleInputPassword1" id="cor">Senha</label>
          <input type="password" class="form-control" id="exampleInputPassword1" name="senha" required>
        </div>
        <br>
        <button type="submit" class="btn btn-block" style="background-color: #fb7e6d;color:#fff">Entrar</button>
      </form>
      <br>

    </div>
    <div class="col-md-4"></div>
  </div>

</div>








<script src="painel/assets/js/jquery.min.js"></script>
<script src="painel/assets/js/bootstrap.bundle.min.js"></script>
<link href="painel/assets/font-awesome/css/all.css" rel="stylesheet">
<?php require_once __DIR__ . '/painel/modal.php'; echo modal_render($erro !== '' ? ['danger', $erro] : null); ?>
</body>
</html>