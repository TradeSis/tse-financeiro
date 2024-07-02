
// Programa especializado em CRAR a tabela contaspagar
def temp-table ttentrada no-undo serialize-name "contaspagar"   /* JSON ENTRADA */
    LIKE contaspagar.

  
def input param vAcao as char.
DEF INPUT PARAM TABLE FOR ttentrada.
def output param vidCP as INT.
def output param vmensagem as char.

vidCP = ?.
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
        create contaspagar.
        vidCP = contaspagar.idCP.
        BUFFER-COPY ttentrada EXCEPT idCP TO contaspagar.
    end.
    
END.


IF vAcao = "POST" 
THEN DO:

    if ttentrada.idCP = ? or ttentrada.idCP = 0
    then do:
        vmensagem = "Dados de Entrada Invalidos".
        return.
    end.

    find contaspagar where contaspagar.idCP = ttentrada.idCP no-lock no-error.
    if not avail contaspagar
    then do:
        vmensagem = "Conta à pagar nao cadastrada".
        return.
    end.

    do on error undo: 
        find contaspagar where contaspagar.idCP = ttentrada.idCP EXCLUSIVE no-error.
        if ttentrada.idPessoaFornecedor <> ? 
        then do:
            contaspagar.idPessoaFornecedor = ttentrada.idPessoaFornecedor.
        end.
        if ttentrada.dtCompetencia <> ?
        then do:
            contaspagar.dtCompetencia = ttentrada.dtCompetencia.
        end.
        if ttentrada.dtVencimento <> ?
        then do:
            contaspagar.dtVencimento = ttentrada.dtVencimento.
        end.
        if ttentrada.dtCompetencia <> ?
        then do:
            contaspagar.dtCompetencia = ttentrada.dtCompetencia.
        end.
        if ttentrada.documento <> ?
        then do:
            contaspagar.documento = ttentrada.documento.
        end.
        if ttentrada.historico <> ?
        then do:
            contaspagar.historico = ttentrada.historico.
        end.
        if ttentrada.valorOriginal <> ?
        then do:
            contaspagar.valorOriginal = ttentrada.valorOriginal.
        end.
        if ttentrada.idPortador <> ?
        then do:
            contaspagar.idPortador = ttentrada.idPortador.
        end.
        if ttentrada.idCategoria <> ?
        then do:
            contaspagar.idCategoria = ttentrada.idCategoria.
        end.
    end.

    
END. 
   

