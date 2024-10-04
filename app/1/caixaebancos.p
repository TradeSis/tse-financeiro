DEF VAR hSaida  AS HANDLE.
DEF VAR lcSaida AS LONGCHAR.

DEF INPUT PARAM vlcentrada as longchar. /* JSON ENTRADA */
DEF INPUT PARAM vtmp       as char.     /* CAMINHO PROGRESS_TMP */

def var lokjson as log.                 /* LOGICAL DE APOIO */
def var hentrada as handle.             /* HANDLE ENTRADA */


RUN LOG("INICIO").
  
def temp-table ttentrada NO-UNDO   serialize-name "entradaDados"/* JSON ENTRADA */
    field anoEntrada  AS INT
    FIELD mesEntrada  AS INT
    FIELD portador    AS INT.

def temp-table ttcontaspagarpagamento  no-undo serialize-name "contaspagarpagamento"  /* JSON SAIDA */
    like contaspagarpagamento.
    
def temp-table ttcontasreceberpagamento  no-undo serialize-name "contasreceberpagamento"  /* JSON SAIDA */
    like contasreceberpagamento.
    
def temp-table ttcaixaebancos  no-undo serialize-name "caixaebancos"  /* JSON SAIDA */
    FIELD cbdtPagamento AS DATE
    FIELD cbhistorico   AS CHAR
    FIELD cbvalorPago   AS DEC
    FIELD cbcategoria   AS CHAR
    FIELD cbportador  AS CHAR
    FIELD cbportadorDestino  AS CHAR
    FIELD cbidentificador  AS CHAR
    FIELD cbqtd AS INT
    FIELD cbdocumento AS CHAR
    FIELD cbnomePessoaFornecedor AS CHAR
    FIELD id_recid AS INT64
    index dt is primary cbdtpagamento asc.
    
 
DEF VAR vdtini  AS DATE.
DEF VAR vdtfim  AS DATE.
DEF VAR vidx_imposto    AS CHAR.
DEF VAR vidx-cst    AS CHAR.
DEF VAR vqtd AS INT.
  
def var vvalor as dec.
def buffer bttcaixaebancos for ttcaixaebancos.


hEntrada = temp-table ttentrada:HANDLE.
lokJSON = hentrada:READ-JSON("longchar",vlcentrada, "EMPTY") no-error.
find first ttentrada NO-ERROR.
if NOT AVAIL ttentrada then do:
    RETURN.
end.

    
RUN LOG("PORTADOR: " + string(ttentrada.portador)).
RUN LOG("ANO: " + string(ttentrada.anoEntrada)).
RUN LOG("MES: " + string(ttentrada.mesEntrada)).

/* primeiro dia de um mes */
vdtini = DATE(ttentrada.mesEntrada,01,ttentrada.anoEntrada).
/* ultimo dia de um mes */
vdtfim = DATE(IF ttentrada.mesEntrada + 1 = 13 THEN 1 ELSE ttentrada.mesEntrada + 1,01,
              IF ttentrada.mesEntrada + 1 = 13 THEN ttentrada.anoEntrada + 1 ELSE ttentrada.anoEntrada) - 1.

RUN LOG("VDTINI: " + string(vdtini)).
RUN LOG("VDTFIM: " + string(vdtfim)).

vvalor = 0.
// SALDO - SAIDA
FOR EACH contaspagarpagamento WHERE 
        contaspagarpagamento.idPortador = ttentrada.portador AND
        contaspagarpagamento.dtPagamento < vdtini 
        NO-LOCK:
       
       vvalor = vvalor - contaspagarpagamento.valorPago.
END.

// SALDO - ENTRADA
FOR EACH contasreceberpagamento WHERE 
        contasreceberpagamento.idPortador = ttentrada.portador AND
        contasreceberpagamento.dtPagamento < vdtini 
        NO-LOCK:
       
       vvalor = vvalor + contasreceberpagamento.valorPago.
END.
// SALDO - SAIDA
FOR EACH cbtransferencia WHERE 
         cbtransferencia.idPortadorOrigem = ttentrada.portador AND
         cbtransferencia.data < vdtini 
         NO-LOCK:
       
       vvalor = vvalor - cbtransferencia.valor.
END.
// SALDO - ENTRADA
FOR EACH cbtransferencia WHERE 
         cbtransferencia.idPortadorDestino = ttentrada.portador AND
         cbtransferencia.data < vdtini 
         NO-LOCK:
       
         vvalor = vvalor + cbtransferencia.valor.
END.

        create bttcaixaebancos.
        bttcaixaebancos.cbdtPagamento = vdtini - 1.
        bttcaixaebancos.cbvalorPago = vvalor.
        bttcaixaebancos.cbidentificador = "TOTAL".
        
        RUN LOG("bttcaixaebancos.cbdtPagamento" + STRING(bttcaixaebancos.cbdtPagamento)).
        RUN LOG("bttcaixaebancos.cbvalorPago" + STRING(bttcaixaebancos.cbvalorPago)).

DEF VAR vportadorOrigem AS CHAR.
DEF VAR vportadorDestino AS CHAR.
FOR EACH cbtransferencia WHERE 
        cbtransferencia.idPortadorOrigem = ttentrada.portador AND
        cbtransferencia.data >= vdtini AND 
        cbtransferencia.data <= vdtfim
        NO-LOCK:
        
        FIND portador WHERE portador.idPortador = cbtransferencia.idPortadorOrigem NO-LOCK.
        vportadorOrigem = portador.nomePortador.
        
        FIND portador WHERE portador.idPortador = cbtransferencia.idPortadorDestino NO-LOCK.
        vportadorDestino = portador.nomePortador.
        
        create bttcaixaebancos.
        bttcaixaebancos.cbdtPagamento = cbtransferencia.data.
        bttcaixaebancos.cbvalorPago = cbtransferencia.valor.
        bttcaixaebancos.cbportador = vportadorOrigem.
        bttcaixaebancos.cbportadorDestino = vportadorDestino.
        bttcaixaebancos.cbidentificador = "TRS".
        bttcaixaebancos.id_recid = RECID(cbtransferencia).
END.


FOR EACH cbtransferencia WHERE 
        cbtransferencia.idPortadorDestino = ttentrada.portador AND
        cbtransferencia.data >= vdtini AND 
        cbtransferencia.data <= vdtfim
        NO-LOCK:
           
        FIND portador WHERE portador.idPortador = cbtransferencia.idPortadorOrigem NO-LOCK.
        vportadorOrigem = portador.nomePortador.
        
        FIND portador WHERE portador.idPortador = cbtransferencia.idPortadorDestino NO-LOCK.
        vportadorDestino = portador.nomePortador.
         
        create bttcaixaebancos.
        bttcaixaebancos.cbdtPagamento = cbtransferencia.data.
        bttcaixaebancos.cbvalorPago = cbtransferencia.valor.
        bttcaixaebancos.cbportador = vportadorOrigem.
        bttcaixaebancos.cbportadorDestino = vportadorDestino.
        bttcaixaebancos.cbidentificador = "TRE".
        bttcaixaebancos.id_recid = RECID(cbtransferencia).
END.
  

FOR EACH contaspagarpagamento WHERE 
        contaspagarpagamento.idPortador = ttentrada.portador AND
        contaspagarpagamento.dtPagamento >= vdtini AND 
        contaspagarpagamento.dtPagamento <= vdtfim
        NO-LOCK:
            
        CREATE ttcontaspagarpagamento.
        BUFFER-COPY contaspagarpagamento TO ttcontaspagarpagamento.
            
        FIND contascategoria WHERE contascategoria.idCategoria = ttcontaspagarpagamento.idCategoria NO-LOCK.
        FIND portador WHERE portador.idPortador = ttcontaspagarpagamento.idPortador NO-LOCK.
        FIND contaspagar OF ttcontaspagarpagamento.
        FIND pessoas WHERE pessoa.idpessoa = contaspagar.idPessoaFornecedor.
        FIND geralpessoas OF pessoas.
                
        CREATE ttcaixaebancos.
        ttcaixaebancos.cbdtPagamento = ttcontaspagarpagamento.dtPagamento.
        ttcaixaebancos.cbhistorico = ttcontaspagarpagamento.historico.
        ttcaixaebancos.cbvalorPago = ttcontaspagarpagamento.valorPago.
        ttcaixaebancos.cbcategoria = contascategoria.nomeCategoria.
        ttcaixaebancos.cbportador = portador.nomePortador.
        ttcaixaebancos.cbdocumento = contaspagar.documento.
        ttcaixaebancos.cbnomePessoaFornecedor = geralpessoas.nomeFantasia.
        ttcaixaebancos.cbidentificador = "CP".
END.  


FOR EACH contasreceberpagamento WHERE 
        contasreceberpagamento.idPortador = ttentrada.portador AND
        contasreceberpagamento.dtPagamento >= vdtini AND 
        contasreceberpagamento.dtPagamento <= vdtfim
        NO-LOCK:
  
        CREATE ttcontasreceberpagamento.
        BUFFER-COPY contasreceberpagamento TO ttcontasreceberpagamento.
        
        FIND contascategoria WHERE contascategoria.idCategoria = ttcontasreceberpagamento.idCategoria NO-LOCK. 
        FIND portador WHERE portador.idPortador = ttcontasreceberpagamento.idPortador NO-LOCK.
        FIND contasreceber OF ttcontasreceberpagamento.
        FIND pessoas WHERE pessoa.idpessoa = contasreceber.idPessoaFornecedor.
        FIND geralpessoas OF pessoas.
        
        CREATE ttcaixaebancos.
        ttcaixaebancos.cbdtPagamento = ttcontasreceberpagamento.dtPagamento.
        ttcaixaebancos.cbhistorico = ttcontasreceberpagamento.historico.
        ttcaixaebancos.cbvalorPago = ttcontasreceberpagamento.valorPago.
        ttcaixaebancos.cbcategoria = contascategoria.nomeCategoria.
        ttcaixaebancos.cbportador = portador.nomePortador.
        ttcaixaebancos.cbdocumento = contasreceber.documento.
        ttcaixaebancos.cbnomePessoaFornecedor = geralpessoas.nomeFantasia.
        ttcaixaebancos.cbidentificador = "CR".
END.

vqtd =0.   
for each ttcaixaebancos WHERE  ttcaixaebancos.cbdtPagamento >= vdtini AND 
                               ttcaixaebancos.cbdtPagamento <= vdtfim AND
                               ttcaixaebancos.cbidentificador <> "TOTAL"
                               break by ttcaixaebancos.cbdtPagamento.
    
    IF ttcaixaebancos.cbidentificador = "CP" OR ttcaixaebancos.cbidentificador = "TRS"
    THEN DO:
          vvalor = vvalor - ttcaixaebancos.cbvalorPago.
    END.
    IF ttcaixaebancos.cbidentificador = "CR" OR ttcaixaebancos.cbidentificador = "TRE" 
    THEN DO:
          vvalor = vvalor + ttcaixaebancos.cbvalorPago.
    END.
    
    
    vqtd = vqtd + 1.
    if last-of(ttcaixaebancos.cbdtPagamento)
    then do:
        create bttcaixaebancos.
        bttcaixaebancos.cbdtPagamento = ttcaixaebancos.cbdtPagamento.
        bttcaixaebancos.cbvalorPago = vvalor.
        bttcaixaebancos.cbidentificador = "TOTAL". 
        bttcaixaebancos.cbqtd = vqtd.
       
    end.
end. 
 
hsaida  = TEMP-TABLE ttcaixaebancos:handle.


hsaida:WRITE-JSON("LONGCHAR", lcSaida, TRUE).
put unformatted string(lcSaida).



procedure LOG.
    DEF INPUT PARAM vmensagem AS CHAR.    
    OUTPUT TO VALUE(vtmp + "/financeiro_caixaebancos_" + string(today,"99999999") + ".log") APPEND.
        PUT UNFORMATTED 
            STRING (TIME,"HH:MM:SS")
            " progress -> " vmensagem
            SKIP.
    OUTPUT CLOSE.
    
END PROCEDURE.


