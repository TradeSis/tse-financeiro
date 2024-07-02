def input param vlcentrada as longchar. /* JSON ENTRADA */
def input param vtmp       as char.     /* CAMINHO PROGRESS_TMP */

def var vlcsaida   as longchar.         /* JSON SAIDA */

def var lokjson as log.                 /* LOGICAL DE APOIO */
def var hentrada as handle.             /* HANDLE ENTRADA */
def var hsaida   as handle.             /* HANDLE SAIDA */

def temp-table ttentrada no-undo serialize-name "contasreceberpagamento"   /* JSON ENTRADA */
    field idCrPgto   like contasreceberpagamento.idCrPgto
    field idCR       like contasreceber.idCR.

def temp-table ttsaida  no-undo serialize-name "conteudoSaida"  /* JSON SAIDA CASO ERRO */
    field tstatus        as int serialize-name "status"
    field descricaoStatus      as char.

hEntrada = temp-table ttentrada:HANDLE.
lokJSON = hentrada:READ-JSON("longchar",vlcentrada, "EMPTY") no-error.
find first ttentrada no-error.

DEF VAR contadorSaldo AS DEC.
DEF VAR vdataLiquidado AS DATE.

if not avail ttentrada
then do:
    create ttsaida.
    ttsaida.tstatus = 400.
    ttsaida.descricaoStatus = "Dados de Entrada nao encontrados".

    hsaida  = temp-table ttsaida:handle.

    lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
    message string(vlcSaida).
    return.
end.

if ttentrada.idCrPgto = ?
then do:
    create ttsaida.
    ttsaida.tstatus = 400.
    ttsaida.descricaoStatus = "Dados de Entrada Invalidos".

    hsaida  = temp-table ttsaida:handle.

    lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
    message string(vlcSaida).
    return.
end.

find contasreceberpagamento where contasreceberpagamento.idCrPgto = ttentrada.idCrPgto no-lock no-error.
if not avail contasreceberpagamento
then do:
    create ttsaida.
    ttsaida.tstatus = 400.
    ttsaida.descricaoStatus = "contasreceberpagamento nao deletado".

    hsaida  = temp-table ttsaida:handle.

    lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
    message string(vlcSaida).
    return.
end.


do on error undo:
    find contasreceberpagamento where contasreceberpagamento.idCrPgto = ttentrada.idCrPgto.
    DELETE contasreceberpagamento. 
end.

// BLOCO ATUALIZA CONTASRECEBER
do on error undo: 
    contadorSaldo = 0.
    vdataLiquidado = ?.
    
    FOR EACH contasreceberpagamento WHERE contasreceberpagamento.idCR = ttentrada.idCR:
       contadorSaldo += contasreceberpagamento.valorPago. 
    END.
    
    find contasreceber where contasreceber.idCR = ttentrada.idCR EXCLUSIVE no-error.
    
    IF contadorSaldo = contasreceber.valorOriginal
    THEN DO:
        vdataLiquidado = DATE(TODAY).
    END.
    
    contasreceber.dtLiquidacao = vdataLiquidado.
    contasreceber.valorPago = contadorSaldo.
    
end.

create ttsaida.
ttsaida.tstatus = 200.
ttsaida.descricaoStatus = "contasreceberpagamento deletado com sucesso".

hsaida  = temp-table ttsaida:handle.

lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
put unformatted string(vlcSaida).
