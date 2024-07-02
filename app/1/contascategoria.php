<?php

//LOG
$LOG_CAMINHO=defineCaminhoLog();
if (isset($LOG_CAMINHO)) {
    $LOG_NIVEL=defineNivelLog();
    $identificacao=date("dmYHis")."-PID".getmypid()."-"."contascategoria";
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

$categoria = array();

  $progr = new chamaprogress();
  
  // PASSANDO idEmpresa PARA PROGRESS
  if (isset($jsonEntrada['idEmpresa'])) {
      $progr->setempresa($jsonEntrada['idEmpresa']);
  }
  
  $retorno = $progr->executarprogress("financeiro/app/1/contascategoria",json_encode($jsonEntrada));
  fwrite($arquivo,$identificacao."-RETORNO->".$retorno."\n");

  $categoria = json_decode($retorno,true);
  if (isset($categoria["conteudoSaida"][0])) { // Conteudo Saida - Caso de erro
      $categoria = $categoria["conteudoSaida"][0];
  } else {
    
     if (!isset($categoria["contascategoria"][1]) && ($jsonEntrada['idCategoria'] != null)) {  // Verifica se tem mais de 1 registro
      $categoria = $categoria["contascategoria"][0]; // Retorno sem array
    } else {
      $categoria = $categoria["contascategoria"];  
    }

  }


$jsonSaida = $categoria;


//LOG
if (isset($LOG_NIVEL)) {
  if ($LOG_NIVEL >= 2) {
    fwrite($arquivo, $identificacao . "-SAIDA->" . json_encode($jsonSaida) . "\n\n");
  }
}
//LOG

fclose($arquivo);


?>

