<?php
// lucas 20052024 criado 

include_once(__DIR__ . '/../header.php');

?>

<!doctype html>
<html lang="pt-BR">

<head>

    <?php include_once ROOT . "/vendor/head_css.php"; ?>

</head>



<body>
    <div class="container-fluid">

        <div class="row">
            <!-- MENSAGENS/ALERTAS -->
        </div>
        <div class="row">
            <!-- BOTOES AUXILIARES -->
        </div>
        <div class="row align-items-center">
            <div class="col-3 text-start">
                <!-- TITULO -->
                <h2 class="ts-tituloPrincipal">Portador</h2>
            </div>

            <div class="col">
                <div class="input-group">
                    <input type="text" class="form-control ts-input" id="buscaPortador" placeholder="Buscar por id ou nome">
                    <button class="btn btn-primary rounded" type="button" id="buscar"><i class="bi bi-search"></i></button>
                    <button type="button" class="ms-4 btn btn-success" data-bs-toggle="modal" data-bs-target="#inserirModal"><i class="bi bi-plus-square"></i>&nbsp Novo</button>
                </div>
            </div>

        </div>

        <div class="table mt-2 ts-divTabela ts-tableFiltros">
            <table class="table table-sm table-hover">
                <thead class="ts-headertabelafixo">
                    <tr>
                        <th class='col-1'>#</th>
                        <th>Nome</th>
                        <th class='col-1'>Ação</th>
                    </tr>
                </thead>

                <tbody id='dados' class="fonteCorpo">

                </tbody>
            </table>
        </div>


        <!--------- INSERIR --------->
        <div class="modal" id="inserirModal" tabindex="-1" aria-labelledby="inserirModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Inserir Portador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="inserirPortador">
                            <div class="row ">
                                <div class="col-md">
                                    <label class="form-label ts-label">nome Portador</label>
                                    <input type="text" class="form-control ts-input" name="nomePortador" required>
                                </div>
                            </div>
                    </div><!--body-->
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Cadastrar</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <!--------- ALTERAR --------->
        <div class="modal" id="alterarmodal" tabindex="-1" aria-labelledby="alterarmodalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Alterar Portador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="alterarPortador">
                            <div class="row">
                                <div class="col-md">
                                    <label class="form-label ts-label">nome Portador</label>
                                    <input type="text" class="form-control ts-input" name="nomePortador" id="nomePortador">
                                    <input type="hidden" class="form-control ts-input" name="idPortador" id="idPortador">
                                </div>
                            </div>
                    </div><!--body-->
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Salvar</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- LOCAL PARA COLOCAR OS JS -->

        <?php include_once ROOT . "/vendor/footer_js.php"; ?>

        <!-- script para menu de filtros -->
        <script src="<?php echo URLROOT ?>/sistema/js/filtroTabela.js"></script>
        <script>
            buscar($("#buscaPortador").val());

            function buscar(buscaPortador) {
                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: '<?php echo URLROOT ?>/financeiro/database/portador.php?operacao=filtrar',
                    beforeSend: function() {
                        $("#dados").html("Carregando...");
                    },
                    data: {
                        buscaPortador: buscaPortador
                    },
                    success: function(msg) {

                        var json = JSON.parse(msg);
                        var linha = "";
                        for (var $i = 0; $i < json.length; $i++) {
                            var object = json[$i];

                            linha += "<tr>";
                            linha += "<td class='text-start'>" + object.idPortador + "</td>";
                            linha += "<td class='text-start'>" + object.nomePortador + "</td>";
                            linha += "<td>" + "<button type='button' class='btn btn-warning btn-sm' data-toggle='modal' data-target='#alterarmodal' data-idPortador='" + object.idPortador + "'><i class='bi bi-pencil-square'></i></button>"
                            linha += "</tr>";
                        }

                        $("#dados").html(linha);

                    }
                });
            }

            $("#buscar").click(function() {
                buscar($("#buscaPortador").val());
            });
            document.addEventListener("keypress", function(e) {
                if (e.key === "Enter") {
                    buscar($("#buscaPortador").val());
                }
            });

            $(document).on('click', 'button[data-target="#alterarmodal"]', function() {
                var idPortador = $(this).attr("data-idPortador");
                //alert(idCP)
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '<?php echo URLROOT ?>/financeiro/database/portador.php?operacao=buscar',
                    data: {
                        idPortador: idPortador
                    },
                    success: function(data) {
                        $('#idPortador').val(data.idPortador);
                        $('#nomePortador').val(data.nomePortador);

                        $('#alterarmodal').modal('show');
                    }
                });
            });



            var inserirModal = document.getElementById("inserirModal");

            var inserirBtn = document.querySelector("button[data-target='#inserirModal']");

            inserirBtn.onclick = function() {
                inserirModal.style.display = "block";
            };

            window.onclick = function(event) {
                if (event.target == inserirModal) {
                    inserirModal.style.display = "none";
                }
            };
        </script>

        <script>
            $(document).ready(function() {
                $("#inserirPortador").submit(function(event) {
                    event.preventDefault();
                    var formData = new FormData(this);
                    $.ajax({
                        url: "../database/portador.php?operacao=inserir",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: refreshPage,
                    });
                });

                $("#alterarPortador").submit(function(event) {
                    event.preventDefault();
                    var formData = new FormData(this);
                    $.ajax({
                        url: "../database/portador.php?operacao=alterar",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: refreshPage,
                    });
                });

                function refreshPage() {
                    window.location.reload();
                }
            });
        </script>

        <!-- LOCAL PARA COLOCAR OS JS -FIM -->


</body>

</html>