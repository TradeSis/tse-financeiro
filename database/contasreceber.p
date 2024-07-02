
// Programa especializado em CRAR a tabela contasreceber
def temp-table ttentrada no-undo serialize-name "contasreceber"   /* JSON ENTRADA */
    LIKE contasreceber.

  
def input param vAcao as char.
DEF INPUT PARAM TABLE FOR ttentrada.
def output param vidCR as INT.
def output param vmensagem as char.

vidCR = ?.
vmensagem = ?.

find first ttentrada no-error.
if not avail ttentrada then do:
    vmensagem = "Dados de Entrada nao encontrados".
    return.    
end.

if ttentrada.idPessoaFornecedor = 0
then do:
    ttentrada.idPessoaFornecedor = ?.
end.
if ttentrada.idPortador = 0
then do:
    ttentrada.idPortador = ?.
end.
if ttentrada.idCategoria = 0
then do:
    ttentrada.idCategoria = ?.
end.
if ttentrada.valorOriginal = 0
then do:
    ttentrada.valorOriginal = ?.
end.
        
if vAcao = "PUT"
THEN DO:

    do on error undo:
        create contasreceber.
        vidCR = contasreceber.idCR.
        BUFFER-COPY ttentrada EXCEPT idCR TO contasreceber.
    end.
    
END.


IF vAcao = "POST" 
THEN DO:

    if ttentrada.idCR = ? or ttentrada.idCR = 0
    then do:
        vmensagem = "Dados de Entrada Invalidos".
        return.
    end.

    find contasreceber where contasreceber.idCR = ttentrada.idCR no-lock no-error.
    if not avail contasreceber
    then do:
        vmensagem = "Conta à receber nao cadastrada".
        return.
    end.

    do on error undo: 
        find contasreceber where contasreceber.idCR = ttentrada.idCR EXCLUSIVE no-error.
        if ttentrada.idPessoaFornecedor <> ? 
        then do:
            contasreceber.idPessoaFornecedor = ttentrada.idPessoaFornecedor.
        end.
        if ttentrada.dtCompetencia <> ?
        then do:
            contasreceber.dtCompetencia = ttentrada.dtCompetencia.
        end.
        if ttentrada.dtVencimento <> ?
        then do:
            contasreceber.dtVencimento = ttentrada.dtVencimento.
        end.
        if ttentrada.dtCompetencia <> ?
        then do:
            contasreceber.dtCompetencia = ttentrada.dtCompetencia.
        end.
        if ttentrada.documento <> ?
        then do:
            contasreceber.documento = ttentrada.documento.
        end.
        if ttentrada.historico <> ?
        then do:
            contasreceber.historico = ttentrada.historico.
        end.
        if ttentrada.valorOriginal <> ?
        then do:
            contasreceber.valorOriginal = ttentrada.valorOriginal.
        end.
        if ttentrada.idPortador <> ?
        then do:
            contasreceber.idPortador = ttentrada.idPortador.
        end.
        if ttentrada.idCategoria <> ?
        then do:
            contasreceber.idCategoria = ttentrada.idCategoria.
        end.
    end.

    
END. 
   

