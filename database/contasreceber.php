<?php
// lucas 20052024 - id747 Contas a Pagar e Receber - Progress e Movimentacoes
// Gabriel 22092023 

include_once __DIR__ . "/../conexao.php";

function buscaCR($idCR = null)
{

	$contas = array();

	$idEmpresa = null;
	if (isset($_SESSION['idEmpresa'])) {
		$idEmpresa = $_SESSION['idEmpresa'];
	}

	$apiEntrada = array(
		'idEmpresa' => $idEmpresa,
		'idCR' => $idCR
	);
	$contas = chamaAPI(null, '/financeiro/contasreceber', json_encode($apiEntrada), 'GET');

	return $contas;
}


if (isset($_GET['operacao'])) {

	$operacao = $_GET['operacao'];

	if ($operacao == "inserir") {

		// tratamento de valores para banco progress
		$valorOriginal_formatado = $_POST['valorOriginal'];
		$valorOriginal_formatado = str_replace('.', '', $valorOriginal_formatado); // remove o ponto
		$valorOriginal_formatado = str_replace(',', '.', $valorOriginal_formatado); // troca a vírgula por ponto

		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'idPessoaFornecedor' => $_POST['idPessoaFornecedor'],
			'dtCompetencia' => $_POST['dtCompetencia'],
			'dtVencimento' => $_POST['dtVencimento'],
			'documento' => $_POST['documento'],
			'historico' => $_POST['historico'],
			'valorOriginal' => $valorOriginal_formatado,
			'idPortador' => $_POST['idPortador'],
			'idCategoria' => $_POST['idCategoria']
		);
		if(isset($_POST['ocorrencia']) && ($_POST['ocorrencia'] == 'recorrente')){
			$apiEntrada += array(
				'vencimento' => $_POST['vencimento'],
				'parcelas' => $_POST['parcelas']
			);	
			$contas = chamaAPI(null, '/financeiro/contasreceber_clonar', json_encode($apiEntrada), 'PUT');
		}else{
			$contas = chamaAPI(null, '/financeiro/contasreceber', json_encode($apiEntrada), 'PUT');
		}

	}

	if ($operacao == "alterar") {

		// tratamento de valores para banco progress
		$valorOriginal_formatado = $_POST['valorOriginal'];
		$valorOriginal_formatado = str_replace('.', '', $valorOriginal_formatado); // remove o ponto
		$valorOriginal_formatado = str_replace(',', '.', $valorOriginal_formatado); // troca a vírgula por ponto

		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'idCR' => $_POST['idCR'],
			'idPessoaFornecedor' => $_POST['idPessoaFornecedor'],
			'dtCompetencia' => $_POST['dtCompetencia'],
			'dtVencimento' => $_POST['dtVencimento'],
			'documento' => $_POST['documento'],
			'historico' => $_POST['historico'],
			'valorOriginal' => $valorOriginal_formatado,
			'idPortador' => $_POST['idPortador'],
			'idCategoria' => $_POST['idCategoria']
		);
		$contas = chamaAPI(null, '/financeiro/contasreceber', json_encode($apiEntrada), 'POST');
	}

	if ($operacao == "excluir") {
		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'idCR' => $_POST['idCR']
		);
		$contas = chamaAPI(null, '/financeiro/contasreceber', json_encode($apiEntrada), 'DELETE');
	}

	if ($operacao == "filtrar") {

		$buscar = isset($_POST["buscar"]) && $_POST["buscar"] !== "" ? $_POST["buscar"] : null;
		$idCR = isset($_POST["idCR"]) && $_POST["idCR"] !== "" ? $_POST["idCR"] : null;
		$buscaPortador = isset($_POST["buscaPortador"]) && $_POST["buscaPortador"] !== ""  ? $_POST["buscaPortador"]  : null;
		$buscaCategoria = isset($_POST["buscaCategoria"]) && $_POST["buscaCategoria"] !== ""  ? $_POST["buscaCategoria"]  : null;
		$filtrosituacao = isset($_POST["filtrosituacao"]) && $_POST["filtrosituacao"] !== ""  ? $_POST["filtrosituacao"]  : null;
		$PeriodoInicio = isset($_POST["PeriodoInicio"]) && $_POST["PeriodoInicio"] !== ""  ? $_POST["PeriodoInicio"]  : null;
		$PeriodoFim = isset($_POST["PeriodoFim"]) && $_POST["PeriodoFim"] !== ""  ? $_POST["PeriodoFim"]  : null;
		
		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'idCR' => $idCR,
			'buscar' => $buscar,
			'buscaPortador' => $buscaPortador,
			'buscaCategoria' => $buscaCategoria,
			'filtrosituacao' => $filtrosituacao,
			'PeriodoInicio' => $PeriodoInicio,
			'PeriodoFim' => $PeriodoFim
		);
		$contas = chamaAPI(null, '/financeiro/contasreceber', json_encode($apiEntrada), 'GET');

		echo json_encode($contas);
		return $contas;
	}

	if ($operacao == "buscar") {
		$apiEntrada = array(
			'idEmpresa' => $_SESSION['idEmpresa'],
			'idCR' => $_POST['idCR']
		);
		$contas = chamaAPI(null, '/financeiro/contasreceber', json_encode($apiEntrada), 'GET');

		echo json_encode($contas);
		return $contas;
	}

}