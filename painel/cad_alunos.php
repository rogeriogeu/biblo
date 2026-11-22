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
                                    <h3> Cadastro de Alunos</h3>
                                                 <?php
                            if(isset($_GET['msg'])){
                                if($_GET['msg']==1){?>
                            
                                <div class="alert alert-success" role="alert">
                                    Aluno cadastrado com sucesso!
                                 </div>

                          <?php  } else{?>
                                <div class="alert alert-danger" role="alert">

                                    Aluno não foicaastrado!
                                </div>
                           <?php }}
                                ?>
                                </div>

                                  <div class="card-body">
                            
                        <form action = "inserir_alunos.php" method="POST">
                            <div class="row" style="margin-left: 2px;" style="margin-right:2px;">
                        <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-1">
                  <label for=" exampleInputEmail1">Nome</label>
                                <input type="text" class="form-control" id="nome"
                                    placeholder="Enter email" name="nome">
                            </div>

                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                  <label for=" exampleInputEmail1">Turma</label>
                                <input type="text" class="form-control" id="turma"
                                    placeholder="Enter email" name = "turma">
                            </div>


                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                  <label for=" exampleInputEmail1">Tipo de ensino</label>
                                <select class="form-control select-picker" name="ensino" id="ensino" required>
                                    <option selected>Fundamental I</option>
                                    <option>Fundamental II</option>
                                    <option>Ensino Médio</option>
                                    <option>Ensino Médio Integral</option>
                                    <option>EJA</option>
                                </select>
                            </div>

                          


                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12"">
                  <label for=" exampleInputEmail1">Telefone</label>
                                <input type="text" class="form-control" id="telefone"
                                    placeholder="Enter email"name="telefone">
                                   
                            </div>
                            
                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12"">
                  <label for=" exampleInputEmail1">Condição</label>
                                <select class="form-control select-picker" name="condicao" id="condicao">
                                    <option selected>Ativo</option>
                                    <option>Inativo</option>
                                   
                                </select>
                                <br>

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


 