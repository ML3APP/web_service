<?php 


function getDateParcela($date, $index, $periodo){

	$date =  date('Y-m-d', strtotime($date));

	switch ($periodo) {
		case "Anual":
		$date = date('Y-m-d', strtotime("+$index years", strtotime($date)));
		break;	
		case "Semestral":

		$aux = 6*$index;
		$date = date('Y-m-d', strtotime("+$aux months", strtotime($date)));
		break;
		case "Trimestral":
		$aux = 3*$index;
		$date = date('Y-m-d', strtotime("+$aux months", strtotime($date)));
		break;
		case "Bimestral":
		$aux = 2*$index;
		$date = date('Y-m-d', strtotime("+$aux months", strtotime($date)));
		break;
		case "Mensal":
		$date = date('Y-m-d', strtotime("+$index months", strtotime($date)));
		break;
		case "Quinzenal":
		$aux = 15*$index;
		$date = date('Y-m-d', strtotime("+$aux days", strtotime($date)));
		break;
		case "Semanal":
		$aux = 7*$index;
		$date = date('Y-m-d', strtotime("+$aux days", strtotime($date)));
		break;
		case "Diária":
		$date = date('Y-m-d', strtotime("+$index days", strtotime($date)));
		break;

		default:

		break;
	}

	return $date;

}

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$obj = json_decode(file_get_contents('php://input'), true);

$parcela = json_decode($obj['parcela'], true);

$id_parcela = $parcela['id_parcela'];
$repetir = $parcela['repetir'];
$tipo_excluir = $obj['tipo_excluir'];
$dt_lancamento = $parcela['dt_lancamento'];
$repetir_periodo = $parcela['repetir_periodo'];

if(empty($excluido)){
	$excluido = 0;
}

$id_lancamento = $parcela['id_lancamento'];
$valor = $parcela['valor'];
$dt_parcela = $parcela['dt_parcela'];

$where = "1";



try{

	echo $tipo_excluir;
	$con->beginTransaction();

	if($tipo_excluir == 'unica'){


		if(empty($id_parcela)){

			$str_parcela = "INSERT INTO tb_parcela (

			cod_lancamento,
			foi_pago,
			num_parcela,
			valor_parcela,
			dt_parcela,
			dt_pagamento,
			excluido

			) 

			VALUES (

			$id_lancamento, 
			0, 
			0, 
			$valor,	
			'$dt_parcela',
			'',
			1

			)";

			$sql_parcela = $con->exec($str_parcela);


		}else{
			$str_parcela = "UPDATE tb_parcela SET excluido = 1 WHERE id_parcela = $id_parcela";
			echo $str_parcela ;
			$sql_parcela = $con->exec($str_parcela);
		}


		if($repetir == 0){
			$str_lancamento = "UPDATE tb_lancamento SET excluido = 1 WHERE tb_lancamento.id_lancamento = $id_lancamento";
			$sql_lancamento = $con->exec($str);			
		}


	}else if($tipo_excluir == 'proximas'){

		$data_conta = date('Y-m-d',strtotime($dt_lancamento));
		$data_escolhida = date('Y-m-d',strtotime($dt_parcela));

		while($data_conta < $data_escolhida){
			echo "<br>". $data_conta ." - ". $data_escolhida ."<br>";

			$sql_verifica_ja_existe = $con->query("SELECT tb_parcela.id_parcela FROM tb_parcela WHERE tb_parcela.dt_parcela = '$dt_lancamento' and tb_parcela.cod_lancamento = $id_lancamento");

			$result_verifica_ja_existe = $sql_verifica_ja_existe->fetchAll();

			if(COUNT($result_verifica_ja_existe) == 0){

				$str_insert = "INSERT INTO tb_parcela (

				cod_lancamento,
				foi_pago,
				num_parcela,
				valor_parcela,
				dt_parcela,
				dt_pagamento,
				excluido

				) 

				VALUES (

				$id_lancamento, 
				0, 
				0, 
				$valor,	
				'$dt_lancamento',
				'',
				0

				)";

				$sql_insert = $con->exec($str_insert);	

			}else{
				echo "else \n";
			}

			$dt_lancamento = getDateParcela($dt_lancamento, 1, $repetir_periodo);
			$data_conta = date('Y-m-d',strtotime($dt_lancamento));
		}

		$str_parcela = "UPDATE tb_parcela SET excluido = 1 WHERE DATE(tb_parcela.dt_parcela) >= DATE('$data_escolhida')";
		$sql_parcela= $con->exec($str_parcela);

		$str_lancamento = "UPDATE tb_lancamento SET repetir_tipo = 'fixa_cancelada' WHERE tb_lancamento.id_lancamento = $id_lancamento";
		$sql_lancamento = $con->exec($str_lancamento);	

	}else if($tipo_excluir == 'todas'){

		$str_parcela = "UPDATE tb_parcela SET excluido = 1 WHERE tb_parcela.cod_lancamento = $id_lancamento";
		$sql_parcela= $con->exec($str_parcela);

		$str_lancamento = "UPDATE tb_lancamento SET excluido = 1 WHERE tb_lancamento.id_lancamento = $id_lancamento";
		$sql_lancamento = $con->exec($str_lancamento);	

	}


	if($sql_parcela){

		echo "deu_bom";

		// SendEmail::sendEmailDefault($nome,'Bem Vindo', $email, "Olá, ".$nome.". <br><br>Login: ". $email."<br>"."senha:". $senha);
		// SendNotificacao::sendNotificacaoNovoPai($id_usuario, $id_filho);
	}else{
		echo "deu_ruim";
	}


	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>