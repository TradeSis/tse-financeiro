def input param vlcentrada as longchar. /* JSON ENTRADA */
def input param vtmp       as char.     /* CAMINHO PROGRESS_TMP */

def var vlcsaida   as longchar.         /* JSON SAIDA */

def var lokjson as log.                 /* LOGICAL DE APOIO */
def var hentrada as handle.             /* HANDLE ENTRADA */
def var hsaida   as handle.             /* HANDLE SAIDA */

def temp-table ttentrada no-undo serialize-name "dadosEntrada"   /* JSON ENTRADA */
    field idCrPgto  like contasreceberpagamento.idCrPgto
    field idCR      like contasreceber.idCR
    FIELD buscar AS CHAR
    FIELD buscaPortador AS INT
    FIELD buscaCategoria AS INT
    FIELD PeriodoInicio  AS DATE
    FIELD PeriodoFim     AS DATE.
    
def TEMP-TABLE ttcontasreceberpagamento  no-undo serialize-name "contasreceberpagamento"  /* JSON SAIDA */
    like contasreceberpagamento
    FIELD nomePortador           LIKE portador.nomePortador
    FIELD nomeCategoria          LIKE contascategoria.nomeCategoria
    FIELD valorOriginal          LIKE contasreceber.valorOriginal
    FIELD documento              LIKE contasreceber.documento
    FIELD nomePessoa             LIKE geralpessoas.nomePessoa
    FIELD saldo                  AS DEC.                    

def temp-table ttsaida  no-undo serialize-name "conteudoSaida"  /* JSON SAIDA CASO ERRO */
    field tstatus        as int serialize-name "status"
    field descricaoStatus      as char.

def VAR vidCrPgto like ttentrada.idCrPgto.

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

if ttentrada.PeriodoInicio = ?  AND ttentrada.idCR = ? AND ttentrada.idCrPgto = ?
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
// contasReceber UNICO
if ttentrada.idCR <> ? 
then do:
    for each contasreceberpagamento where  contasreceberpagamento.idCR =   ttentrada.idCR
        NO-LOCK.
        
        contadorSaldo = contadorSaldo + contasreceberpagamento.valorPago.
       
        // contasReceber UNICO com contasReceberPagamento UNICO
        if ttentrada.idCrPgto <> ? 
        then do:
            if contasreceberpagamento.idCrPgto <> ttentrada.idCrPgto  
            THEN NEXT.
        end.
        RUN criaContasReceberPagamento.
    end.    
    RUN SAIDA.
    RETURN.
end.

// contasReceberPagamento UNICO
if ttentrada.idCrPgto <> ? 
then do:
    FIND contasreceberpagamento where  contasreceberpagamento.idCrPgto =   ttentrada.idCrPgto NO-LOCK NO-ERROR.
    if AVAIL contasreceberpagamento 
    then do:
        RUN criaContasReceberPagamento.    
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
        FOR EACH contasReceberPagamento WHERE 
            contasReceberPagamento.idPortador    = portador.idportador AND
            contasReceberPagamento.dtPagamento   = vdtPagamento 
            NO-LOCK:
            if ttentrada.buscaCategoria <> ?
            then DO:
                if  contasReceberPagamento.idCategoria <> ttentrada.buscaCategoria
                THEN NEXT.
            END.
            if ttentrada.buscar <> ?
            then DO:
                IF NOT contasreceberpagamento.historico MATCHES "*" + ttentrada.buscar + "*"
                THEN NEXT.
            END.
            RUN criaContasReceberPagamento.
        end.
     end.
    
end.

RUN SAIDA.
RETURN.

procedure SAIDA.
    find first ttcontasreceberpagamento no-error.

    if not avail ttcontasreceberpagamento
    then do:
        create ttsaida.
        ttsaida.tstatus = 400.
        ttsaida.descricaoStatus = "contasreceberpagamento nao encontrada".

        hsaida  = temp-table ttsaida:handle.

        lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
        message string(vlcSaida).
        return.
    end.

    hsaida  = TEMP-TABLE ttcontasreceberpagamento:handle.


    lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
    put unformatted string(vlcSaida).

END.


PROCEDURE criaContasReceberPagamento.

    vnomePortador = ?.
    vnomeCategoria = ?.
    vDocumento = ?.
    vnomePessoa = ?.
    
    FIND portador WHERE portador.idPortador = contasreceberpagamento.idPortador NO-LOCK NO-ERROR.
    IF AVAIL portador
    THEN DO:
        vnomePortador = portador.nomePortador.    
    END.
    
    FIND contascategoria WHERE contascategoria.idCategoria = contasreceberpagamento.idCategoria NO-LOCK NO-ERROR.
    IF AVAIL contascategoria
    THEN DO:
        vnomeCategoria = contascategoria.nomeCategoria.    
    END.
    
  
    FIND contasreceber WHERE contasreceber.idCR = contasreceberpagamento.idCR NO-LOCK NO-ERROR.
    IF AVAIL contasreceber
    THEN DO: 
        vsaldo = contasreceber.valorOriginal - contadorSaldo.
        vValorOriginal = contasreceber.valorOriginal.
        vDocumento = contasreceber.documento.
        
        
        FIND pessoas WHERE pessoas.idPessoa = contasreceber.idPessoaFornecedor NO-LOCK NO-ERROR.
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
    
          
    create ttcontasreceberpagamento.
    BUFFER-COPY contasreceberpagamento TO ttcontasreceberpagamento.
    ttcontasreceberpagamento.nomePortador = vnomePortador.
    ttcontasreceberpagamento.nomeCategoria = vnomeCategoria.
    ttcontasreceberpagamento.valorOriginal = vValorOriginal.
    ttcontasreceberpagamento.saldo = vsaldo.
    ttcontasreceberpagamento.documento = vDocumento.
    ttcontasreceberpagamento.nomePessoa = vnomePessoa.

END.

