def input param vlcentrada as longchar. /* JSON ENTRADA */
def input param vtmp       as char.     /* CAMINHO PROGRESS_TMP */

def var vlcsaida   as longchar.         /* JSON SAIDA */

def var lokjson as log.                 /* LOGICAL DE APOIO */
def var hentrada as handle.             /* HANDLE ENTRADA */
def var hsaida   as handle.             /* HANDLE SAIDA */

def temp-table ttentrada no-undo serialize-name "contaspagarpagamento"   /* JSON ENTRADA */
    like contaspagarpagamento.
    
def temp-table ttsaida  no-undo serialize-name "conteudoSaida"  /* JSON SAIDA CASO ERRO */
    field tstatus        as int serialize-name "status"
    field descricaoStatus      as char.

DEF VAR contadorSaldo AS DEC.
DEF VAR vdataLiquidado AS DATE.

hEntrada = temp-table ttentrada:HANDLE.
lokJSON = hentrada:READ-JSON("longchar",vlcentrada, "EMPTY") no-error.

find first ttentrada no-error.
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

do on error undo:
  create contaspagarpagamento.
  BUFFER-COPY ttentrada EXCEPT idCpPgto TO contaspagarpagamento.
end.

// BLOCO ATUALIZA CONTASPAGAR
do on error undo: 
    contadorSaldo = 0.
    vdataLiquidado = ?.
        
    FOR EACH contaspagarpagamento WHERE contaspagarpagamento.idCP = ttentrada.idCP:
        contadorSaldo += contaspagarpagamento.valorPago. 
    END.
      
    find contaspagar where contaspagar.idCP = ttentrada.idCP EXCLUSIVE no-error.
        
    IF contadorSaldo = contaspagar.valorOriginal
    THEN DO:
        vdataLiquidado = DATE(TODAY).
    END.
        
    contaspagar.dtLiquidacao = vdataLiquidado.
    contaspagar.valorPago = contadorSaldo.
        
end.


create ttsaida.
ttsaida.tstatus = 200.
ttsaida.descricaoStatus = "contaspagarpagamento criado com sucesso".

hsaida  = temp-table ttsaida:handle.

lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
put unformatted string(vlcSaida).
