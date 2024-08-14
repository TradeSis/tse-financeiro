<?php
// lucas 20052024 - id747 Contas a Pagar e Receber - Progress e Movimentacoes

include_once(__DIR__ . '/../header.php');
include_once(ROOT . '/cadastros/database/pessoas.php');
include_once(__DIR__ . '/../database/contascategoria.php');
include_once(__DIR__ . '/../database/portador.php');

$pessoas = buscarPessoa();
$contacategorias = buscaContaCategoria();
$portadores = buscaPortador();
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
        <div class="row d-flex align-items-center justify-content-center mt-1 pt-1 ">

            <div class="col-2 col-lg-1 order-lg-1">
                <button class="btn btn-outline-secondary ts-btnFiltros" type="button"><i class="bi bi-funnel"></i></button>
            </div>

            <div class="col-2 col-lg-2 order-lg-2" id="filtroh6">
                <h2 class="ts-tituloPrincipal">Recebimentos</h2>
                <h6 style="font-size: 10px;font-style:italic;text-align:left;"></h6>
            </div>

            <div class="col-2 col-lg-2 order-lg-2 pt-3">
                <a class="btn btn-sm btn-primary" href="contasreceber.php" role="button">Contas à Receber</a>
            </div>

            <div class="col-6 col-lg-2 order-lg-3">
                <!-- FLTROS -->
                <select class="form-select ts-input mt-1 pt-1" name="idPortador" id="FiltroPortador">
                    <option value="<?php echo null ?>">
                        <?php echo "Todos Portadores" ?>
                    </option>
                    <?php
                    foreach ($portadores as $portador) {
                    ?>
                        <option <?php
                                ?> value="<?php echo $portador['idPortador'] ?>">
                            <?php echo $portador['nomePortador'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-1 col-lg-1 order-lg-4">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#periodoModal"><i class="bi bi-calendar3"></i></button>
            </div>

            <div class="col-11 col-lg-4 order-lg-5">
                <div class="input-group">
                    <input type="text" class="form-control ts-input" id="buscarCrPagamento" placeholder="Buscar por historico">
                    <button class="btn btn-primary rounded" type="button" id="buscar"><i class="bi bi-search"></i></button>
                </div>
            </div>

        </div>

        <div class="ts-menuFiltros mt-2 px-3">
            <label>Filtrar por:</label>
            <div class="col-12"> <!-- ABERTO/FECHADO -->
                <form class="d-flex" action="" method="post">
                    <select class="form-select" name="idCategoria" id="FiltroCategoria">
                        <option value="<?php echo null ?>">
                            <?php echo "Categorias" ?>
                        </option>
                        <?php
                        foreach ($contacategorias  as $contacategoria) {
                        ?>
                            <option <?php
                                    ?> value="<?php echo $contacategoria['idCategoria'] ?>">
                                <?php echo $contacategoria['nomeCategoria'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </form>
            </div>

            <div class="col-sm text-end mt-2">
                <a onClick="limpar()" role=" button" class="btn btn-sm bg-info text-white">Limpar</a>
            </div>
        </div>

        <!--------- MODAL GERENCIAR RECEBIMENTO --------->
        <?php include_once 'gerenciarrecebimentos_modal.php' ?>

        <!--------- FILTRO PERIODO --------->
        <?php include_once 'filtroPeriodo_modal.php' ?>

        <!--------- EXCLUIR CONTAS RECEBER PAGAMENTO --------->
        <?php include_once 'excluirrecebimentos_modal.php' ?>

        <div class="table mt-2 ts-divTabela ts-tableFiltros text-center">
            <table class="table table-sm table-hover">
                <thead class="ts-headertabelafixo">
                    <tr>
                        <th class="text-start">Documento</th>
                        <th class="text-start">Cliente</th>
                        <th class="text-start">dtPagamento</th>
                        <th class="text-start">Historico</th>
                        <th class="text-start">Categoria</th>
                        <th class="text-start">Portador</th>
                        <th class="text-end">Acrescimos</th>
                        <th class="text-end">Descontos</th>
                        <th class="text-end">Valor Pago</th>
                    </tr>
                </thead>

                <tbody id='dados' class="fonteCorpo">

                </tbody>
            </table>
        </div>


        <!-- LOCAL PARA COLOCAR OS JS -->

        <?php include_once ROOT . "/vendor/footer_js.php"; ?>

        <!-- script para menu de filtros -->
        <script src="<?php echo URLROOT ?>/sistema/js/filtroTabela.js"></script>
        <script>
            // Ao iniciar o programa, inseri a data no modal "periodoModal" e aciona a funcao buscar. 
            $(document).ready(function() {
                var data = new Date(),
                    dia = data.getDate().toString(),
                    diaF = (dia.length == 1) ? '0' + dia : dia,
                    mes = (data.getMonth() + 1).toString(), //+1 pois no getMonth Janeiro come�a com zero.
                    mesF = (mes.length == 1) ? '0' + mes : mes,
                    anoF = data.getFullYear();
                dataAtual = anoF + "-" + mesF + "-" + diaF;
                primeirodiadomes = anoF + "-" + mesF + "-" + "01";

                const FiltroPeriodoInicio = document.getElementById("FiltroPeriodoInicio");
                FiltroPeriodoInicio.value = primeirodiadomes;

                const FiltroPeriodoFim = document.getElementById("FiltroPeriodoFim");
                FiltroPeriodoFim.value = dataAtual;

                buscar($("#buscarCrPagamento").val(), $("#FiltroPortador").val(), $("#FiltroCategoria").val(), $("#FiltroPeriodoInicio").val(), $("#FiltroPeriodoFim").val());
            });

            function limpar() {
                window.location.reload();
            }

            function buscar(buscarCrPagamento, idPortador, idCategoria, PeriodoInicio, PeriodoFim) {
                var h6Element = $("#filtroh6 h6");
                var text = "";

                if (PeriodoInicio !== "") {
                    text += formatDate(PeriodoInicio);

                }

                if (PeriodoFim !== "") {
                    text += PeriodoFim !== null ? " até " + formatDate(PeriodoFim) : " ";
                }

                h6Element.html(text);

                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: '<?php echo URLROOT ?>/financeiro/database/contasreceberpagamento.php?operacao=filtrar',
                    beforeSend: function() {
                        $("#dados").html("Carregando...");
                    },
                    data: {
                        buscar: buscarCrPagamento,
                        buscaPortador: idPortador,
                        buscaCategoria: idCategoria,
                        PeriodoInicio: PeriodoInicio,
                        PeriodoFim: PeriodoFim,
                    },
                    success: function(msg) {
                        //alert(msg)
                        var json = JSON.parse(msg);
                        var linha = "";
                        for (var $i = 0; $i < json.length; $i++) {
                            var object = json[$i];

                            var dataPagamentoFormatada = formatDate(object.dtPagamento);

                            linha += "<tr>";
                            linha += "<td class='text-start ts-click' data-idCR='" + object.idCR + "'>" + object.documento + "</td>";
                            linha += "<td class='text-start ts-click' data-idCR='" + object.idCR + "'>" + object.nomePessoa + "</td>";
                            linha += "<td class='text-start ts-click' data-idCR='" + object.idCR + "'>" + dataPagamentoFormatada + "</td>";
                            linha += "<td class='text-start ts-click' data-idCR='" + object.idCR + "'>" + object.historico + "</td>";
                            linha += "<td class='text-start ts-click' data-idCR='" + object.idCR + "'>" + object.nomeCategoria + "</td>";
                            linha += "<td class='text-start ts-click' data-idCR='" + object.idCR + "'>" + object.nomePortador + "</td>";
                            linha += "<td class='text-end ts-click' data-idCR='" + object.idCR + "'>" + object.acrescimos + "</td>";
                            linha += "<td class='text-end ts-click' data-idCR='" + object.idCR + "'>" + object.descontos + "</td>";
                            linha += "<td class='text-end ts-click' data-idCR='" + object.idCR + "'>" + (object.valorPago !== null ? object.valorPago.toLocaleString('pt-br', {
                                minimumFractionDigits: 2
                            }) : "-") + "</td>";

                            linha += "</tr>";
                        }

                        $("#dados").html(linha);

                    }
                });
            }

            $("#buscar").click(function() {
                buscar($("#buscarCrPagamento").val(), $("#FiltroPortador").val(), $("#FiltroCategoria").val(), $("#FiltroPeriodoInicio").val(), $("#FiltroPeriodoFim").val());
            });

            $("#FiltroPortador").change(function() {
                buscar($("#buscarCrPagamento").val(), $("#FiltroPortador").val(), $("#FiltroCategoria").val(), $("#FiltroPeriodoInicio").val(), $("#FiltroPeriodoFim").val());
            });

            $("#FiltroCategoria").change(function() {
                buscar($("#buscarCrPagamento").val(), $("#FiltroPortador").val(), $("#FiltroCategoria").val(), $("#FiltroPeriodoInicio").val(), $("#FiltroPeriodoFim").val());
            });

            $("#filtrarButton").click(function() {
                buscar($("#buscarCrPagamento").val(), $("#FiltroPortador").val(), $("#FiltroCategoria").val(), $("#FiltroPeriodoInicio").val(), $("#FiltroPeriodoFim").val());
                $('#periodoModal').modal('hide');
            });

            document.addEventListener("keypress", function(e) {
                if (e.key === "Enter") {
                    buscar($("#buscarCrPagamento").val(), $("#FiltroPortador").val(), $("#FiltroCategoria").val(), $("#FiltroPeriodoInicio").val(), $("#FiltroPeriodoFim").val());
                }
            });

            function limparPeriodo() {
                window.location.reload();
            }

            //MODAL GERENCIAR RECEBIMENTO
            $(document).on('click', '.ts-click', function() {
                var idCR = $(this).attr("data-idCR");

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '<?php echo URLROOT ?>/financeiro/database/contasreceberpagamento.php?operacao=buscarContasReceber',
                    data: {
                        idCR: idCR
                    },
                    success: function(data) {
                        //console.log(JSON.stringify(data, null, 2));
                        var linha = "";
                        for (var $i = 0; $i < data.length; $i++) {
                            var object = data[$i];

                            //titulo modal gerenciar
                            var titulo = $("#titulomodalgerenciar");
                            titulo.html('Gerenciar Recebimento: ' + object.documento);

                            //texto valorOriginal
                            vvalorOriginal = (object.valorOriginal !== null ? object.valorOriginal.toLocaleString('pt-br', {
                                minimumFractionDigits: 2
                            }) : "-")
                            var texto = $("#textovalorOriginal");
                            texto.html('Valor: ' + vvalorOriginal);

                            //texto saldo
                            vsaldo = (object.saldo !== null ? object.saldo.toLocaleString('pt-br', {
                                minimumFractionDigits: 2
                            }) : "-");
                            var texto = $("#textosaldo");
                            texto.html('Saldo: ' + vsaldo);

                            situacao = '';
                            //situacao Parcial
                            if (object.saldo != 0) {
                                situacao = 'Parcial'
                            }

                            //situacao Liquidado
                            if (object.saldo == 0) {
                                situacao = 'Liquidado'
                            }
                            var texto = $("#textoGRsituacao");
                            texto.html('Situacao: ' + situacao);

                            var dataPagamentoFormatada = formatDate(object.dtPagamento);

                            linha += "<tr>";
                            linha += "<td class='text-start'>" + object.nomePessoa + "</td>";
                            linha += "<td>" + dataPagamentoFormatada + "</td>";
                            linha += "<td class='text-start'>" + object.historico + "</td>";
                            linha += "<td class='text-start'>" + object.nomeCategoria + "</td>";
                            linha += "<td class='text-start'>" + object.nomePortador + "</td>";
                            linha += "<td class='text-end'>" + object.acrescimos + "</td>";
                            linha += "<td class='text-end'>" + object.descontos + "</td>";
                            linha += "<td class='text-end'>" + (object.valorPago !== null ? object.valorPago.toLocaleString('pt-br', {
                                minimumFractionDigits: 2
                            }) : "-") + "</td>";
                            linha += "<td>" + "<button type='button' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#excluirCrPagamentomodal' data-idCR='" + object.idCR + "' data-idCrPgto='" + object.idCrPgto + "'><i class='bi bi-trash3'></i></button>"
                            linha += "</tr>";
                        }

                        $("#dados_gerenciarRecebimento").html(linha);
                        $('#gerenciarrecebimentomodal').modal('show')
                    }
                });
            });

            //MODAL EXCLUIR CONTAS PAGAR PAGAMENTO
            $(document).on('click', 'button[data-target="#excluirCrPagamentomodal"]', function() {
                var idCR = $(this).attr("data-idCR");
                var idCrPgto = $(this).attr("data-idCrPgto");

                $('#excluir_idCR').val(idCR);
                $('#excluir_idCrPgto').val(idCrPgto);

                $('#excluirCrPagamentomodal').modal('show');
            });
        </script>
        <script>
            $(document).ready(function() {

                $("#excluirFormCrPagamento").submit(function(event) {
                    event.preventDefault();
                    var formData = new FormData(this);
                    $.ajax({
                        url: "../database/contasreceberpagamento.php?operacao=excluir",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: refreshPage,
                    });
                });

            });
            // FORMATAR DATAS
            function formatDate(dateString) {
                if (dateString !== null && !isNaN(new Date(dateString))) {
                    var date = new Date(dateString);
                    var day = date.getUTCDate().toString().padStart(2, '0');
                    var month = (date.getUTCMonth() + 1).toString().padStart(2, '0');
                    var year = date.getUTCFullYear().toString().padStart(4, '0');
                    return day + "/" + month + "/" + year;
                }
                return "00/00/0000";
            }

            function refreshPage() {
                window.location.reload();
            }
        </script>

        <!-- LOCAL PARA COLOCAR OS JS -FIM -->


</body>

</html>