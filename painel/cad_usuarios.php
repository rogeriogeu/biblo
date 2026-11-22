<?php include 'header.php' ?>

<body class="adminbody">

    <div id="main">

        <!-- top bar navigation -->
        <?php include 'topbar.php'?>
       
    <?php   

include 'menu.php'
?>

        <div class="content-page">

            <!-- Start content -->
            <div class="content">

                <div class="container-fluid">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="breadcrumb-holder">
                                <h1 class="main-title float-left">Alunos</h1>
                                <ol class="breadcrumb float-right">
                                    <li class="breadcrumb-item">Alunos</li>
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                          
                          
    <!-- Main content -->
     
                            <div class="col-xs-6 col-sm-16 col-md-6 col-lg-6 col-xl-12">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h3> Cadastro de usuários</h3>
                                    <?php
                            if(isset($_GET['msg'])){
                                if($_GET['msg']==1){?>
                            
                                <div class="alert alert-success" role="alert">
                                    Usuário cadastrado com sucesso!
                                 </div>

                          <?php  } else{?>
                                <div class="alert alert-danger" role="alert">

                                    Usuário não foicaastrado!
                                </div>
                           <?php }}
                                ?>
                                </div>

                                  <div class="card-body">
                            
                        <form action = "inserir_usuario.php" method="POST">
                            <div class="row" style="margin-left: 2px;" style="margin-right:2px;">
                        <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-1">
                  <label for=" exampleInputEmail1">Nome</label>
                                <input type="text" class="form-control" id="nome"
                                    placeholder="Nome" name="nome">
                            </div>

                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                  <label for=" exampleInputEmail1">Cargo</label>
                                <input type="text" class="form-control" id="cargo"
                                    placeholder="Cargo" name = "cargo">
                            </div>


                        <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                  <label for=" exampleInputEmail1">Telefone</label>
                                <input type="text" class="form-control" id="telefone"
                                    placeholder="Telefone" name = "telefone">
                            </div>

                          
                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12"">
                  <label for=" exampleInputEmail1">E-mail</label>
                                <input type="email" class="form-control" id="email"
                                    placeholder="Digite seu E-mail"name="email">
                                   
                            </div>
                            
                               <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12"">
                  <label for=" exampleInputEmail1">Login</label>
                                <input type="text" class="form-control" id="login"
                                    placeholder="Digite seu login"name="login">
                                   
                            </div>
                             <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12"">
                  <label for=" exampleInputEmail1">Senha</label>
                                <input type="text" class="form-control" id="senha"
                                    placeholder="Digite sua senha"name="senha">
                                   
                            </div>
                      
                        <!-- /.box-body -->
                        <br>
                       <br>
                       <br>
                    
                       <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12"">
                  <label for=" exampleInputEmail1"></label>
                            
                                  
                                   
                            </div>

                        <div class= "form-group col-md-12" clas="box-footer" style="text-align: right;">
                            <button class="btn btn-outline-primary" type="submit" id="btnGuardar"><i class="fa fa-save"></i>
                                Salvar</button>
                            <button class="btn btn-outline-danger" type="reset" type="button"><i
                                    class="fa fa-arrow-circle-left"></i> Cancelar</button>
                        
                        </div>
                            </div>
                        
                        </form>
                        </div>
                    
                </div>
                            </div><!-- end card-->
                       

    <!-- Main content -->
      
                        <!-- /.box-body -->
                        
                </div>

            </div>
        </div>
    </div>
        </div>
        <script type="text/javascript">

                        $("#telefone, #celular").mask("(00) 00000-0000"); //000 000 0000 eua
                    </script>
   
    <!-- /.content -->

                
<?php include 'footer.php'?>


 