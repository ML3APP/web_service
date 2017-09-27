<?php 


include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

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

try{

	$con->beginTransaction();

	$sql = $con->query("SELECT tb_lancamento.* FROM tb_lancamento WHERE tb_lancamento.excluido = 0 and tb_lancamento.repetir = 1 and tb_lancamento.repetir_tipo = 'fixa'");

	$result = $sql->fetchAll();

	for ($i=0; $i < COUNT($result) ; $i++) { 

		$dt_lancamento = $result[$i]['dt_lancamento'];
		$repetir_periodo = $result[$i]['repetir_periodo'];
		$id_lancamento = $result[$i]['id_lancamento'];
		$valor = $result[$i]['valor'];

		$data_conta = date('Y-m',strtotime($dt_lancamento));
		$data_escolhida = date('Y-m');

		while($data_conta <= $data_escolhida){
			// echo "<br>". $data_conta ." - ". $data_escolhida ."<br>";

			$sql_verifica_ja_existe = $con->query("SELECT tb_parcela.id_parcela FROM tb_parcela WHERE tb_parcela.dt_parcela = '$dt_lancamento' and tb_parcela.cod_lancamento = $id_lancamento");

			$result_verifica_ja_existe = $sql_verifica_ja_existe->fetchAll();

			if(COUNT($result_verifica_ja_existe) == 0){

				$str_insert = "INSERT INTO tb_parcela (

				cod_lancamento,
				foi_pago,
				num_parcela,
				valor_parcela,
				dt_parcela,
				dt_pagamento

				) 

				VALUES (

				$id_lancamento, 
				0, 
				0, 
				$valor,	
				'$dt_lancamento',
				''

				)";

				$sql_insert = $con->exec($str_insert);	

			}else{
				echo "else \n";
			}

			$dt_lancamento = getDateParcela($dt_lancamento, 1, $repetir_periodo);
			$data_conta = date('Y-m',strtotime($dt_lancamento));
		}

	}

	$con->commit();


}catch(Exception $e){
	$con->rollback();
}

?>