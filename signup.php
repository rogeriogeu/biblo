<?php
$msg = '';
$ok = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = trim($_POST['mail'] ?? '');
    $senha = $_POST['password'] ?? '';
    $senha2 = $_POST['password2'] ?? '';

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL) || $senha === '') {
        $msg = 'Informe um e-mail válido e uma senha.';
    } elseif ($senha !== $senha2) {
        $msg = 'Senhas diferentes!';
    } else {
        include 'painel/conexao.php';
        $stmt = mysqli_prepare($conexao, 'SELECT id FROM usuario WHERE email = ? OR login = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'ss', $mail, $mail);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $existe = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);

        if ($existe) {
            $msg = 'E-mail já cadastrado.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $cargo = 'Usuário';
            $telefone = '';
            $stmt = mysqli_prepare($conexao, 'INSERT INTO usuario (nome, cargo, telefone, email, login, senha) VALUES (?, ?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'ssssss', $mail, $cargo, $telefone, $mail, $mail, $hash);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $msg = $ok ? 'Cadastro realizado! Faça login.' : 'Erro ao cadastrar.';
        }
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
      <div class="col-md-4" style="margin-top: 90px">
       
        <br><br>
       <form action="signup.php" method="POST">
        <?php if ($msg !== '') { ?>
          <div class="alert alert-<?php echo $ok ? 'success' : 'danger'; ?>"><?php echo htmlspecialchars($msg); ?></div>
        <?php } ?>
        <div class="form-group">
          <label for="exampleInputEmail1"  id="cor">E-Mail</label>
          <input type="email" class="form-control" id="exampleInputEmail1" name = "mail" aria-describedby="emailHelp">
          
        </div>
        <div class="form-group">
          <label for="exampleInputPassword1" id="cor">Senha</label>
          <input type="password" class="form-control" id="password" name = "password">
        </div>
        <div class="form-group">
          <label for="exampleInputPassword1"  id="cor">Confirme a Senha</label>
          <input type="password" class="form-control" id="confirm_password" name = "password2">
        </div>
        <br>
        <button type="submit" class="btn btn-block" style="background-color: #fb7e6d;color:#fff">Cadastrar</button>
      </form>
      <br>

      <center>
        <a href="index.php">Fazer Login</a>
      </center>

    </div>
    <div class="col-md-4"></div>
  </div>

</div>








</body>
</html>
<script type="text/javascript">
var password = document.getElementById("password")
  , confirm_password = document.getElementById("confirm_password");

function validatePassword(){
  if(password.value != confirm_password.value) {
    confirm_password.setCustomValidity("Senhas diferentes!");
  } else {
    confirm_password.setCustomValidity('');
  }
}

password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;
</script>