<?php
//Lucas 16072024 criado
include_once(__DIR__ . '/../header.php');
include_once(__DIR__ . '/../database/portador.php');

$portadores = buscaPortador();
?>
<!doctype html>
<html lang="pt-BR">

<head>

    <?php include_once ROOT . "/vendor/head_css.php"; ?>

</head>

<body>

    <div class="container-fluid">

        <div class="row ">
            <!--<BR> MENSAGENS/ALERTAS -->
        </div>
        <div class="row">
            <!--<BR> BOTOES AUXILIARES -->
        </div>

        <div class="row d-flex align-items-center justify-content-center mt-1 pt-1 ">

            <div class="col-2">
                <h2 class="ts-tituloPrincipal">Caixa e Bancos</h2>
            </div>

            <div class="col-2">
                <select class="form-select ts-input" name="idPortador" id="FiltroPortador">
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
            <div class="col-4">

            </div>
            <div class="col-3 gap-1 d-flex">
                <input type="text" class="form-control ts-input" name="anoImposto" id="FiltroDataAno" placeholder="Ano" autocomplete="off" required>

                <select class="form-select ts-input" name="mesImposto" id="FiltroDataMes">
                    <option value="01">Janeiro</option>
                    <option value="02">Fevereiro</option>
                    <option value="03">Março</option>
                    <option value="04">Abril</option>
                    <option value="05">Maio</option>
                    <option value="06">Junho</option>
                    <option value="07">Julho</option>
                    <option value="08">Agosto</option>
                    <option value="09">Setembro</option>
                    <option value="10">Outubro</option>
                    <option value="11">Novembro</option>
                    <option value="12">Dezembro</option>
                </select>
                <button class="btn btn-sm btn-primary d-none" type="button" id="filtrardata">Filtrar</button>
            </div>
            <div class="col-1 text-end">
                <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#transferir" aria-controls="offcanvasRight">Tranferir</button>
            </div>

        </div><!-- ROW -->

        <div class="table mt-2 ts-divTabela ts-tableFiltros text-center">
            <table class="table table-sm table-hover">
                <thead class="ts-headertabelafixo">
                    <tr class="ts-headerTabelaLinhaCima">
                        <th class="text-start">Documento</th>
                        <th class="text-start">Cliente/Fornecedor</th>
                        <th>Data</th>
                        <th class="text-start">Historico</th>
                        <th class="text-start">Categoria</th>
                        <th class="text-end">Valor</th>
                        <th style="width: 20px;"></th>
                    </tr>
                </thead>

                <tbody id='dados' class="fonteCorpo">

                </tbody>
            </table>
            <!-- div de loading -->
            <div class="text-center" id="div-load" style="margin-top: 200px; display: none">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
        <h6 class="fixed-bottom" id="textocontador" style="color: #13216A;"></h6>

        <!-- TRANSFERENCIA -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="transferir" aria-labelledby="transferirLabel">
            <div class="offcanvas-header border-bottom">
                <h5>Transferência</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form method="post" id="transferirForm">
                    <div class="row">
                        <div class="col-md">
                            <label class="form-label ts-label">Conta Origem</label>
                            <select class="form-select ts-input ts-displayDisable" name="idPortadorOrigem" id="idPortadorOrigem">
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
                            <label class="form-label ts-label">Conta Destino</label>
                            <select class="form-select ts-input" name="idPortadorDestino" id="idPortadorDestino">
                                <?php
                                foreach ($portadores as $portador) {
                                ?>
                                    <option value="<?php echo $portador['idPortador'] ?>">
                                        <?php echo $portador['nomePortador'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md">
                            <label class="form-label ts-label">valor</label>
                            <input type="text" class="form-control ts-input formatValorDecimal" name="valor" id="valor" required>
                        </div>
                        <div class="col-md">
                            <label class="form-label ts-label">Data de transferencia</label>
                            <input type="date" class="form-control ts-input dataTransferencia" name="data" id="data" required>
                        </div>

                    </div>

            </div><!--body-->
            <div class="p-1 text-end">
                <button type="submit" class="btn btn-success">Transferir</button>
            </div>
            </form>
        </div>

        <!--------- EXCLUIR TRANSFERENCIA --------->
        <div class="modal fade" id="excluirTransferencia" tabindex="-1" aria-labelledby="excluirTransferenciaLabel" aria-hidden="true">
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
                                Deseja Realmente excluir essa Transferencia?
                            </div>
                        </div>
                        <form method="post" id="excluirTransferenciaForm">
                            <div class="row mt-2 d-none">
                                <div class="col-md">
                                    <label class="form-label ts-label">Transferencia</label>
                                    <input type="text" class="form-control ts-input" name="id_recid" id="exc_id_recid" readonly>
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

    </div><!--container-fluid-->

    <!-- LOCAL PARA COLOCAR OS JS -->

    <?php include_once ROOT . "/vendor/footer_js.php"; ?>

    <script>
        // Ao iniciar o programa, inseri os valores de ano(input) e mes(select) atuais. 
        $(document).ready(function() {
            const date = new Date();
            const year = date.getFullYear();
            const currentMonth = date.getMonth() + 1;

            const FiltroDataAno = document.getElementById("FiltroDataAno");
            FiltroDataAno.value = year;
            ano = year;

            const FiltroDataMes = document.getElementById("FiltroDataMes");
            FiltroDataMes.value = (currentMonth <= 9 ? "0" + currentMonth : currentMonth);
            mes = (currentMonth <= 9 ? "0" + currentMonth : currentMonth)

            var texto = $("#textocontador");
            texto.html('total: ' + 0);

            buscar(ano, mes, $("#FiltroPortador").val());
        });

        function buscar(FiltroDataAno, FiltroDataMes, FiltroPortador) {
            //alert(FiltroPortador)

            if (FiltroPortador == '') {
                alert("Informar Portador")
            } else {
                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: '../database/caixaebancos.php?operacao=filtrar',
                    beforeSend: function() {
                        setTimeout(function() {
                            $("#div-load").css("display", "block");
                        }, 500);
                    },
                    data: {
                        anoEntrada: FiltroDataAno,
                        mesEntrada: FiltroDataMes,
                        portador: FiltroPortador
                    },
                    success: function(msg) {
                        //alert(msg)
                        var json = JSON.parse(msg);
                        //alert(JSON.stringify(json));
                        if (json == '') {
                            alert("Nenhum registro retornado!")
                            linha = "";
                            $("#dados").html(linha);
                            $("#div-load").css("display", "none");

                        } else {
                            var contadorItem = 0;
                            var linha = "";
                            for (var $i = 0; $i < json.length; $i++) {
                                var object = json[$i];

                                if (object.cbidentificador != "TOTAL") {
                                    contadorItem += 1;
                                }
                                var valorPagoFormatado = object.cbvalorPago.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 2
                                });
                                //LINHA DE TOTAL
                                if (object.cbidentificador == "TOTAL") {
                                    linha = linha + "<tr class='table-active'>";

                                    linha = linha + "<td></td>";
                                    linha = linha + "<td></td>";
                                    linha = linha + "<td></td>";
                                    linha = linha + "<td></td>";
                                    linha = linha + "<td class='text-end' >Saldo em: " + formatDate(object.cbdtPagamento) + "</td>";
                                    linha = linha + "<td class='text-end fw-bold text-secundary border-0'>" + valorPagoFormatado + "</td>";

                                    linha = linha + "</tr>";
                                } else {
                                    linha = linha + "<tr>";

                                    linha = linha + "<td class='text-start'>" + object.cbdocumento + "</td>";

                                    linha = linha + "<td class='text-start'>" + object.cbnomePessoaFornecedor + "</td>";
                                    linha = linha + "<td>" + formatDate(object.cbdtPagamento) + "</td>";

                                    if (object.cbidentificador == "TRE") {
                                        linha = linha + "<td class='text-start'>ORIGEM: " + object.cbportador + "</td>";
                                        linha = linha + "<td class='text-start'>" + object.cbcategoria + "</td>";
                                    } else if (object.cbidentificador == "TRS") {
                                        linha = linha + "<td class='text-start'>DESTINO: " + object.cbportadorDestino + "</td>";
                                        linha = linha + "<td class='text-start'>" + object.cbcategoria + "</td>";
                                    } else {
                                        linha = linha + "<td class='text-start'>" + object.cbhistorico + "</td>";
                                        linha = linha + "<td class='text-start'>" + object.cbcategoria + "</td>";
                                    }

                                    if (object.cbidentificador == "CR") {
                                        linha = linha + "<td class='text-end fw-bold text-success border-0'>" + valorPagoFormatado + "</td>";
                                    } else if (object.cbidentificador == "TRE") {
                                        linha = linha + "<td class='text-end fw-bold text-success border-0'>" + valorPagoFormatado + "</td>";
                                    } else if (object.cbidentificador == "TRS") {
                                        linha = linha + "<td class='text-end fw-bold text-danger border-0'> -" + valorPagoFormatado + "</td>";
                                    } else {
                                        linha = linha + "<td class='text-end fw-bold text-danger border-0'> -" + valorPagoFormatado + "</td>";
                                    }

                                    linha += "<td>";
                                    linha += "<div class='btn-group dropstart'><button type='button' class='btn' data-toggle='tooltip' data-placement='left' title='Opções' data-bs-toggle='dropdown' " +
                                        " aria-expanded='false' style='box-shadow:none'><i class='bi bi-three-dots-vertical'></i></button><ul class='dropdown-menu'>";

                                    if (object.cbidentificador == "CR") {
                                        linha += "<li class='ms-1 me-1'><a class='btn btn-primary btn-sm w-100 text-start' href='gerenciarrecebimentos.php' role='button'><i class='bi bi-clipboard-pulse'></i> Gerenciar</a></td>";
                                    } else if (object.cbidentificador == "CP") {
                                        linha += "<li class='ms-1 me-1'><a class='btn btn-primary btn-sm w-100 text-start' href='gerenciarpagamentos.php' role='button'><i class='bi bi-clipboard-pulse'></i> Gerenciar</a></td>";
                                    } else {
                                        linha += "<li class='ms-1 me-1'><a class='btn btn-danger btn-sm w-100 text-start' data-bs-toggle='modal' data-bs-target='#excluirTransferencia' ";
                                        linha += " data-id_recid='" + object.id_recid + "' ";
                                        linha += " role='button'><i class='bi bi-trash3'></i> Excluir</a></li>";
                                    }

                                    linha += "</tr></ul></div>"
                                    linha += "</td>";

                                    linha = linha + "</tr>";
                                }

                            }

                            $("#dados").html(linha);
                            $("#div-load").css("display", "none");
                            var texto = $("#textocontador");

                            texto.html('Total: ' + contadorItem);
                        }

                    }
                });
            }

        }

        $("#filtrardata").click(function() {
            buscar($("#FiltroDataAno").val(), $("#FiltroDataMes").val(), $("#FiltroPortador").val());
        });

        $("#FiltroPortador").change(function() {
            buscar($("#FiltroDataAno").val(), $("#FiltroDataMes").val(), $("#FiltroPortador").val());
        });

        $("#FiltroDataMes").change(function() {
            buscar($("#FiltroDataAno").val(), $("#FiltroDataMes").val(), $("#FiltroPortador").val());
        });

        document.addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                buscar($("#FiltroDataAno").val(), $("#FiltroDataMes").val(), $("#FiltroPortador").val());
            }
        });

        //MODAL BAIXARPAGAMENTO
        $(document).on('click', 'button[data-bs-target="#transferir"]', function() {

            portador = $("#FiltroPortador").val();
            $('#idPortadorOrigem').val(portador);

            var data = new Date(),
                dia = data.getDate().toString(),
                diaF = (dia.length == 1) ? '0' + dia : dia,
                mes = (data.getMonth() + 1).toString(), //+1 pois no getMonth Janeiro come�a com zero.
                mesF = (mes.length == 1) ? '0' + mes : mes,
                anoF = data.getFullYear();
            dataAtual = anoF + "-" + mesF + "-" + diaF;
            primeirodiadomes = anoF + "-" + mesF + "-" + "01";

            // offCanvas data
            const dataTransferencia = document.getElementById("data");
            dataTransferencia.value = dataAtual;

        });

        //MODAL EXCLUIR
        $(document).on('click', 'a[data-bs-target="#excluirTransferencia"]', function() {
            var id_recid = $(this).attr("data-id_recid");

            $('#exc_id_recid').val(id_recid);

            $('#excluirTransferencia').modal('show');

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

        // Formatar input de valor decimal
        $(document).ready(function() {
            $('.formatValorDecimal').mask("#.##0,00", {
                reverse: true
            });
            $('.formatValorDecimal').addClass("text-end")
        });

        $("#transferirForm").submit(function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: "../database/caixaebancos.php?operacao=transferir",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (setTimeout(FechaOffCanvas, 1000))
            });
        });

        $("#excluirTransferenciaForm").submit(function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: "../database/caixaebancos.php?operacao=excluirTransferencia",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: refreshPage
            });
        });


        function FechaOffCanvas() {
            const elemento = document.getElementById('filtrardata');
            elemento.click()
            $('#transferir').offcanvas('hide');
            $('#valor').val('');
            $('#data').val('');
        }

        function refreshPage() {
            window.location.reload();
        }
    </script>

    <!-- LOCAL PARA COLOCAR OS JS -FIM -->

</body>

</html>