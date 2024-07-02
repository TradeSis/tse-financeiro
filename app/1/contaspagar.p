def input param vlcentrada as longchar. /* JSON ENTRADA */
def input param vtmp       as char.     /* CAMINHO PROGRESS_TMP */

def var vlcsaida   as longchar.         /* JSON SAIDA */

def var lokjson as log.                 /* LOGICAL DE APOIO */
def var hentrada as handle.             /* HANDLE ENTRADA */
def var hsaida   as handle.             /* HANDLE SAIDA */

def temp-table ttentrada no-undo serialize-name "dadosEntrada"   /* JSON ENTRADA */
    field idCP  like contaspagar.idCP
    FIELD buscar AS CHAR  
    FIELD buscaPortador AS INT
    FIELD buscaCategoria AS INT
    FIELD filtrosituacao AS CHAR
    FIELD PeriodoInicio  AS DATE
    FIELD PeriodoFim     AS DATE.

def temp-table ttcontaspagar  no-undo serialize-name "contaspagar"  /* JSON SAIDA */
    like contaspagar
    FIELD nomePortador      LIKE portador.nomePortador
    FIELD nomeCategoria     LIKE contascategoria.nomeCategoria
    FIELD nomePessoa        LIKE geralpessoas.nomePessoa
    FIELD situacao          AS CHAR
    FIELD saldo             AS DEC
    index idxvcto dtVencimento asc.

def temp-table ttsaida  no-undo serialize-name "conteudoSaida"  /* JSON SAIDA CASO ERRO */
    field tstatus        as int serialize-name "status"
    field descricaoStatus      as char.

def VAR vidCP like ttentrada.idCP.
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

if ttentrada.filtrosituacao = ?  AND ttentrada.idCP = ? 
then do:
    create ttsaida.
    ttsaida.tstatus = 400.
    ttsaida.descricaoStatus = "Informe uma situacao ou um ID".

    hsaida  = temp-table ttsaida:handle.

    lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
    message string(vlcSaida).
    return.

    
end.

// contaspagar UNICO
if ttentrada.idCP <> ? 
then do:
    for each contaspagar where  contaspagar.idCP =   ttentrada.idCP
        NO-LOCK.
        RUN criaContasPagar.
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
                    FOR EACH contaspagar WHERE 
                        contaspagar.idPortador     = portador.idportador AND
						contaspagar.dtLiquidacao   = ? and
                        contaspagar.dtVencimento   = vdtVencimento 
                        
                        NO-LOCK:
                     
                        if ttentrada.buscaCategoria <> ?
                        then DO:
                            if  contaspagar.idCategoria <> ttentrada.buscaCategoria
                            THEN NEXT.
                        END.
                        if ttentrada.buscar <> ?
                        then DO:
                            IF NOT contaspagar.historico MATCHES "*" + ttentrada.buscar + "*"
                            THEN NEXT.
                        END.
                        
                        RUN criaContasPagar.
           
                    end.
                 end.
            END.
            ELSE DO:
                 FOR EACH contaspagar WHERE 
                        contaspagar.idPortador = portador.idportador AND
                        contaspagar.dtLiquidacao = ?
                        NO-LOCK:
                        
                        if ttentrada.buscaCategoria <> ?
                        then DO:
                            if  contaspagar.idCategoria <> ttentrada.buscaCategoria
                            THEN NEXT.
                        END.
                        if ttentrada.buscar <> ?
                        then DO:
                            IF NOT contaspagar.historico MATCHES "*" + ttentrada.buscar + "*"
                            THEN NEXT.
                        END.
                        
                        RUN criaContasPagar.
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
                    FOR EACH contaspagar WHERE 
                        contaspagar.idPortador    = portador.idportador AND
                        contaspagar.dtCompetencia   = vdtCompetencia
                        NO-LOCK:
                     
                        if ttentrada.buscaCategoria <> ?
                        then DO:
                            if  contaspagar.idCategoria <> ttentrada.buscaCategoria
                            THEN NEXT.
                        END.
                        if ttentrada.buscar <> ?
                        then DO:
                            IF NOT contaspagar.historico MATCHES "*" + ttentrada.buscar + "*"
                            THEN NEXT.
                        END.
                        
                        RUN criaContasPagar.
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
                    FOR EACH contaspagar WHERE 
                        contaspagar.idPortador    = portador.idportador AND
                        contaspagar.dtLiquidacao   = vdtLiquidacao 
                        NO-LOCK:
                     
                        if ttentrada.buscaCategoria <> ?
                        then DO:
                            if  contaspagar.idCategoria <> ttentrada.buscaCategoria
                            THEN NEXT.
                        END.
                        if ttentrada.buscar <> ?
                        then DO:
                            IF NOT contaspagar.historico MATCHES "*" + ttentrada.buscar + "*"
                            THEN NEXT.
                        END.
                        
                        RUN criaContasPagar.
                    end.
                 end.
            END.       
        
    end.
    RUN SAIDA.
    RETURN.
END.

procedure SAIDA.

    find first ttcontaspagar no-error.

    if not avail ttcontaspagar
    then do:
        create ttsaida.
        ttsaida.tstatus = 400.
        ttsaida.descricaoStatus = "Conta nao encontrada".

        hsaida  = temp-table ttsaida:handle.

        lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
        message string(vlcSaida).
        return.
    end.

    hsaida  = TEMP-TABLE ttcontaspagar:handle.


    lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
    put unformatted string(vlcSaida).

END.

PROCEDURE criaContasPagar.
    vnomePortador = ?.
    vnomeCategoria = ?.
    vnomePessoa = ?.
    FIND portador WHERE portador.idPortador = contaspagar.idPortador NO-LOCK NO-ERROR.
    IF AVAIL portador
    THEN DO:
        vnomePortador = portador.nomePortador.    
    END.
    
    FIND contascategoria WHERE contascategoria.idCategoria = contaspagar.idCategoria NO-LOCK NO-ERROR.
    IF AVAIL contascategoria
    THEN DO:
        vnomeCategoria = contascategoria.nomeCategoria.    
    END.
    
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
    //contador relacionado ao acumolo de valores de contaspagarpagamento
    contadorSaldo = 0.
    vsaldo = 0.
    FOR EACH contaspagarpagamento WHERE contaspagarpagamento.idCP = contaspagar.idCP:
       contadorSaldo += contaspagarpagamento.valorPago. 
    END.
    IF contadorSaldo <> 0
    THEN DO:
        vsaldo = contaspagar.valorOriginal - contadorSaldo.
    END.
    
    IF contaspagar.dtLiquidacao = ? AND  contadorSaldo = 0
    THEN DO:
      vsituacao = 'Aberto'.  
    END.
    IF contaspagar.dtLiquidacao = ? AND  contadorSaldo <> 0 
    THEN DO:
      vsituacao = 'Parcial'.  
    END.
    IF contaspagar.dtLiquidacao <> ?
    THEN DO:
      vsituacao = 'Liquidado'.  
    END.
 

    create ttcontaspagar.
    BUFFER-COPY contaspagar TO ttcontaspagar.
    ttcontaspagar.nomePortador = vnomePortador.
    ttcontaspagar.nomeCategoria = vnomeCategoria.
    ttcontaspagar.nomePessoa = vnomePessoa.
    ttcontaspagar.situacao = vsituacao.
    ttcontaspagar.saldo = vsaldo.

END.

