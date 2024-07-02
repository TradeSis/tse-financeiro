 <!--------- EXCLUIR CONTAS RECEBER PAGAMENTO --------->
 <div class="modal fade" id="excluirCrPagamentomodal" tabindex="-1" aria-labelledby="excluirCrPagamentomodalLabel" aria-hidden="true">
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
                                Deseja Realmente excluir esse pagamento?
                            </div>
                        </div>
                        <form method="post" id="excluirFormCrPagamento">
                            <div class="row mt-2 d-none">
                                <div class="col-md">
                                    <label class="form-label ts-label">idCrPgto</label>
                                    <input type="text" class="form-control ts-input" name="idCrPgto" id="excluir_idCrPgto" readonly>
                                </div>
                                <div class="col-md">
                                    <label class="form-label ts-label">idCR</label>
                                    <input type="text" class="form-control ts-input" name="idCR" id="excluir_idCR" readonly>
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