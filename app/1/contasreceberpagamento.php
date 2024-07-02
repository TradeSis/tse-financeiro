<?php

//LOG
$LOG_CAMINHO=defineCaminhoLog();
if (isset($LOG_CAMINHO)) {
    $LOG_NIVEL=defineNivelLog();
    $identificacao=date("dmYHis")."-PID".getmypid()."-"."contasreceberpagamento";
    if(isset($LOG_NIVEL)) {
        if ($LOG_NIVEL>=1) {
            $arquivo = fopen(defineCaminhoLog()."financeiro_".date("dmY").".log","a");
        }
    }
    
}
if(isset($LOG_NIVEL)) {
    if ($LOG_NIVEL==1) {
        fwrite($arquivo,$identificacao."\n");
    }
    if ($LOG_NIVEL>=2) {
        fwrite($arquivo,$identificacao."-ENTRADA->".json_encode($jsonEntrada)."\n");
    }
}
//LOG

$contas = array();

  $progr = new chamaprogress();
  
  // PASSANDO idEmpresa PARA PROGRESS
  if (isset($jsonEntrada['idEmpresa'])) {
      $progr->setempresa($jsonEntrada['idEmpresa']);
  }
  
  $retorno = $progr->executarprogress("financeiro/app/1/contasreceberpagamento",json_encode($jsonEntrada));
  fwrite($arquivo,$identificacao."-RETORNO->".$retorno."\n");

  $contas = json_decode($retorno,true);
  if (isset($contas["conteudoSaida"][0])) { // Conteudo Saida - Caso de erro
      $contas = $contas["conteudoSaida"][0];
  } else {
    
     if (!isset($contas["contasreceberpagamento"][1]) && ($jsonEntrada['idCrPgto'] != null)) {  // Verifica se tem mais de 1 registro
      $contas = $contas["contasreceberpagamento"][0]; // Retorno sem array
    } else {
      $contas = $contas["contasreceberpagamento"];  
    }

  }


$jsonSaida = $contas;


//LOG
if (isset($LOG_NIVEL)) {
  if ($LOG_NIVEL >= 2) {
    fwrite($arquivo, $identificacao . "-SAIDA->" . json_encode($jsonSaida) . "\n\n");
  }
}
//LOG

fclose($arquivo);


?>

