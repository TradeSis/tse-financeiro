<?php
// Lucas 20052024 

include_once __DIR__ . "/../conexao.php";

function buscaContaCategoria($idCategoria = null, $tipo = null)
{

	$contas = array();

	$idEmpresa = null;
	if (isset($_SESSION['idEmpresa'])) {
		$idEmpresa = $_SESSION['idEmpresa'];
	}

	$apiEntrada = array(
		'idEmpresa' => $idEmpresa,
		'buscaContaCategoria' => null,
		'idCategoria' => $idCategoria,
		'tipo' => $tipo
	);
	$contas = chamaAPI(null, '/financeiro/contascategoria', json_encode($apiEntrada), 'GET');

	return $contas;
}


if (isset($_GET['operacao'])) {

	$operacao = $_GET['operacao'];

	if ($operacao == "inserir") {

		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'nomeCategoria' => $_POST['nomeCategoria'],
			'tipo' => $_POST['tipo']
		);
		$contas = chamaAPI(null, '/financeiro/contascategoria', json_encode($apiEntrada), 'PUT');

	}

	if ($operacao == "alterar") {
		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'idCategoria' => $_POST['idCategoria'],
			'nomeCategoria' => $_POST['nomeCategoria'],
			'tipo' => $_POST['tipo']
		);
		$contas = chamaAPI(null, '/financeiro/contascategoria', json_encode($apiEntrada), 'POST');

	}

	if ($operacao == "filtrar") {

		$idCategoria = isset($_POST["idCategoria"]) && $_POST["idCategoria"] !== "" ? $_POST["idCategoria"] : null;
		$buscaContaCategoria = isset($_POST["buscaContaCategoria"]) && $_POST["buscaContaCategoria"] !== "" ? $_POST["buscaContaCategoria"] : null;
		$tipo = isset($_POST["tipo"]) && $_POST["tipo"] !== "" ? $_POST["tipo"] : null;

		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'idCategoria' => $idCategoria,
			'buscaContaCategoria' => $buscaContaCategoria,
			'tipo' => $tipo
		);
		$contas = chamaAPI(null, '/financeiro/contascategoria', json_encode($apiEntrada), 'GET');

		echo json_encode($contas);
		return $contas;
	}

	if ($operacao == "buscar") {
		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'idCategoria' => $_POST['idCategoria']
		);
		$contas = chamaAPI(null, '/financeiro/contascategoria', json_encode($apiEntrada), 'GET');

		echo json_encode($contas);
		return $contas;
	}

}