<?php include 'header.php' ?>

<script>
    //Aqui estamos atribuindo os datas
    $(function(){
$('#editarModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var recipient = button.data('idestudante') 
  var recipient2 = button.data('nome')
  var recipient3 = button.data('turma')
  var recipient4 = button.data('ensino')
  var recipient5 = button.data('telefone')
  var recipient6 = button.data('condicao')

  
  var modal = $(this)
  //vai procurar dentro do modal esses itens aqui:
  modal.find('.modal-title').text('Modificar cliente código: ' + recipient)
  modal.find('#recipient-idestudante').val(recipient)
  modal.find('#recipient-nome').val(recipient2)
  modal.find('#recipient-turma').val(recipient3)
  modal.find('#recipient-ensino').val(recipient4)
  modal.find('#recipient-telefone').val(recipient5)
  modal.find('#recipient-condicao').val(recipient6)
});
});

</script>

<script>
    $(function(){
$('#deleteModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var recipient = button.data('id-delete') 
  var recipient2 = button.data('name-delete')
  

  //vai procurar dentro do modal esses itens aqui:
  var modal = $(this)
  modal.find('.modal-title').text('Deletar cliente código: ' + recipient)
  modal.find('#recipient-id-delete').val(recipient)
  modal.find('#recipient-name-delete').val(recipient2)
  
});
});

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
                                <h1 class="main-title float-left">Alunos</h1>
                                <ol class="breadcrumb float-right">
                                    <li class="breadcrumb-item">Alunos</li>
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
                                    <h3> Listagem de Alunos</h3>
                                   
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
                                                                <td><?php echo($condicao==0? '<span class="badge badge-success">Ativo</span>':' <span class="badge badge-secondary">Inativo</span>'); ?>
                                                                </td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-warning"
                                                                        title="editar" data-toggle="modal"
                                                                        data-target="#editarModal"
                                                                        data-idestudante="<?php echo $idestudante?>"
                                                                        data-nome="<?php echo $nome?>"
                                                                        data-turma="<?php echo $turma?>"
                                                                        data-ensino="<?php echo $ensino?>"
                                                                        data-telefone="<?php echo $telefone?>"
                                                                        data-condicao="<?php echo $condicao?>"><i class="fas fa-user-edit"></i></button>
                                                             
                                                                    <button type="button" class="btn btn-danger" title="deletar"                                              
                                                                        data-toggle="modal" data-target="#deleteModal"
                                                                        data-id-delete="<?php echo $idestudante?>"
                                                                        data-name-delete="<?php echo $nome?>"> <i class="fas fa-user-minus"></i></button>


                                                                </td>

                                                            </tr>
                                                <!--Editar Modal -->

                <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                 <div class="modal-content">
         <!-- <div class="modal-header">
            <h5 class="modal-title" id="exampleModal">New message</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>-->
          <div class="modal-body">
            <form action="edit_aluno.php" method="POST">
              <div class="form-group">
               <label for="message-text" class="col-form-label">Código:</label>
               <input type="text" class="form-control" id="recipient-idestudante" readonly name="idestudante">
                <label for="message-text" class="col-form-label">Nome:</label>
                <input type="text" class="form-control" id="recipient-nome" name="nome">
                <label for="message-text" class="col-form-label">Turma:</label>
                <input type="text" class="form-control" id="recipient-turma" name="turma">
                <label for="message-text" class="col-form-label">Ensino:</label>
                
                <select for="message-text" class="form-control" id="recipient-ensino" name="ensino">
                     <option>Fundamental I</option>
                     <option>Fundamental II</option>
                     <option>Ensino Médio</option>
                     <option>Ensino Médio Integral</option>
                     <option>EJA</option>
                </select>
                <label for="message-text" class="col-form-label">Telefone:</label>
                <input type="text" class="form-control telefone" id="recipient-telefone"name="telefone">
                
                <label for="message-text" class="col-form-label">Situação (0-Ativo, 1-Inativo):</label>
                <select  type="int" class="form-control" id="recipient-condicao"name="condicao">
                    <option>0</option>
                    <option>1</option>
                    </select>

              </div>
          
       
                </div>
                <div class="modal-footer">
                 <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Fechar</button>
                 <button type="submit" class="btn btn-outline-primary">Editar</button>
            </div>
                </form>
                </div>
                </div>
                </div>


<!-- delete modal para apagar um registro -->

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New message</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="delete_estudante.php" method="POST">
          <div class="form-group">
            <label for="message-text" class="col-form-label">Código:</label>
            <input type="text" class="form-control" id="recipient-id-delete" readonly name="idestudante">
            <label for="message-text" class="col-form-label">Nome:</label>
            <input type="text" class="form-control" id="recipient-name-delete" readonly name="nome">
           </div>
          
       
            </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Fechar</button>
                 <button type="submit" class="btn btn-outline-danger">Deletar</button>
                            </div>
                                </form>
                                    </div>
                                        </div>
                                            </div>





                                              <?php }  ?>
                                            </tbody>

                                        </table>
                                    </div>
                                    <!-- end table-responsive-->

                                </div>
                                <!-- end card-body-->

                            </div>
                            <!-- end card-->

                        </div>




                                       
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        

    


<script type="text/javascript">

                        $(".telefone, #celular").mask("(00) 00000-0000"); //000 000 0000 eua
                    </script>

                    <!-- end row -->
<?php include 'footer.php'?>


 <script type = "text/javascript">

$(document).ready(function() {
    $('#example').DataTable( {
        "language": {

           url:"js/Portuguese-Brasil.json"
          
        }
    } );
} );


</script>