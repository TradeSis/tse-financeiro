def input param vlcentrada as longchar. /* JSON ENTRADA */
def input param vtmp       as char.     /* CAMINHO PROGRESS_TMP */

def var vlcsaida   as longchar.         /* JSON SAIDA */

def var lokjson as log.                 /* LOGICAL DE APOIO */
def var hentrada as handle.             /* HANDLE ENTRADA */
def var hsaida   as handle.             /* HANDLE SAIDA */

def temp-table ttentrada no-undo serialize-name "dadosEntrada"   /* JSON ENTRADA */
    field idPortador  like portador.idPortador
    field buscaPortador as char.

def temp-table ttportador  no-undo serialize-name "portador"  /* JSON SAIDA */
    like portador.

def temp-table ttsaida  no-undo serialize-name "conteudoSaida"  /* JSON SAIDA CASO ERRO */
    field tstatus        as int serialize-name "status"
    field descricaoStatus      as char.

def VAR vidPortador like ttentrada.idPortador.


hEntrada = temp-table ttentrada:HANDLE.
lokJSON = hentrada:READ-JSON("longchar",vlcentrada, "EMPTY") no-error.
find first ttentrada no-error.

vidPortador = 0.
if avail ttentrada
then do:
    vidPortador = ttentrada.idPortador.
    if vidPortador = ? then vidPortador = 0.
end.

IF ttentrada.idPortador <> ? OR (ttentrada.idPortador = ? AND ttentrada.buscaPortador = ?)
THEN DO:
    for each portador where
        (if vidPortador = 0
        then true /* TODOS */
        ELSE portador.idPortador = vidPortador)
        no-lock.
       
        RUN criaPortador.

    end.
END.

IF ttentrada.buscaPortador <> ? AND (ttentrada.idPortador = ?)
THEN DO: 
      vidPortador = INT(ttentrada.buscaPortador) no-error.
      for each portador WHERE 
        portador.idPortador = vidPortador OR
        portador.nomePortador MATCHES "*" + ttentrada.buscaPortador + "*"
      no-lock.
        
      RUN criaPortador.

    end.
END.

find first ttportador no-error.

if not avail ttportador
then do:
    create ttsaida.
    ttsaida.tstatus = 400.
    ttsaida.descricaoStatus = "Portador nao encontrada".

    hsaida  = temp-table ttsaida:handle.

    lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
    message string(vlcSaida).
    return.
end.

hsaida  = TEMP-TABLE ttportador:handle.


lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
put unformatted string(vlcSaida).


PROCEDURE criaPortador.

    create ttportador.
    BUFFER-COPY portador TO ttportador.

END.
