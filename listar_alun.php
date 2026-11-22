<?php include 'header.php' ?>

     <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
	<script type="text/javascript"> 
        $('#exampleModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var recipient = button.data('whatever') // Extract info from data-* attributes
  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  var modal = $(this)
  modal.find('.modal-title').text('New message to ' + recipient)
  modal.find('.modal-body input').val(recipient)
})
</script>

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
                                <h1 class="main-title float-left">Contatos</h1>
                                <ol class="breadcrumb float-right">
                                    <li class="breadcrumb-item">Contatos</li>
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                                <div class="clearfix"></div>
                            </div>
                        </div>



                        <!-- Main content -->

                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-12 col-xl-12">

                            <?php
                            if(isset($_GET['msg'])){
                                if($_GET['msg']==1){?>

                            <div class="alert alert-success" role="alert">
                                Alterado com sucesso!
                            </div>

                            <?php  } else{?>
                            <div class="alert alert-danger" role="alert">

                                Excluido com sucesso!
                            </div>
                            <?php }}
                                ?>

                            <div class="card mb-3">
                                <div class="card-header">
                                    <h3> Lista de Contatos</h3>

                                </div>

                                <div class="card-body">


                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <div class="card mb-3">

                                            <div class="card-body">
                                                <div class="table-responsive">


                                                    <table id="example" class="table table-bordered table-hover display"
                                                        style="width:100%">
                                                        <thead>
                                                            <tr>

                                                                <th>Nome</th>
                                                                <th>Turma</th>
                                                                <th>Modalidade de Ensino</th>
                                                                <th>Telefone</th>
                                                                <th>Situação</th>
                                                                <th>Ação</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                include 'conexao.php';
                                $sql = "SELECT * FROM estudante";
                                $search = mysqli_query ($conexao, $sql);

                                while($array = mysqli_fetch_array($search)){
                                    $idestudante = $array['idestudante'];
                                    $nome = $array['nome'];
                                    $turma = $array['turma'];
                                    $ensino = $array['ensino'];
                                    $telefone =$array['telefone'];
                                    $condicao = $array['condicao'];
                                    ?>
                                                            <tr>

                                                                <td><?php echo $nome ?></td>
                                                                <td><?php echo $turma ?></td>
                                                                <td><?php echo $ensino ?></td>
                                                                <td><?php echo $telefone ?></td>
                                                                <td><?php echo($condicao==0? '<span class="label label-success">Ativo</span>':' <span class="label label-warning">Inativo</span>'); ?>
                                                                </td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-warning fa fa-edit"
                                                                        title="editar" data-toggle="modal"
                                                                        data-target="#exampleModal"
                                                                        data-idestudante="<?php echo $idestudante?>"
                                                                        data-nome="<?php echo $nome?>"
                                                                        data-turma="<?php echo $turma?>"
                                                                        data-ensino="<?php echo $ensino?>"
                                                                        data-telefone="<?php echo $telefone?>"
                                                                        data-condicao="<?php echo $condicao?>"></button>
                                                             
                                            <button type="button" class="btn btn-danger btn fa fa-minus" title="deletar"></button>


                                                                </td>

                                                            </tr>

                                                            <!--- Aqui entra o modal---->


 <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">Open modal for @mdo</button>


<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New message</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Recipient:</label>
            <input type="text" class="form-control" id="recipient-name">
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Message:</label>
            <textarea class="form-control" id="message-text"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Send message</button>
      </div>
    </div>
  </div>
</div>                                       <!-- Aqui termina o Modal-->

                                                            <?php   }


                                
                                ?>

                                                </tbody>
                            <tfoot>
                             <!--   <tr>
                                    <th>Nome</th>
                                    <th>Turma</th>
                                    <th>Modalidade de Ensino</th>
                                    <th>Telefone</th>
                                    <th>Situação</th>
                                    <th>Ação</th>

                                </tr>-->
                                
                                                      
                             
                                
                                
                            </tfoot>

                        </table>
                    
                                                </div>
                                                </div>
            </div>


       
        
                                        <!--Table ende-->




                                


                    <!--Aqui entra o recipiente-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

   

                    <!-- Aqui termina o recipiente--->







                    </div>
                
            
        
        
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript">
        $("#telefone, #celular").mask("(00) 00000-0000"); //000 000 0000 eua
        </script>

        <!-- /.content -->
<script src="js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="assets/css/bootstrap.min.js"></script>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->


        <?php include 'footer.php'?>



        <script type="text/javascript">
        $(".telefone, #celular").mask("(00) 00000-0000"); //000 000 0000 eua
        </script>

        <!-- end row -->
        <?php include 'footer.php'?>


        <script type="text/javascript">
        $(document).ready(function() {
            $('#example').DataTable({
                "language": {

                    url: "js/Portuguese-Brasil.json"

                }
            });
        });
        </script>