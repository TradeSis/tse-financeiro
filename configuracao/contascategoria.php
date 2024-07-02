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
                <h2 class="ts-tituloPrincipal">Categorias</h2>
            </div>
            <div class="col-6 col-lg-2">
                <!-- FLTROS -->
                <select class="form-select ts-input mt-1 pt-1" name="tipo" id="FiltroTipo">
                    <option value="<?php echo null ?>">
                        <?php echo "Tipo de Conta" ?>
                    </option>
                    <option value="CP">Contas à Pagar</option>
                    <option value="CR">Contas à Receber</option>
                </select>
            </div>
            <div class="col">
                <div class="input-group">
                    <input type="text" class="form-control ts-input" id="buscaContaCategoria" placeholder="Buscar por id ou nome">
                    <button class="btn btn-primary rounded" type="button" id="buscar"><i class="bi bi-search"></i></button>
                    <button type="button" class="ms-4 btn btn-success" data-bs-toggle="modal" data-bs-target="#inserirModal"><i class="bi bi-plus-square"></i>&nbsp Novo</button>
                </div>
            </div>

        </div>

        <div class="table mt-2 ts-divTabela ts-tableFiltros">
            <table class="table table-sm table-hover">
                <thead class="ts-headertabelafixo">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th class="col-1">Tipo</th>
                        <th class="col-1">Ação</th>
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
                        <h5 class="modal-title" id="exampleModalLabel">Inserir Categoria</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="inserirContasCategoria">
                            <div class="row">
                                <div class="col-md">
                                    <label class="form-label ts-label">nome Categoria</label>
                                    <input type="text" class="form-control ts-input" name="nomeCategoria" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label ts-label">tipo Categoria</label>
                                    <select class="form-select ts-input" name="tipo" required>
                                        <option value="CP">Contas à Pagar</option>
                                        <option value="CR">Contas à Receber</option>
                                    </select>
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
                        <h5 class="modal-title" id="exampleModalLabel">Alterar Categoria</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="alterarContasCategoria">
                            <div class="row">
                                <div class="col-md">
                                    <label class="form-label ts-label">nome Categoria</label>
                                    <input type="text" class="form-control ts-input" name="nomeCategoria" id="nomeCategoria">
                                    <input type="hidden" class="form-control ts-input" name="idCategoria" id="idCategoria">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label ts-label">tipo Categoria</label>
                                    <select class="form-select ts-input" name="tipo" id="tipo" required>
                                        <option value="CP">Contas à Pagar</option>
                                        <option value="CR">Contas à Receber</option>
                                    </select>
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
            buscar($("#buscaContaCategoria").val(), $("#FiltroTipo").val());

            function buscar(buscaContaCategoria, tipo) {
                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: '<?php echo URLROOT ?>/financeiro/database/contascategoria.php?operacao=filtrar',
                    beforeSend: function() {
                        $("#dados").html("Carregando...");
                    },
                    data: {
                        buscaContaCategoria: buscaContaCategoria,
                        tipo: tipo
                    },
                    success: function(msg) {

                        var json = JSON.parse(msg);
                        var linha = "";
                        for (var $i = 0; $i < json.length; $i++) {
                            var object = json[$i];

                            linha += "<tr>";
                            linha += "<td class='text-start'>" + object.idCategoria + "</td>";
                            linha += "<td class='text-start'>" + object.nomeCategoria + "</td>";
                            linha += "<td class='text-start'>" + object.tipo + "</td>";
                            linha += "<td>" + "<button type='button' class='btn btn-warning btn-sm' data-toggle='modal' data-target='#alterarmodal' data-idCategoria='" + object.idCategoria + "'><i class='bi bi-pencil-square'></i></button>"
                            linha += "</tr>";
                        }

                        $("#dados").html(linha);

                    }
                });
            }

            $("#buscar").click(function() {
                buscar($("#buscaContaCategoria").val(), $("#FiltroTipo").val(), );
            });
            $("#FiltroTipo").change(function() {
                buscar($("#buscaContaCategoria").val(), $("#FiltroTipo").val(),);
            });
            document.addEventListener("keypress", function(e) {
                if (e.key === "Enter") {
                    buscar($("#buscaContaCategoria").val(), $("#FiltroTipo").val(),);
                }
            });

            $(document).on('click', 'button[data-target="#alterarmodal"]', function() {
                var idCategoria = $(this).attr("data-idCategoria");

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '<?php echo URLROOT ?>/financeiro/database/contascategoria.php?operacao=buscar',
                    data: {
                        idCategoria: idCategoria
                    },
                    success: function(data) {
                        $('#idCategoria').val(data.idCategoria);
                        $('#nomeCategoria').val(data.nomeCategoria);
                        $('#tipo').val(data.tipo);

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
                $("#inserirContasCategoria").submit(function(event) {
                    event.preventDefault();
                    var formData = new FormData(this);
                    $.ajax({
                        url: "../database/contascategoria.php?operacao=inserir",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: refreshPage,
                    });
                });

                $("#alterarContasCategoria").submit(function(event) {
                    event.preventDefault();
                    var formData = new FormData(this);
                    $.ajax({
                        url: "../database/contascategoria.php?operacao=alterar",
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