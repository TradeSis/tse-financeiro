<?php

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

include_once __DIR__ . "/../conexao.php";


if (isset($_GET['operacao'])) {

	$operacao = $_GET['operacao'];

	if ($operacao == "filtrar") {
		$anoEntrada = isset($_POST["anoEntrada"]) && $_POST["anoEntrada"] !== "null"  ? $_POST["anoEntrada"]  : null;
		$mesEntrada = isset($_POST["mesEntrada"]) && $_POST["mesEntrada"] !== "null"  ? $_POST["mesEntrada"]  : null;
		
		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'anoEntrada' => $anoEntrada,
			'mesEntrada' => $mesEntrada,
			'portador' => $_POST["portador"]
		);
		
		$caixaebancos = chamaAPI(null, '/financeiro/caixaebancos', json_encode($apiEntrada), 'GET');

		echo json_encode($caixaebancos);
		return $caixaebancos;
	}

	if ($operacao == "transferir") {
	
		$data = isset($_POST["data"]) && $_POST["data"] !== "null"  ? $_POST["data"]  : null;
		$idPortadorOrigem = isset($_POST["idPortadorOrigem"]) && $_POST["idPortadorOrigem"] !== "null"  ? $_POST["idPortadorOrigem"]  : null;
		$idPortadorDestino = isset($_POST["idPortadorDestino"]) && $_POST["idPortadorDestino"] !== "null"  ? $_POST["idPortadorDestino"]  : null;
		$valor = isset($_POST["valor"]) && $_POST["valor"] !== "null"  ? $_POST["valor"]  : null;
		
		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'data' => $data,
			'idPortadorOrigem' => $idPortadorOrigem,
			'idPortadorDestino' => $idPortadorDestino,
			'valor' => $valor
		);
		
		$caixaebancos = chamaAPI(null, '/financeiro/transferencia', json_encode($apiEntrada), 'PUT');

		echo json_encode($caixaebancos);
		return $caixaebancos;
	}


}