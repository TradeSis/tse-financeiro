<?php

//LOG
$LOG_CAMINHO=defineCaminhoLog();
if (isset($LOG_CAMINHO)) {
    $LOG_NIVEL=defineNivelLog();
    $identificacao=date("dmYHis")."-PID".getmypid()."-"."contasreceber";
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
  
  $retorno = $progr->executarprogress("financeiro/app/1/contasreceber",json_encode($jsonEntrada));
  fwrite($arquivo,$identificacao."-RETORNO->".$retorno."\n");

  $contas = json_decode($retorno,true);
  if (isset($contas["conteudoSaida"][0])) { // Conteudo Saida - Caso de erro
      $contas = $contas["conteudoSaida"][0];
  } else {
    
     if (!isset($contas["contasreceber"][1]) && ($jsonEntrada['idCR'] != null)) {  // Verifica se tem mais de 1 registro
      $contas = $contas["contasreceber"][0]; // Retorno sem array
    } else {
      $contas = $contas["contasreceber"];  
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

