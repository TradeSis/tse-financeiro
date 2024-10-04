<?php

//echo "metodo=".$metodo."\n";
//echo "funcao=".$funcao."\n";
//echo "parametro=".$parametro."\n";

if ($metodo == "GET") {

  switch ($funcao) {

    case "contasreceber":
      include 'contasreceber.php';
      break;

    case "contaspagar":
      include 'contaspagar.php';
      break;

    case "contaspagarpagamento":
      include 'contaspagarpagamento.php';
      break;

    case "contascategoria":
      include 'contascategoria.php';
      break;
      
    case "portador":
      include 'portador.php';
      break; 

    case "contasreceber":
      include 'contasreceber.php';
      break;

    case "contasreceberpagamento":
      include 'contasreceberpagamento.php';
      break;

    case "caixaebancos":
      include 'caixaebancos.php';
      break;
      
    default:
      $jsonSaida = json_decode(json_encode(
        array(
          "status" => "400",
          "retorno" => "Aplicacao " . $aplicacao . " Versao " . $versao . " Funcao " . $funcao . " Invalida" . " Metodo " . $metodo . " Invalido "
        )
      ), TRUE);
      break;
  }
}

if ($metodo == "PUT") {
  switch ($funcao) {

    case "contasreceber":
      include 'contasreceber_inserir.php';
      break;

    case "contasreceber_clonar":
      include 'contasreceber_clonar.php';
      break;

    case "contaspagar":
      include 'contaspagar_inserir.php';
      break;

    case "contaspagar_clonar":
      include 'contaspagar_clonar.php';
      break;

    case "contaspagarpagamento":
      include 'contaspagarpagamento_inserir.php';
      break;

    case "contascategoria":
      include 'contascategoria_inserir.php';
      break;

    case "portador":
      include 'portador_inserir.php';
      break;

    case "contasreceber":
      include 'contasreceber_inserir.php';
      break;

    case "contasreceberpagamento":
      include 'contasreceberpagamento_inserir.php';
      break;
    
    case "transferencia":
      include 'caixaebancos_transferencia.php';
      break;

    default:
      $jsonSaida = json_decode(json_encode(
        array(
          "status" => "400",
          "retorno" => "Aplicacao " . $aplicacao . " Versao " . $versao . " Funcao " . $funcao . " Invalida" . " Metodo " . $metodo . " Invalido "
        )
      ), TRUE);
      break;
  }
}

if ($metodo == "POST") {

  switch ($funcao) {

    case "contasreceber":
      include 'contasreceber_alterar.php';
      break;

    case "contaspagar":
      include 'contaspagar_alterar.php';
      break;

    case "contaspagarpagamento":
      include 'contaspagarpagamento_alterar.php';
      break;

    case "contascategoria":
      include 'contascategoria_alterar.php';
      break;

    case "portador":
      include 'portador_alterar.php';
      break;

    case "contasreceber":
      include 'contasreceber_alterar.php';
      break;

    case "contasreceberpagamento":
      include 'contasreceberpagamento_alterar.php';
      break;

    default:
      $jsonSaida = json_decode(json_encode(
        array(
          "status" => "400",
          "retorno" => "Aplicacao " . $aplicacao . " Versao " . $versao . " Funcao " . $funcao . " Invalida" . " Metodo " . $metodo . " Invalido "
        )
      ), TRUE);
      break;
  }
}

if ($metodo == "DELETE") {
  switch ($funcao) {

    case "contaspagar":
      include 'contaspagar_excluir.php';
      break;

    case "contaspagarpagamento":
      include 'contaspagarpagamento_excluir.php';
      break;

    case "contasreceber":
      include 'contasreceber_excluir.php';
      break;

    case "contasreceberpagamento":
      include 'contasreceberpagamento_excluir.php';
      break;

    case "caixaebancos":
      include 'caixaebancos_excluir.php';
      break;
  
    default:
      $jsonSaida = json_decode(json_encode(
        array(
          "status" => "400",
          "retorno" => "Aplicacao " . $aplicacao . " Versao " . $versao . " Funcao " . $funcao . " Invalida" . " Metodo " . $metodo . " Invalido "
        )
      ), TRUE);
      break;
  }
}
