def input param vlcentrada as longchar. /* JSON ENTRADA */
def input param vtmp       as char.     /* CAMINHO PROGRESS_TMP */

def var vlcsaida   as longchar.         /* JSON SAIDA */

def var lokjson as log.                 /* LOGICAL DE APOIO */
def var hentrada as handle.             /* HANDLE ENTRADA */
def var hsaida   as handle.             /* HANDLE SAIDA */

def temp-table ttentrada no-undo serialize-name "dadosEntrada"   /* JSON ENTRADA */
    field idCR  like contasreceber.idCR
    FIELD buscar AS CHAR  
    FIELD buscaPortador AS INT
    FIELD buscaCategoria AS INT
    FIELD filtrosituacao AS CHAR
    FIELD PeriodoInicio  AS DATE
    FIELD PeriodoFim     AS DATE.

def temp-table ttcontasreceber  no-undo serialize-name "contasreceber"  /* JSON SAIDA */
    like contasreceber
    FIELD nomePortador      LIKE portador.nomePortador
    FIELD nomeCategoria     LIKE contascategoria.nomeCategoria
    FIELD nomePessoa        LIKE geralpessoas.nomePessoa
    FIELD situacao          AS CHAR
    FIELD saldo             AS DEC
    index idxvcto dtVencimento asc.

def temp-table ttsaida  no-undo serialize-name "conteudoSaida"  /* JSON SAIDA CASO ERRO */
    field tstatus        as int serialize-name "status"
    field descricaoStatus      as char.

def VAR vidCR like ttentrada.idCR.
DEF VAR vnomePortador AS CHAR.
DEF VAR vnomeCategoria AS CHAR.
DEF VAR vnomePessoa AS CHAR.
DEF VAR contadorSaldo AS DEC.
DEF VAR vsituacao AS CHAR.
DEF VAR vdtVencimento    AS DATE.
DEF VAR vdtCompetencia    AS DATE.
DEF VAR vdtLiquidacao    AS DATE.
DEF VAR vsaldo AS DEC.

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

if ttentrada.filtrosituacao = ?  AND ttentrada.idCR = ? 
then do:
    create ttsaida.
    ttsaida.tstatus = 400.
    ttsaida.descricaoStatus = "Informe uma situacao ou um ID".

    hsaida  = temp-table ttsaida:handle.

    lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
    message string(vlcSaida).
    return.

    
end.

// contasReceber UNICO
if ttentrada.idCR <> ? 
then do:
    for each contasreceber where  contasreceber.idCR =   ttentrada.idCR
        NO-LOCK.
        RUN criaContasReceber.
    end.    
    RUN SAIDA.
    RETURN.
end.

IF ttentrada.filtrosituacao = 'emaberto' 
THEN DO:

    FOR EACH portador WHERE
            (if ttentrada.buscaPortador = ?
            then true /* TODOS */
            ELSE portador.idPortador = ttentrada.buscaPortador)
            NO-LOCK:
            
            // com entrada de periodo
            IF  ttentrada.PeriodoInicio <> ? 
            THEN DO:    
                do vdtVencimento = ttentrada.PeriodoInicio TO ttentrada.PeriodoFim:
                    FOR EACH contasreceber WHERE 
                        contasreceber.idPortador     = portador.idportador AND
						contasreceber.dtLiquidacao   = ? and
                        contasreceber.dtVencimento   = vdtVencimento 
                        
                        NO-LOCK:
                     
                        if ttentrada.buscaCategoria <> ?
                        then DO:
                            if  contasreceber.idCategoria <> ttentrada.buscaCategoria
                            THEN NEXT.
                        END.
                        if ttentrada.buscar <> ?
                        then DO:
                            IF NOT contasreceber.historico MATCHES "*" + ttentrada.buscar + "*"
                            THEN NEXT.
                        END.
                        
                        RUN criaContasReceber.
           
                    end.
                 end.
            END.
            ELSE DO:
                 FOR EACH contasreceber WHERE 
                        contasreceber.idPortador = portador.idportador AND
                        contasreceber.dtLiquidacao = ?
                        NO-LOCK:
                        
                        if ttentrada.buscaCategoria <> ?
                        then DO:
                            if  contasreceber.idCategoria <> ttentrada.buscaCategoria
                            THEN NEXT.
                        END.
                        if ttentrada.buscar <> ?
                        then DO:
                            IF NOT contasreceber.historico MATCHES "*" + ttentrada.buscar + "*"
                            THEN NEXT.
                        END.
                        
                        RUN criaContasReceber.
                    end.
            END.
        
        
    end.
    RUN SAIDA.
    RETURN.
END.


IF ttentrada.filtrosituacao = 'emitida' 
THEN DO:

    FOR EACH portador WHERE
            (if ttentrada.buscaPortador = ?
            then true /* TODOS */
            ELSE portador.idPortador = ttentrada.buscaPortador)
            NO-LOCK:
            
            // com periodo
            IF  ttentrada.PeriodoInicio <> ? AND ttentrada.PeriodoFim <> ?
            THEN DO:    
                do vdtCompetencia = ttentrada.PeriodoInicio TO ttentrada.PeriodoFim:
                    FOR EACH contasreceber WHERE 
                        contasreceber.idPortador    = portador.idportador AND
                        contasreceber.dtCompetencia   = vdtCompetencia 
                        NO-LOCK:
                     
                        if ttentrada.buscaCategoria <> ?
                        then DO:
                            if  contasreceber.idCategoria <> ttentrada.buscaCategoria
                            THEN NEXT.
                        END.
                        if ttentrada.buscar <> ?
                        then DO:
                            IF NOT contasreceber.historico MATCHES "*" + ttentrada.buscar + "*"
                            THEN NEXT.
                        END.
                        
                        RUN criaContasReceber.
                    end.
                 end.
            END.       
        
    end.
    RUN SAIDA.
    RETURN.
END.

IF ttentrada.filtrosituacao = 'pagas' 
THEN DO:

    FOR EACH portador WHERE
            (if ttentrada.buscaPortador = ?
            then true /* TODOS */
            ELSE portador.idPortador = ttentrada.buscaPortador)
            NO-LOCK:
            
            // com periodo
            IF  ttentrada.PeriodoInicio <> ? AND ttentrada.PeriodoFim <> ?
            THEN DO:    
                do vdtLiquidacao = ttentrada.PeriodoInicio TO ttentrada.PeriodoFim:
                    FOR EACH contasreceber WHERE 
                        contasreceber.idPortador    = portador.idportador AND
                        contasreceber.dtLiquidacao   = vdtLiquidacao 
                        NO-LOCK:
                     
                        if ttentrada.buscaCategoria <> ?
                        then DO:
                            if  contasreceber.idCategoria <> ttentrada.buscaCategoria
                            THEN NEXT.
                        END.
                        if ttentrada.buscar <> ?
                        then DO:
                            IF NOT contasreceber.historico MATCHES "*" + ttentrada.buscar + "*"
                            THEN NEXT.
                        END.
                        
                        RUN criaContasReceber.
                    end.
                 end.
            END.       
        
    end.
    RUN SAIDA.
    RETURN.
END.

procedure SAIDA.

    find first ttcontasreceber no-error.

    if not avail ttcontasreceber
    then do:
        create ttsaida.
        ttsaida.tstatus = 400.
        ttsaida.descricaoStatus = "Conta nao encontrada".

        hsaida  = temp-table ttsaida:handle.

        lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
        message string(vlcSaida).
        return.
    end.

    hsaida  = TEMP-TABLE ttcontasreceber:handle.


    lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
    put unformatted string(vlcSaida).

END.

PROCEDURE criaContasReceber.
    vnomePortador = ?.
    vnomeCategoria = ?.
    vnomePessoa = ?.
    FIND portador WHERE portador.idPortador = contasreceber.idPortador NO-LOCK NO-ERROR.
    IF AVAIL portador
    THEN DO:
        vnomePortador = portador.nomePortador.    
    END.
    
    FIND contascategoria WHERE contascategoria.idCategoria = contasreceber.idCategoria NO-LOCK NO-ERROR.
    IF AVAIL contascategoria
    THEN DO:
        vnomeCategoria = contascategoria.nomeCategoria.    
    END.
    
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
    //contador relacionado ao acumolo de valores de contasreceberpagamento
    contadorSaldo = 0.
    vsaldo = 0.
    FOR EACH contasreceberpagamento WHERE contasreceberpagamento.idCR = contasreceber.idCR:
       contadorSaldo += contasreceberpagamento.valorPago. 
    END.
    IF contadorSaldo <> 0
    THEN DO:
        vsaldo = contasreceber.valorOriginal - contadorSaldo.
    END.
    
    IF contasreceber.dtLiquidacao = ? AND  contadorSaldo = 0
    THEN DO:
      vsituacao = 'Aberto'.  
    END.
    IF contasreceber.dtLiquidacao = ? AND  contadorSaldo <> 0 
    THEN DO:
      vsituacao = 'Parcial'.  
    END.
    IF contasreceber.dtLiquidacao <> ?
    THEN DO:
      vsituacao = 'Liquidado'.  
    END.
 

    create ttcontasreceber.
    BUFFER-COPY contasreceber TO ttcontasreceber.
    ttcontasreceber.nomePortador = vnomePortador.
    ttcontasreceber.nomeCategoria = vnomeCategoria.
    ttcontasreceber.nomePessoa = vnomePessoa.
    ttcontasreceber.situacao = vsituacao.
    ttcontasreceber.saldo = vsaldo.

END.

