<?php
// helio 31012023 - incluido POST para inserir clientes
// helio 26012023 18:10

// NOVA VERSAO 
include  __DIR__ . "/../conexao.php";
//include "app/conexao.php";

if ($versao==""){$versao="1";}

if ($metodo=="GET"||$metodo=="PUT"||$metodo=="POST"||$metodo=="DELETE") {

      switch ($versao) {
         case "1":
               include '1/controle.php';
               break;
         default:
          $jsonSaida = json_decode(json_encode(
             array("erro" => "400",
                 "retorno" => "Aplicacao " . $aplicacao . " Versao ".$versao." Invalida")
               ), TRUE);
            break;
          }

} else {

      $jsonSaida = json_decode(json_encode(
        array("erro" => "400",
            "retorno" => "Aplicacao " . $aplicacao . " Metodo ".$metodo." Invalido")
          ), TRUE);

    }

  
