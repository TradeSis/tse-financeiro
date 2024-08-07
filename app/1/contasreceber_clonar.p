def input param vlcentrada as longchar. /* JSON ENTRADA */
def input param vtmp       as char.     /* CAMINHO PROGRESS_TMP */

def var vlcsaida   as longchar.         /* JSON SAIDA */

def var lokjson as log.                 /* LOGICAL DE APOIO */
def var hentrada as handle.             /* HANDLE ENTRADA */
def var hsaida   as handle.             /* HANDLE SAIDA */

def temp-table ttentrada no-undo serialize-name "contasreceber"   /* JSON ENTRADA */
    like contasreceber
    FIELD vencimento AS INT
    FIELD parcelas AS INT.

def temp-table ttsaida  no-undo serialize-name "conteudoSaida"  /* JSON SAIDA CASO ERRO */
    field tstatus        as int serialize-name "status"
    field descricaoStatus      as char.

    
hEntrada = temp-table ttentrada:HANDLE.
lokJSON = hentrada:READ-JSON("longchar",vlcentrada, "EMPTY") no-error.
find first ttentrada no-error.

def var pdiavencimento as int.
def var vdia as int.
def var vmes as int.
def var vano as int.
def var ames as int.
def var aano as int.

def var vloop as int.
def var vdata as date.

vdia = ttentrada.vencimento.
pdiavencimento = vdia.

vdia  = ttentrada.vencimento.
vmes = month(ttentrada.dtVencimento).
vano = year(ttentrada.dtVencimento).

do vloop = 1 to ttentrada.parcelas.
    vdata = ?.
    vdata = date(vmes,vdia,vano) no-error.
    if vdata = ?
    then do:
        if vmes = 2 or vdia > 30
        then do:
            ames = vmes + 1.
            aano = vano.
            if ames = 13 then aano = aano + 1.
            vdata = date(ames, 01, aano) - 1.
        end.
    end.
    vmes = vmes + 1.
    if vmes = 13 then do:
        vmes = 1.
        vano = vano + 1.
    end.
    //vdia = pdiavencimento.
    do on error undo:
        create contasreceber.
        BUFFER-COPY ttentrada EXCEPT idCR TO contasreceber.
        contasreceber.dtVencimento = vdata.
    end.
     
end.

create ttsaida.
find last contasreceber no-lock.
ttsaida.tstatus = 200.
ttsaida.descricaoStatus = "Contas Pagar cadastrado com sucesso".

hsaida  = temp-table ttsaida:handle.

lokJson = hsaida:WRITE-JSON("LONGCHAR", vlcSaida, TRUE).
put unformatted string(vlcSaida).
