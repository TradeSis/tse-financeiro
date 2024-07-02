<?php

//LOG
$LOG_CAMINHO=defineCaminhoLog();
if (isset($LOG_CAMINHO)) {
    $LOG_NIVEL=defineNivelLog();
    $identificacao=date("dmYHis")."-PID".getmypid()."-"."portador";
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

$portador = array();

  $progr = new chamaprogress();
  
  // PASSANDO idEmpresa PARA PROGRESS
  if (isset($jsonEntrada['idEmpresa'])) {
      $progr->setempresa($jsonEntrada['idEmpresa']);
  }
  
  $retorno = $progr->executarprogress("financeiro/app/1/portador",json_encode($jsonEntrada));
  fwrite($arquivo,$identificacao."-RETORNO->".$retorno."\n");

  $portador = json_decode($retorno,true);
  if (isset($portador["conteudoSaida"][0])) { // Conteudo Saida - Caso de erro
      $portador = $portador["conteudoSaida"][0];
  } else {
    
     if (!isset($portador["portador"][1]) && ($jsonEntrada['idPortador'] != null)) {  // Verifica se tem mais de 1 registro
      $portador = $portador["portador"][0]; // Retorno sem array
    } else {
      $portador = $portador["portador"];  
    }

  }


$jsonSaida = $portador;


//LOG
if (isset($LOG_NIVEL)) {
  if ($LOG_NIVEL >= 2) {
    fwrite($arquivo, $identificacao . "-SAIDA->" . json_encode($jsonSaida) . "\n\n");
  }
}
//LOG

fclose($arquivo);


?>

