def input param vlcentrada as longchar. /* JSON ENTRADA */
def input param vtmp       as char.     /* CAMINHO PROGRESS_TMP */

def var vlcsaida   as longchar.         /* JSON SAIDA */

def var lokjson as log.                 /* LOGICAL DE APOIO */
def var hentrada as handle.             /* HANDLE ENTRADA */
def var hsaida   as handle.             /* HANDLE SAIDA */

def temp-table ttentrada no-undo serialize-name "dadosEntrada"   /* JSON ENTRADA */
    field idCpPgto  like contaspagarpagamento.idCpPgto
    field idCP      like contaspagar.idCP
    FIELD buscar AS CHAR
    FIELD buscaPortador AS INT
    FIELD buscaCategoria AS INT
    FIELD PeriodoInicio  AS DATE
    FIELD PeriodoFim     AS DATE.
    
def TEMP-TABLE ttcontaspagarpagamento  no-undo serialize-name "contaspagarpagamento"  /* JSON SAIDA */
    like contaspagarpagamento
    FIELD nomePortador           LIKE portador.nomePortador
    FIELD nomeCategoria          LIKE contascategoria.nomeCategoria
    FIELD valorOriginal          LIKE contaspagar.valorOriginal
    FIELD documento              LIKE contaspagar.documento
    FIELD nomePessoa             LIKE geralpessoas.nomePessoa
    FIELD saldo                  AS DEC.                    

def temp-table ttsaida  no-undo serialize-name "conteudoSaida"  /* JSON SAIDA CASO ERRO */
    field tstatus        as int serialize-name "status"
    field descricaoStatus      as char.

def VAR vidCpPgto like ttentrada.idCpPgto.

DEF VAR vnomePortador AS CHAR.
DEF VAR vnomeCategoria AS CHAR.
DEF VAR vsaldo AS DEC.
DEF VAR vValorOriginal AS DEC.
DEF VAR vDocumento AS CHAR.
DEF VAR vnomePessoa AS CHAR.
DEF VAR vdtPagamento    AS DATE.
DEF VAR contadorSaldo AS DEC.

hEntrada = temp-table ttentrada:HANDLE.
lokJSON = hentrada:READ-JSON("longchar",vlcentrada, "EMPTY") no-error.

find first ttentrada no-error.

if NOT AVAIL ttentrada then do:
    create ttsaida.
    ttsaida.tstatus = 400.
    ttsaida.descricaoStatus = "sem parametros de entrada".

    hsaida  = temp-table ttsaida:handle.

    lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
    message string(vlcSaida).
    return.
    
end.

if ttentrada.PeriodoInicio = ?  AND ttentrada.idCP = ? AND ttentrada.idCpPgto = ?
then do:
    create ttsaida.
    ttsaida.tstatus = 400.
    ttsaida.descricaoStatus = "Informe um periodo ou um ID".

    hsaida  = temp-table ttsaida:handle.

    lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
    message string(vlcSaida).
    return.

    
end.
contadorSaldo = 0.
// contasPagar UNICO
if ttentrada.idCP <> ? 
then do:
    for each contaspagarpagamento where  contaspagarpagamento.idCP =   ttentrada.idCP
        NO-LOCK.
        
        contadorSaldo = contadorSaldo + contaspagarpagamento.valorPago.
       
        // contaPagar UNICO com contasPagarPagamento UNICO
        if ttentrada.idCpPgto <> ? 
        then do:
            if contaspagarpagamento.idCpPgto <> ttentrada.idCpPgto  
            THEN NEXT.
        end.
        RUN criaContasPagarPagamento.
    end.    
    RUN SAIDA.
    RETURN.
end.

// contasPagarPagamento UNICO
if ttentrada.idCpPgto <> ? 
then do:
    FIND contaspagarpagamento where  contaspagarpagamento.idCpPgto =   ttentrada.idCpPgto NO-LOCK NO-ERROR.
    if AVAIL contaspagarpagamento 
    then do:
        RUN criaContasPagarPagamento.    
    end.
    RUN SAIDA.
    RETURN.
end.


// Obrigatorio periodo
FOR EACH portador WHERE
        (if ttentrada.buscaPortador = ?
        then true /* TODOS */
        ELSE portador.idPortador = ttentrada.buscaPortador)
        NO-LOCK:
    // Obrigatório periodo    
    do vdtPagamento = ttentrada.PeriodoInicio TO ttentrada.PeriodoFim:
        FOR EACH contaspagarPagamento WHERE 
            contaspagarPagamento.idPortador    = portador.idportador AND
            contaspagarPagamento.dtPagamento   = vdtPagamento 
            NO-LOCK:
            if ttentrada.buscaCategoria <> ?
            then DO:
                if  contaspagarPagamento.idCategoria <> ttentrada.buscaCategoria
                THEN NEXT.
            END.
            if ttentrada.buscar <> ?
            then DO:
                IF NOT contaspagarpagamento.historico MATCHES "*" + ttentrada.buscar + "*"
                THEN NEXT.
            END.
            RUN criaContasPagarPagamento.
        end.
     end.
    
end.

RUN SAIDA.
RETURN.

procedure SAIDA.
    find first ttcontaspagarpagamento no-error.

    if not avail ttcontaspagarpagamento
    then do:
        create ttsaida.
        ttsaida.tstatus = 400.
        ttsaida.descricaoStatus = "contaspagarpagamento nao encontrada".

        hsaida  = temp-table ttsaida:handle.

        lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
        message string(vlcSaida).
        return.
    end.

    hsaida  = TEMP-TABLE ttcontaspagarpagamento:handle.


    lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
    put unformatted string(vlcSaida).

END.


PROCEDURE criaContasPagarPagamento.

    vnomePortador = ?.
    vnomeCategoria = ?.
    vDocumento = ?.
    vnomePessoa = ?.
    
    FIND portador WHERE portador.idPortador = contaspagarpagamento.idPortador NO-LOCK NO-ERROR.
    IF AVAIL portador
    THEN DO:
        vnomePortador = portador.nomePortador.    
    END.
    
    FIND contascategoria WHERE contascategoria.idCategoria = contaspagarpagamento.idCategoria NO-LOCK NO-ERROR.
    IF AVAIL contascategoria
    THEN DO:
        vnomeCategoria = contascategoria.nomeCategoria.    
    END.
    
  
    FIND contaspagar WHERE contaspagar.idCP = contaspagarpagamento.idCP NO-LOCK NO-ERROR.
    IF AVAIL contaspagar
    THEN DO: 
        vsaldo = contaspagar.valorOriginal - contadorSaldo.
        vValorOriginal = contaspagar.valorOriginal.
        vDocumento = contaspagar.documento.
        
        
        FIND pessoas WHERE pessoas.idPessoa = contaspagar.idPessoaFornecedor NO-LOCK NO-ERROR.
        IF AVAIL pessoas
        THEN DO:
            FIND geralpessoas OF pessoas NO-LOCK NO-ERROR.
            IF AVAIL geralpessoas
            THEN DO:
                vnomePessoa = geralpessoas.nomePessoa.
                IF geralpessoas.nomeFantasia <> ?
                THEN DO:
                    vnomePessoa = geralpessoas.nomeFantasia.
                END.
                    
            END.
        END.
    END.
    
          
    create ttcontaspagarpagamento.
    BUFFER-COPY contaspagarpagamento TO ttcontaspagarpagamento.
    ttcontaspagarpagamento.nomePortador = vnomePortador.
    ttcontaspagarpagamento.nomeCategoria = vnomeCategoria.
    ttcontaspagarpagamento.valorOriginal = vValorOriginal.
    ttcontaspagarpagamento.saldo = vsaldo.
    ttcontaspagarpagamento.documento = vDocumento.
    ttcontaspagarpagamento.nomePessoa = vnomePessoa.

END.

