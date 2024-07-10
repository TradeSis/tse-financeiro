<?php
// lucas 20052024 - id747 Contas a Pagar e Receber - Progress e Movimentacoes
// lucas 11102023 novo padrao
// Gabriel 22092023 

include_once(__DIR__ . '/../header.php');
include_once(ROOT . '/cadastros/database/pessoas.php');
include_once(__DIR__ . '/../database/contascategoria.php');
include_once(__DIR__ . '/../database/portador.php');

$pessoas = buscarPessoa();
$contacategorias = buscaContaCategoria(null, "CP");
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
                <h2 class="ts-tituloPrincipal">Contas à Pagar</h2>
                
                <h6 style="font-size: 10px;font-style:italic;text-align:left;"></h6>
            </div>

            <div class="col-2 col-lg-1 order-lg-3 pt-3">
                <a class="btn btn-sm btn-primary" href="gerenciarpagamentos.php" role="button">Pagamentos</a>
            </div>

            <div class="col-6 col-lg-2 order-lg-4">
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

            <div class="col-1 col-lg-1 order-lg-5">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#periodoModal"><i class="bi bi-calendar3"></i></button>
            </div>

            <div class="col-11 col-lg-5 order-lg-5">
                <div class="input-group">
                    <input type="text" class="form-control ts-input" id="buscaContasPagar" placeholder="Buscar por historico">
                    <button class="btn btn-primary rounded" type="button" id="buscar"><i class="bi bi-search"></i></button>
                    <button type="button" class="ms-4 btn btn-success" data-bs-toggle="modal" data-bs-target="#inserirModal"><i class="bi bi-plus-square"></i>&nbsp Novo</button>
                </div>
            </div>

        </div>

        <div class="ts-menuFiltros mt-2 px-3">
            <label>Filtrar por:</label>

            <div class="col-12"> <!-- ABERTO/FECHADO -->
                <span class="" id="datainiciomes"></span>
                <span class="" id="dataatualmes"></span>
                <form class="d-flex" action="" method="post">
                    <select class="form-select" name="filtrosituacao" id="FiltroSituacao">
                        <option value="emaberto">Em aberto</option>
                        <option value="emitida">Emitida</option>
                        <option value="pagas">Pagas</option>
                    </select>
                </form>
            </div>
            <div class="col-12">
                <form class="d-flex" action="" method="post">
                    <select class="form-select" name="idCategoria" id="FiltroCategoria">
                        <option value="<?php echo null ?>">
                            <?php echo "Categorias" ?>
                        </option>
                        <?php
                        foreach ($contacategorias  as $contacategoria) {
                        ?>
                            <option <?php
                                    ?> value="<?php echo $contacategoria['idCategoria'] ?>" namespace="oi">
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

        <div class="table mt-2 ts-divTabela ts-tableFiltros">
            <table class="table table-sm table-hover">
                <thead class="ts-headertabelafixo">
                    <tr>
                        <th>Fornecedor</th>
                        <th>Historico</th>
                        <th>documento</th>
                        <th>dtCompetencia</th>
                        <th>Categoria</th>
                        <th>Portador</th>
                        <th>Vencimento</th>
                        <th>Liquidacao</th>
                        <th>Valor</th>
                        <th>Situacao</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody id='dados' class="fonteCorpo">

                </tbody>
            </table>
        </div>

        <!--------- FILTRO PERIODO --------->
        <?php include_once 'filtroPeriodo_modal.php' ?>

        <!--------- INSERIR --------->
        <div class="modal" id="inserirModal" tabindex="-1" aria-labelledby="inserirModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Inserir Conta à Pagar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <form method="post" id="inserirFormContasPagar">
                            <div class="row">
                                <div class="col-md">
                                    <label class="form-label ts-label">Fornecedor</label>
                                    <select class="form-select ts-input" name="idPessoaFornecedor" autocomplete="off" required>
                                        <?php
                                        foreach ($pessoas as $pessoa) {
                                        ?>
                                            <option value="<?php echo $pessoa['idPessoa'] ?>">
                                                <?php echo $pessoa['nomeFantasia'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div><!--fim row-->
                            <div class="row mt-2">
                                <div class="col-md">
                                    <label class="form-label ts-label">dtCompetencia *</label>
                                    <input type="date" class="form-control ts-input" name="dtCompetencia" id="inserir_dtCompetencia" required>
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">dtVencimento *</label>
                                    <input type="date" class="form-control ts-input" name="dtVencimento" required>
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">documento</label>
                                    <input type="text" class="form-control ts-input" name="documento">
                                </div>
                            </div><!--fim row-->
                            <div class="row mt-2">
                                <label class="form-label ts-label">Historico</label>
                                <div class="col-md mt-3">
                                    <textarea class="ts-textareaResponsivo" name="historico" rows="5"></textarea>
                                </div>
                            </div><!--fim row-->
                            <div class="row mt-3">
                                <div class="col-md">
                                    <label class="form-label ts-label">Categoria</label>
                                    <select class="form-select ts-input" name="idCategoria" autocomplete="off" required>
                                        <?php
                                        foreach ($contacategorias as $contacategoria) {
                                        ?>
                                            <option value="<?php echo $contacategoria['idCategoria'] ?>">
                                                <?php echo $contacategoria['nomeCategoria'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">Portador</label>
                                    <select class="form-select ts-input" name="idPortador" autocomplete="off" required>
                                        <?php
                                        foreach ($portadores as $portador) {
                                        ?>
                                            <option value="<?php echo $portador['idPortador'] ?>">
                                                <?php echo $portador['nomePortador'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">valorOriginal</label>
                                    <input type="text" class="form-control ts-input formatValorDecimal" name="valorOriginal" required>
                                </div>
                            </div><!--fim row-->
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
                        <div class="col align-self-start pl-0">
                            <div class="col-md">
                                <h5 class="modal-title">Alterar Conta � Pagar</h5>
                            </div>
                            <div class="col-md">
                                <h7 class="modal-title" id="textoalterarsituacao"></h7>
                            </div>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <form method="post" id="alterarFormContasPagar">
                            <div class="row">
                                <div class="col-md d-none">
                                    <input type="hidden" class="form-control ts-input" name="idCP" id="idCP">
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">Fornecedor</label>
                                    <select class="form-select ts-input" name="idPessoaFornecedor" id="idPessoaFornecedor">
                                        <?php
                                        foreach ($pessoas as $pessoa) {
                                        ?>
                                            <option value="<?php echo $pessoa['idPessoa'] ?>">
                                                <?php echo $pessoa['nomePessoa'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div><!--fim row-->
                            <div class="row mt-2">
                                <div class="col-md">
                                    <label class="form-label ts-label">dtCompetencia</label>
                                    <input type="date" class="form-control ts-input" name="dtCompetencia" id="dtCompetencia">
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">dtVencimento</label>
                                    <input type="date" class="form-control ts-input" name="dtVencimento" id="dtVencimento">
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">documento</label>
                                    <input type="text" class="form-control ts-input" name="documento" id="documento">
                                </div>
                            </div><!--fim row-->
                            <div class="row mt-2">
                                <label class="form-label ts-label">Historico</label>
                                <div class="col-md mt-3">
                                    <textarea class="ts-textareaResponsivo" name="historico" id="historico" rows="5"></textarea>
                                </div>
                            </div><!--fim row-->
                            <div class="row mt-3">
                                <div class="col-md">
                                    <label class="form-label ts-label">Categoria</label>
                                    <select class="form-select ts-input" name="idCategoria" id="idCategoria">
                                        <?php
                                        foreach ($contacategorias as $contacategoria) {
                                        ?>
                                            <option value="<?php echo $contacategoria['idCategoria'] ?>">
                                                <?php echo $contacategoria['nomeCategoria'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">Portador</label>
                                    <select class="form-select ts-input" name="idPortador" id="idPortador">
                                        <?php
                                        foreach ($portadores as $portador) {
                                        ?>
                                            <option value="<?php echo $portador['idPortador'] ?>">
                                                <?php echo $portador['nomePortador'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">valorOriginal</label>
                                    <input type="text" class="form-control ts-input formatValorDecimal" name="valorOriginal" id="valorOriginal" required>
                                </div>
                            </div><!--fim row-->
                    </div><!--body-->
                    <div class="modal-footer">
                        <div class="col align-self-start mt-3">
                            <h6 class="modal-title" id="textoalterarsaldo"></h6>
                        </div>
                        <button type="submit" class="btn btn-success" id="btn-salvarAlteracao">Salvar</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <!--------- BAIXAR PAGAMENTO --------->
        <div class="modal" id="baixarpagamentomodal" tabindex="-1" aria-labelledby="baixarpagamentomodalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="titulomodalbaixarpagamento"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <form method="post" id="inserirFormCpPagamento">
                            <div class="row mt-2">
                                <div class="col-md">
                                    <label class="form-label ts-label">Historico</label>
                                    <input type="text" class="form-control ts-input" name="historico" id="bp_historico">
                                    <input type="hidden" class="form-control ts-input" name="idCP" id="bp_idCP">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label ts-label">Fornecedor</label>
                                    <input type="text" class="form-control ts-input" id="bp_nomePessoa" disabled>
                                </div>
                            </div><!--fim row-->
                            <div class="row mt-2">
                                <div class="col-md">
                                    <label class="form-label ts-label">valorOriginal</label>
                                    <input type="text" class="form-control ts-input text-end" id="bp_valorOriginal" disabled>
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">valorPago</label>
                                    <input type="text" class="form-control ts-input text-end" id="bp_valorPago" disabled>
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">saldo</label>
                                    <input type="text" class="form-control ts-input text-end" id="bp_saldo" disabled>
                                </div>
                            </div><!--fim row-->
                            <div class="row mt-3">
                                <div class="col-md">
                                    <label class="form-label ts-label">Categoria</label>
                                    <select class="form-select ts-input" name="idCategoria" id="bp_idCategoria">
                                        <?php
                                        foreach ($contacategorias as $contacategoria) {
                                        ?>
                                            <option value="<?php echo $contacategoria['idCategoria'] ?>">
                                                <?php echo $contacategoria['nomeCategoria'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">Portador</label>
                                    <select class="form-select ts-input" name="idPortador" id="bp_idPortador">
                                        <?php
                                        foreach ($portadores as $portador) {
                                        ?>
                                            <option value="<?php echo $portador['idPortador'] ?>">
                                                <?php echo $portador['nomePortador'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div><!--fim row-->
                            <div class="row mt-2">
                                <div class="col-md">
                                    <label class="form-label ts-label">dtPagamento *</label>
                                    <input type="date" class="form-control ts-input" name="dtPagamento" id="baixar_dtPagamento" required>
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">acrescimos</label>
                                    <input type="text" class="form-control ts-input" name="acrescimos">
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">descontos</label>
                                    <input type="text" class="form-control ts-input" name="descontos">
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">valorPago</label>
                                    <input type="text" class="form-control ts-input formatValorDecimal" name="valorPago" id="bp_valorPagoPagamento" required>
                                </div>
                            </div><!--fim row-->

                    </div><!--body-->
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="btnSalvarPagamento">Salvar</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <!--------- MODAL GERENCIAR PAGAMENTO --------->
        <?php include_once 'gerenciarpagamentos_modal.php' ?>

        <!--------- EXCLUIR CONTAS PAgAR --------->
        <div class="modal fade" id="excluirContasPagarmodal" tabindex="-1" aria-labelledby="excluirContasPagarmodalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0 mb-0 pb-0">
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-4" style="font-size: 30px;"></i>
                            <div>
                                Deseja Realmente excluir esse Pagamento?
                            </div>
                        </div>
                        <form method="post" id="excluirFormContasPagar">
                            <div class="row mt-2 d-none">
                                <div class="col-md">
                                    <label class="form-label ts-label">idCP</label>
                                    <input type="text" class="form-control ts-input" name="idCP" id="excluirContasPagar_idCP" readonly>
                                </div>
                            </div>
                    </div><!--body-->
                    <div class="modal-footer mt-0 pt-0">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Nao</button>
                        <button type="submit" class="btn btn-success">Sim</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <!--------- EXCLUIR CONTAS PAGAR PAGAMENTO --------->
        <?php include_once 'excluirpagamentos_modal.php' ?>

        <!-- LOCAL PARA COLOCAR OS JS -->

        <?php include_once ROOT . "/vendor/footer_js.php"; ?>

        <!-- script para menu de filtros -->
        <script src="<?php echo URLROOT ?>/sistema/js/filtroTabela.js"></script>

        <script>
            buscar($("#buscaContasPagar").val(), $("#FiltroCategoria").val(), $("#FiltroPortador").val(), $("#FiltroSituacao").val(), $("#FiltroPeriodoInicio").val(), $("#FiltroPeriodoFim").val());

            function limpar() {
                window.location.reload();
            }

            function limparPeriodo() {
                window.location.reload();
            }

            function buscar(buscaContasPagar, idCategoria, idPortador, filtrosituacao, PeriodoInicio, PeriodoFim) {
                var h6Element = $("#filtroh6 h6");
                var text = "";
                if (filtrosituacao === "emaberto") {
                    if (text) text += ", ";
                    text += "Status = Em aberto";
                } else if (filtrosituacao === "emitida") {
                    if (text) text += ", ";
                    text += "Status = Emitida";
                } else if (filtrosituacao === "pagas") {
                    if (text) text += ", ";
                    text += "Status = Pagas";
                }

                if (PeriodoInicio !== "") {
                    text += PeriodoInicio !== null ? " em " + formatDate(PeriodoInicio) : " ";

                }

                if (PeriodoFim !== "") {
                    text += PeriodoFim !== null ? " até " + formatDate(PeriodoFim) : " ";
                }

                h6Element.html(text);

                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: '<?php echo URLROOT ?>/financeiro/database/contaspagar.php?operacao=filtrar',
                    beforeSend: function() {
                        $("#dados").html("Carregando...");
                    },
                    data: {
                        buscar: buscaContasPagar,
                        buscaCategoria: idCategoria,
                        buscaPortador: idPortador,
                        filtrosituacao: filtrosituacao,
                        PeriodoInicio: PeriodoInicio,
                        PeriodoFim: PeriodoFim
                    },
                    success: function(msg) {

                        var json = JSON.parse(msg);
                        var linha = "";
                        for (var $i = 0; $i < json.length; $i++) {
                            var object = json[$i];

                            var dataCompetenciaFormatada = formatDate(object.dtCompetencia);
                            var dataVencimentoFormatada = formatDate(object.dtVencimento);
                            var dataLiquidacaoFormatada = formatDate(object.dtLiquidacao);

                            vValor = object.valorOriginal;
                            if (object.saldo != 0) {
                                vValor = object.saldo
                            }

                            linha += "<tr>";

                            linha += "<td class='text-start ts-click' data-idCP='" + object.idCP + "'>" + object.nomePessoa + "</td>";
                            linha += "<td class='text-start ts-click' data-idCP='" + object.idCP + "'>" + object.historico + "</td>";
                            linha += "<td class='text-start ts-click' data-idCP='" + object.idCP + "'>" + object.documento + "</td>";
                            linha += "<td class='ts-click' data-idCP='" + object.idCP + "'>" + dataCompetenciaFormatada + "</td>";
                            linha += "<td class='text-start ts-click' data-idCP='" + object.idCP + "'>" + object.nomeCategoria + "</td>";
                            linha += "<td class='text-start ts-click' data-idCP='" + object.idCP + "'>" + object.nomePortador + "</td>";
                            linha += "<td class='ts-click' data-idCP='" + object.idCP + "'>" + dataVencimentoFormatada + "</td>";
                            linha += "<td class='ts-click' data-idCP='" + object.idCP + "'>" + dataLiquidacaoFormatada + "</td>";
                            linha += "<td class='ts-click' data-idCP='" + object.idCP + "'>" + (vValor !== null ? vValor.toLocaleString('pt-br', {
                                minimumFractionDigits: 2
                            }) : "-") + "</td>";
                            linha += "<td class='text-start ts-click' data-idCP='" + object.idCP + "'>" + object.situacao + "</td>";
                            linha += "<td>";
                            linha += "<div class='btn-group dropstart'><button type='button' class='btn' data-toggle='tooltip' data-placement='left' title='Opções' data-bs-toggle='dropdown' " +
                                " aria-expanded='false' style='box-shadow:none'><i class='bi bi-three-dots-vertical'></i></button><ul class='dropdown-menu'>";

                            linha += "<li class='ms-1 me-1 mt-1'><a class='btn btn-primary btn-sm w-100 text-start' data-toggle='modal' data-target='#baixarpagamentomodal' data-idCP='" + object.idCP + "' role='button'><i class='bi bi-pencil-square'></i> Baixar</a></li>";
                            if (object.situacao != 'Aberto') {
                                linha += "<li class='ms-1 me-1 mt-1'><a class='btn btn-primary btn-sm w-100 text-start' data-toggle='modal' data-target='#gerenciarpagamentomodal' data-idCP='" + object.idCP + "' role='button'><i class='bi bi-pencil-square'></i> Gerenciar</a></li>";
                            }
                            linha += "<li ><hr class='mt-2 mb-1'></li>";
                            if (object.situacao != 'Parcial') {
                                linha += "<li class='ms-1 me-1 mt-1'><a class='btn btn-danger btn-sm w-100 text-start' data-toggle='modal' data-target='#excluirContasPagarmodal' data-idCP='" + object.idCP + "' role='button'><i class='bi bi-trash3'></i> Excluir</a></li>";
                            }
                            linha += "</tr></ul></div>"
                            linha += "</td>";

                            linha += "</tr>";
                        }

                        $("#dados").html(linha);

                    }
                });
            }

            $("#buscar").click(function() {
                buscar($("#buscaContasPagar").val(), $("#FiltroCategoria").val(), $("#FiltroPortador").val(), $("#FiltroSituacao").val(), null, null);
            });

            $("#FiltroCategoria").change(function() {
                vfiltrosituacao = $("#FiltroSituacao").val();
                datainiciomes = $("#datainiciomes").val()
                dataatualmes = $("#dataatualmes").val();
                if (vfiltrosituacao != "emaberto") {
                    buscar($("#buscaContasPagar").val(), $("#FiltroCategoria").val(), $("#FiltroPortador").val(), $("#FiltroSituacao").val(), datainiciomes, dataatualmes);
                } else {
                    buscar($("#buscaContasPagar").val(), $("#FiltroCategoria").val(), $("#FiltroPortador").val(), $("#FiltroSituacao").val(), null, null);
                }
            });

            $("#FiltroPortador").change(function() {
                vfiltrosituacao = $("#FiltroSituacao").val();
                datainiciomes = $("#datainiciomes").val()
                dataatualmes = $("#dataatualmes").val();
                if (vfiltrosituacao != "emaberto") {
                    buscar($("#buscaContasPagar").val(), $("#FiltroCategoria").val(), $("#FiltroPortador").val(), $("#FiltroSituacao").val(), datainiciomes, dataatualmes);
                } else {
                    buscar($("#buscaContasPagar").val(), $("#FiltroCategoria").val(), $("#FiltroPortador").val(), $("#FiltroSituacao").val(), null, null);
                }
            });

            $("#FiltroSituacao").change(function() {
                vfiltrosituacao = $("#FiltroSituacao").val();
                datainiciomes = $("#datainiciomes").val()
                dataatualmes = $("#dataatualmes").val();
                if (vfiltrosituacao != "emaberto") {
                    buscar($("#buscaContasPagar").val(), $("#FiltroCategoria").val(), $("#FiltroPortador").val(), $("#FiltroSituacao").val(), datainiciomes, dataatualmes);
                } else {
                    buscar($("#buscaContasPagar").val(), $("#FiltroCategoria").val(), $("#FiltroPortador").val(), $("#FiltroSituacao").val(), null, null);
                }
            });

            $("#filtrarButton").click(function() {
                buscar($("#buscaContasPagar").val(), $("#FiltroCategoria").val(), $("#FiltroPortador").val(), $("#FiltroSituacao").val(), $("#FiltroPeriodoInicio").val(), $("#FiltroPeriodoFim").val());
                $('#periodoModal').modal('hide');
            });

            document.addEventListener("keypress", function(e) {
                if (e.key === "Enter") {
                    buscar($("#buscaContasPagar").val(), $("#FiltroCategoria").val(), $("#FiltroPortador").val(), $("#FiltroSituacao").val(), $("#FiltroPeriodoInicio").val(), $("#FiltroPeriodoFim").val());
                }
            });

            //MODAL ALTERAR
            $(document).on('click', '.ts-click', function() {
                var idCP = $(this).attr("data-idCP");
                //alert(idCP)
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '<?php echo URLROOT ?>/financeiro/database/contaspagar.php?operacao=buscar',
                    data: {
                        idCP: idCP
                    },
                    success: function(data) {
                        //console.log(JSON.stringify(data, null, 2));
                        $('#idCP').val(data.idCP);
                        $('#idPessoaFornecedor').val(data.idPessoaFornecedor);
                        $('#nomePessoa').val(data.nomePessoa);
                        $('#dtCompetencia').val(data.dtCompetencia);
                        $('#dtVencimento').val(data.dtVencimento);
                        $('#documento').val(data.documento);
                        $('#historico').val(data.historico);
                        $('#valorOriginal').val(data.valorOriginal !== null ? data.valorOriginal.toLocaleString('pt-br', {
                            minimumFractionDigits: 2
                        }) : "-");
                        $('#situacao').val(data.situacao);
                        $('#idPortador').val(data.idPortador);
                        $('#idCategoria').val(data.idCategoria);
                        $('#dtLiquidacao').val(data.dtLiquidacao);

                        vsituacao = 'Aberto';
                        //situacao Parcial
                        if (data.dtLiquidacao == null && data.saldo != 0) {
                            vsituacao = 'Parcial';
                            $('#idPessoaFornecedor').addClass('ts-displayDisable');
                            $('#valorOriginal').prop('readonly', true);
                        } else {
                            $('#idPessoaFornecedor').removeClass('ts-displayDisable');
                            $('#valorOriginal').prop('readonly', false);
                        }

                        //situacao Liquidado
                        if (data.dtLiquidacao != null && data.saldo == 0) {
                            vsituacao = 'Liquidado';
                            $('#btn-salvarAlteracao').hide();
                        } else {
                            $('#btn-salvarAlteracao').show();
                        }

                        //texto valorOriginal
                        var texto = $("#textoalterarsituacao");
                        texto.html('Situacao: ' + vsituacao);

                        //texto saldo
                        vsaldo = (data.saldo !== null ? data.saldo.toLocaleString('pt-br', {
                            minimumFractionDigits: 2
                        }) : "-");
                        var texto = $("#textoalterarsaldo");
                        texto.html('Saldo: ' + vsaldo);

                        $('#alterarmodal').modal('show');
                    }
                });
            });

            //MODAL BAIXARPAGAMENTO
            $(document).on('click', 'a[data-target="#baixarpagamentomodal"]', function() {
                var idCP = $(this).attr("data-idCP");

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '<?php echo URLROOT ?>/financeiro/database/contaspagar.php?operacao=buscar',
                    data: {
                        idCP: idCP
                    },
                    success: function(data) {
                        console.log(JSON.stringify(data, null, 2));
                        $('#bp_idCP').val(data.idCP);
                        $('#bp_historico').val(data.historico);
                        $('#bp_idPortador').val(data.idPortador);
                        $('#bp_idCategoria').val(data.idCategoria);
                        $('#bp_valorOriginal').val(data.valorOriginal !== null ? data.valorOriginal.toLocaleString('pt-br', {
                            minimumFractionDigits: 2
                        }) : "-");
                        $('#bp_nomePessoa').val(data.nomePessoa);

                        //titulo modal baixarpagamento
                        var titulo = $("#titulomodalbaixarpagamento");
                        titulo.html('Pagamento documento: ' + data.documento);

                        if (data.saldo == 0) {
                            $('#bp_valorPagoPagamento').val(data.valorOriginal !== null ? data.valorOriginal.toLocaleString('pt-br', {
                                minimumFractionDigits: 2
                            }) : "-");
                        } else {
                            $('#bp_valorPagoPagamento').val(data.saldo !== null ? data.saldo.toLocaleString('pt-br', {
                                minimumFractionDigits: 2
                            }) : "-");
                        }

                        var valorOriginal = data.valorOriginal;
                        $('#bp_saldo').val(data.saldo !== null ? data.saldo.toLocaleString('pt-br', {
                            minimumFractionDigits: 2
                        }) : "-");
                        $('#bp_valorPago').val(data.valorPago !== null ? data.valorPago.toLocaleString('pt-br', {
                            minimumFractionDigits: 2
                        }) : "-");
                        var valorPago = data.valorPago;

                        if (valorOriginal == valorPago) {
                            $('#bp_valorPagoPagamento').prop('readonly', true);
                            $('#btnSalvarPagamento').hide();

                        } else {
                            $('#bp_valorPagoPagamento').prop('readonly', false);
                            $('#btnSalvarPagamento').show();
                        }

                        $('#baixarpagamentomodal').modal('show');
                    }
                });
            });

            //MODAL GERENCIAR PAGAMENTO
            $(document).on('click', 'a[data-target="#gerenciarpagamentomodal"]', function() {
                var idCP = $(this).attr("data-idCP");

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '<?php echo URLROOT ?>/financeiro/database/contaspagarpagamento.php?operacao=buscarContasPagar',
                    data: {
                        idCP: idCP
                    },
                    success: function(data) {
                        //console.log(JSON.stringify(data, null, 2));
                        var linha = "";
                        for (var $i = 0; $i < data.length; $i++) {
                            var object = data[$i];

                            //titulo modal gerenciar
                            var titulo = $("#titulomodalgerenciar");
                            titulo.html('Gerenciar Pagamento: ' + object.documento);

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
                            linha += "<td>" + "<button type='button' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#excluirCpPagamentomodal' data-idCP='" + object.idCP + "' data-idCpPgto='" + object.idCpPgto + "'><i class='bi bi-trash3'></i></button>"
                            linha += "</tr>";
                        }

                        $("#dados_gerenciarPagamento").html(linha);
                        $('#gerenciarpagamentomodal').modal('show')
                    }
                });
            });

            //MODAL EXCLUIR CONTAS PAGAR
            $(document).on('click', 'a[data-target="#excluirContasPagarmodal"]', function() {
                var idCP = $(this).attr("data-idCP");

                $('#excluirContasPagar_idCP').val(idCP);

                $('#excluirContasPagarmodal').modal('show');
            });

            //MODAL EXCLUIR CONTAS PAGAR PAGAMENTO
            $(document).on('click', 'button[data-target="#excluirCpPagamentomodal"]', function() {
                var idCP = $(this).attr("data-idCP");
                var idCpPgto = $(this).attr("data-idCpPgto");

                $('#excluir_idCP').val(idCP);
                $('#excluir_idCpPgto').val(idCpPgto);

                $('#excluirCpPagamentomodal').modal('show');
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
                $("#inserirFormContasPagar").submit(function(event) {
                    event.preventDefault();
                    var formData = new FormData(this);
                    $.ajax({
                        url: "../database/contaspagar.php?operacao=inserir",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: refreshPage,
                    });
                });

                $("#alterarFormContasPagar").submit(function(event) {
                    event.preventDefault();
                    var formData = new FormData(this);
                    $.ajax({
                        url: "../database/contaspagar.php?operacao=alterar",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: refreshPage,
                    });
                });

                $("#inserirFormCpPagamento").submit(function(event) {
                    event.preventDefault();
                    var formData = new FormData(this);
                    $.ajax({
                        url: "../database/contaspagarpagamento.php?operacao=inserir",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: refreshPage,
                    });
                });

                $("#excluirFormContasPagar").submit(function(event) {
                    event.preventDefault();
                    var formData = new FormData(this);
                    $.ajax({
                        url: "../database/contaspagar.php?operacao=excluir",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: refreshPage,
                    });
                });

                $("#excluirFormCpPagamento").submit(function(event) {
                    event.preventDefault();
                    var formData = new FormData(this);
                    $.ajax({
                        url: "../database/contaspagarpagamento.php?operacao=excluir",
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

            // FORMATAR DATAS
            function formatDate(dateString) {
                if (dateString !== null && !isNaN(new Date(dateString))) {
                    var date = new Date(dateString);
                    var day = date.getUTCDate().toString().padStart(2, '0');
                    var month = (date.getUTCMonth() + 1).toString().padStart(2, '0');
                    var year = date.getUTCFullYear().toString().padStart(4, '0');
                    return day + "/" + month + "/" + year;
                }
                return "";
            }

            // Ao iniciar o programa, inseri os valores de data nos inputs. 
            $(document).ready(function() {
                var data = new Date(),
                    dia = data.getDate().toString(),
                    diaF = (dia.length == 1) ? '0' + dia : dia,
                    mes = (data.getMonth() + 1).toString(), //+1 pois no getMonth Janeiro come�a com zero.
                    mesF = (mes.length == 1) ? '0' + mes : mes,
                    anoF = data.getFullYear();
                dataAtual = anoF + "-" + mesF + "-" + diaF;
                primeirodiadomes = anoF + "-" + mesF + "-" + "01";

                //modal inserir contaspagar
                const inserir_dtCompetencia = document.getElementById("inserir_dtCompetencia");
                inserir_dtCompetencia.value = dataAtual;

                //modal baixar pagamento
                const baixar_dtPagamento = document.getElementById("baixar_dtPagamento");
                baixar_dtPagamento.value = dataAtual;

                //filtro situacao
                const datainiciomes = document.getElementById("datainiciomes");
                datainiciomes.value = primeirodiadomes;

                const dataatualmes = document.getElementById("dataatualmes");
                dataatualmes.value = dataAtual;

                //modal filtroPeriodo_modal
                const FiltroPeriodoInicio = document.getElementById("FiltroPeriodoInicio");
                FiltroPeriodoInicio.value = primeirodiadomes;

                const FiltroPeriodoFim = document.getElementById("FiltroPeriodoFim");
                FiltroPeriodoFim.value = dataAtual;

            });

            // Formatar input de valor decimal
            $(document).ready(function() {
                $('.formatValorDecimal').mask("#.##0,00", {
                    reverse: true
                });
                $('.formatValorDecimal').addClass("text-end")
            });
        </script>

        <!-- LOCAL PARA COLOCAR OS JS -FIM -->


</body>

</html>