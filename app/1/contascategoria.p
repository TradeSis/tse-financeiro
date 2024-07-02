def input param vlcentrada as longchar. /* JSON ENTRADA */
def input param vtmp       as char.     /* CAMINHO PROGRESS_TMP */

def var vlcsaida   as longchar.         /* JSON SAIDA */

def var lokjson as log.                 /* LOGICAL DE APOIO */
def var hentrada as handle.             /* HANDLE ENTRADA */
def var hsaida   as handle.             /* HANDLE SAIDA */

def temp-table ttentrada no-undo serialize-name "dadosEntrada"   /* JSON ENTRADA */
    field idCategoria  like contascategoria.idCategoria
    field tipo  like contascategoria.tipo
    field buscaContaCategoria as char.

def temp-table ttcontascategoria  no-undo serialize-name "contascategoria"  /* JSON SAIDA */
    like contascategoria.

def temp-table ttsaida  no-undo serialize-name "conteudoSaida"  /* JSON SAIDA CASO ERRO */
    field tstatus        as int serialize-name "status"
    field descricaoStatus      as char.

def VAR vidCategoria like ttentrada.idCategoria.


hEntrada = temp-table ttentrada:HANDLE.
lokJSON = hentrada:READ-JSON("longchar",vlcentrada, "EMPTY") no-error.
find first ttentrada no-error.

vidCategoria = 0.
if avail ttentrada
then do:
    vidCategoria = ttentrada.idCategoria.
    if vidCategoria = ? then vidCategoria = 0.
end.

IF ttentrada.idCategoria <> ? OR (ttentrada.idCategoria = ? AND ttentrada.buscaContaCategoria = ? AND ttentrada.tipo = ?)
THEN DO:
    for each contascategoria where
        (if vidCategoria = 0
        then true /* TODOS */
        ELSE contascategoria.idCategoria = vidCategoria)
        no-lock.

        
        RUN criaContasCategoria.

    end.
END.

IF ttentrada.buscaContaCategoria <> ? AND (ttentrada.idCategoria = ? AND ttentrada.tipo = ?)
THEN DO: 
      vidCategoria = INT(ttentrada.buscaContaCategoria) no-error.
      for each contascategoria WHERE 
        contascategoria.idCategoria = vidCategoria OR
        contascategoria.nomeCategoria MATCHES "*" + ttentrada.buscaContaCategoria + "*"
      no-lock.
        
      RUN criaContasCategoria.

    end.
END.

IF ttentrada.tipo <> ? AND (ttentrada.idCategoria = ? AND ttentrada.buscaContaCategoria = ?)
THEN DO: 
      for each contascategoria WHERE 
        contascategoria.tipo = ttentrada.tipo
      no-lock.
        
      RUN criaContasCategoria.

    end.
END.

find first ttcontascategoria no-error.

if not avail ttcontascategoria
then do:
    create ttsaida.
    ttsaida.tstatus = 400.
    ttsaida.descricaoStatus = "Categoria nao encontrada".

    hsaida  = temp-table ttsaida:handle.

    lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
    message string(vlcSaida).
    return.
end.

hsaida  = TEMP-TABLE ttcontascategoria:handle.


lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
put unformatted string(vlcSaida).


PROCEDURE criaContasCategoria.

    create ttcontascategoria.
    BUFFER-COPY contascategoria TO ttcontascategoria.

END.
