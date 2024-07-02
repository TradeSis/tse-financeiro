<?php

//LOG
$LOG_CAMINHO = defineCaminhoLog();
if (isset($LOG_CAMINHO)) {
  $LOG_NIVEL = defineNivelLog();
  $identificacao = date("dmYHis") . "-PID" . getmypid() . "-" . "contascategoria_inserir";
  if (isset($LOG_NIVEL)) {
    if ($LOG_NIVEL >= 1) {
      $arquivo = fopen(defineCaminhoLog() . "financeiro_inserir" . date("dmY") . ".log", "a");
    }
  }
}
if (isset($LOG_NIVEL)) {
  if ($LOG_NIVEL == 1) {
    fwrite($arquivo, $identificacao . "\n");
  }
  if ($LOG_NIVEL >= 2) {
    fwrite($arquivo, $identificacao . "-ENTRADA->" . json_encode($jsonEntrada) . "\n");
  }
}
//LOG

  $progr = new chamaprogress();

  // PASSANDO idEmpresa PARA PROGRESS
  if (isset($jsonEntrada['idEmpresa'])) {
    $progr->setempresa($jsonEntrada['idEmpresa']);
  }

  $retorno = $progr->executarprogress("financeiro/app/1/contascategoria_inserir", json_encode($jsonEntrada));
  fwrite($arquivo, $identificacao . "-RETORNO->" . $retorno . "\n");
  $conteudoSaida = json_decode($retorno, true);
  if (isset($conteudoSaida["conteudoSaida"][0])) { // Conteudo Saida - Caso de erro
    $jsonSaida = $conteudoSaida["conteudoSaida"][0];
  }


//LOG
if (isset($LOG_NIVEL)) {
  if ($LOG_NIVEL >= 2) {
    fwrite($arquivo, $identificacao . "-SAIDA->" . json_encode($jsonSaida) . "\n\n");
  }
}
//LOG



fclose($arquivo);
