<?php
// Lucas 20052024 

include_once __DIR__ . "/../conexao.php";

function buscaPortador($idPortador = null)
{

	$contas = array();

	$idEmpresa = null;
	if (isset($_SESSION['idEmpresa'])) {
		$idEmpresa = $_SESSION['idEmpresa'];
	}

	$apiEntrada = array(
		'idEmpresa' => $idEmpresa,
		'idPortador' => $idPortador
	);
	$contas = chamaAPI(null, '/financeiro/portador', json_encode($apiEntrada), 'GET');

	return $contas;
}

if (isset($_GET['operacao'])) {

	$operacao = $_GET['operacao'];

	if ($operacao == "inserir") {

		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'nomePortador' => $_POST['nomePortador']
		);
		$contas = chamaAPI(null, '/financeiro/portador', json_encode($apiEntrada), 'PUT');

	}

	if ($operacao == "alterar") {
		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'idPortador' => $_POST['idPortador'],
			'nomePortador' => $_POST['nomePortador']
		);
		$contas = chamaAPI(null, '/financeiro/portador', json_encode($apiEntrada), 'POST');

	}

	if ($operacao == "filtrar") {

		$idPortador = isset($_POST["idPortador"]) && $_POST["idPortador"] !== "" ? $_POST["idPortador"] : null;
		$buscaPortador = isset($_POST["buscaPortador"]) && $_POST["buscaPortador"] !== "" ? $_POST["buscaPortador"] : null;

		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'idPortador' => $idPortador,
			'buscaPortador' => $buscaPortador
		);
		$contas = chamaAPI(null, '/financeiro/portador', json_encode($apiEntrada), 'GET');

		echo json_encode($contas);
		return $contas;
	}

	if ($operacao == "buscar") {
		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'idPortador' => $_POST['idPortador']
		);
		$contas = chamaAPI(null, '/financeiro/portador', json_encode($apiEntrada), 'GET');

		echo json_encode($contas);
		return $contas;
	}

}