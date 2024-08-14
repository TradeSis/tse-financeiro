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
		$arquivo = fopen("C:TRADESIS/tmp/LOG.txt", "a");
		fwrite($arquivo, json_encode($_POST) . "\n");
		fclose($arquivo);
		//return;

		// tratamento de valores para banco progress
		$valor_formatado = $_POST['valor'];
		$valor_formatado = str_replace('.', '', $valor_formatado); // remove o ponto
		$valor_formatado = str_replace(',', '.', $valor_formatado); // troca a v�rgula por ponto
		
		$data = isset($_POST["data"]) && $_POST["data"] !== "null"  ? $_POST["data"]  : null;
		$idPortadorOrigem = isset($_POST["idPortadorOrigem"]) && $_POST["idPortadorOrigem"] !== "null"  ? $_POST["idPortadorOrigem"]  : null;
		$idPortadorDestino = isset($_POST["idPortadorDestino"]) && $_POST["idPortadorDestino"] !== "null"  ? $_POST["idPortadorDestino"]  : null;
		
		
		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'data' => $data,
			'idPortadorOrigem' => $idPortadorOrigem,
			'idPortadorDestino' => $idPortadorDestino,
			'valor' => $valor_formatado
		);
		
		$caixaebancos = chamaAPI(null, '/financeiro/transferencia', json_encode($apiEntrada), 'PUT');

		echo json_encode($caixaebancos);
		return $caixaebancos;
	}


}