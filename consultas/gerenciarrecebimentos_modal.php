<!--------- GERENCIAR RECEBIMENTO --------->
<div class="modal" id="gerenciarrecebimentomodal" tabindex="-1" aria-labelledby="gerenciarrecebimentomodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header mb-0 pb-0">      
                <div class="col align-self-start pl-0">
                    <div class="col-md">
                        <h5 class="modal-title" id="titulomodalgerenciar"></h5>
                    </div>
                    <div class="col-md">
                        <h7 class="modal-title" id="textoGRsituacao"></h7>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="table mt-2 ts-divTabela60 ts-tableFiltros text-center">
                    <table class="table table-sm table-hover">
                        <thead class="ts-headertabelafixo">
                            <tr>
                                <th>Cliente</th>
                                <th>dtPagamento</th>
                                <th>historico</th>
                                <th>Categoria</th>
                                <th>Portador</th>
                                <th>acrescimos</th>
                                <th>descontos</th>
                                <th>valorPago</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id='dados_gerenciarRecebimento' class="fonteCorpo">

                        </tbody>
                    </table>
                </div>
            </div><!--body-->
            <div class="modal-footer mt-0 pt-0">
                <div class="col align-self-start pl-0">
                    <div class="col-md">
                        <h6 class="modal-title" id="textovalorOriginal"></h6>
                    </div>
                    <div class="col-md">
                        <h6 class="modal-title" id="textosaldo"></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>