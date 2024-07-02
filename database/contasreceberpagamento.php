<?php
// Lucas 22052024 

include_once __DIR__ . "/../conexao.php";


if (isset($_GET['operacao'])) {

	$operacao = $_GET['operacao'];

	if ($operacao == "inserir") {

		// tratamento de valores para banco progress
		$valorPago_formatado = $_POST['valorPago'];
		$valorPago_formatado = str_replace('.', '', $valorPago_formatado); // remove o ponto
		$valorPago_formatado = str_replace(',', '.', $valorPago_formatado); // troca a vírgula por ponto

		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'idCR' => $_POST['idCR'],
			'dtPagamento' => $_POST['dtPagamento'],
			'historico' => $_POST['historico'],
			'valorPago' => $valorPago_formatado,
			'acrescimos' => $_POST['acrescimos'],
			'descontos' => $_POST['descontos'],
			'idCategoria' => $_POST['idCategoria'],
			'idPortador' => $_POST['idPortador']
		);
		
		$contas = chamaAPI(null, '/financeiro/contasreceberpagamento', json_encode($apiEntrada), 'PUT');

	}

	if ($operacao == "excluir") {
		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'idCR' => $_POST['idCR'],
			'idCrPgto' => $_POST['idCrPgto']
		);
		$contas = chamaAPI(null, '/financeiro/contasreceberpagamento', json_encode($apiEntrada), 'DELETE');
	}


	if ($operacao == "filtrar") {
		$idCR = isset($_POST["idCR"]) && $_POST["idCR"] !== ""  ? $_POST["idCR"]  : null;
		$buscar = isset($_POST["buscar"]) && $_POST["buscar"] !== ""  ? $_POST["buscar"]  : null;
		$buscaPortador = isset($_POST["buscaPortador"]) && $_POST["buscaPortador"] !== ""  ? $_POST["buscaPortador"]  : null;
		$buscaCategoria = isset($_POST["buscaCategoria"]) && $_POST["buscaCategoria"] !== ""  ? $_POST["buscaCategoria"]  : null;
		$PeriodoInicio = isset($_POST["PeriodoInicio"]) && $_POST["PeriodoInicio"] !== ""  ? $_POST["PeriodoInicio"]  : null;
		$PeriodoFim = isset($_POST["PeriodoFim"]) && $_POST["PeriodoFim"] !== ""  ? $_POST["PeriodoFim"]  : null;

		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'idCrPgto' => null,
			'idCR' => $idCR,
			'buscar' => $buscar,
			'buscaPortador' => $buscaPortador,
			'buscaCategoria' => $buscaCategoria,
			'PeriodoInicio' => $PeriodoInicio,
          	'PeriodoFim' => $PeriodoFim
		);
		$contas = chamaAPI(null, '/financeiro/contasreceberpagamento', json_encode($apiEntrada), 'GET');

		echo json_encode($contas);
		return $contas;
	}

	if ($operacao == "buscarContasReceber") {
		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'idCrPgto' => null,
			'idCR' => $_POST['idCR']
		);
		$contas = chamaAPI(null, '/financeiro/contasreceberpagamento', json_encode($apiEntrada), 'GET');

		echo json_encode($contas);
		return $contas;
	}

}