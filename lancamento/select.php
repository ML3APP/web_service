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


$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();


$id_igreja = $obj["id_igreja"];
$data = $obj["data"];
$selecionar_tambem_excluidos = $obj["selecionar_tambem_excluidos"];

$fixo = $obj["fixo"];

$sou_membro = $obj["sou_membro"];
$id_usuario = $obj["id_usuario"];

$where = "";

$andExcluido = " ";

if($fixo){

	$where .= " and tb_lancamento.repetir_tipo = 'fixa'";

}else{
	if(!$selecionar_tambem_excluidos){
		$andExcluido .= " and tb_parcela.excluido = 0 ";
	}

	if(!empty($data)){
		$where .= " and MONTH(tb_parcela.dt_parcela) = MONTH('$data') and YEAR(tb_parcela.dt_parcela) = YEAR('$data') ";
	}

}

if($sou_membro){
	$where .= " and tb_lancamento.cod_quem_pagou = $id_usuario";
}

try{

	$con->beginTransaction();

	$str = "SELECT quem_pagou.id_usuario as qp_id_usuario, quem_pagou.avatar as qp_avatar, quem_pagou.nome as qp_nome, tb_categoria.* ,tb_lancamento.* , tb_parcela.* 

	FROM tb_lancamento 

	LEFT JOIN tb_categoria ON(tb_categoria.id_categoria = tb_lancamento.cod_categoria) 
	LEFT JOIN tb_parcela ON(tb_lancamento.id_lancamento = tb_parcela.cod_lancamento $andExcluido) 

	LEFT JOIN tb_usuario as quem_pagou ON(tb_lancamento.cod_quem_pagou = quem_pagou.id_usuario) 

	WHERE tb_lancamento.cod_igreja = $id_igreja $where";

	// echo $str;

	$sql = $con->query($str);

	$result = $sql->fetchAll();



	if($fixo){

		$data_escolhida = date('Y-m',strtotime($data));

		$newResult = array();

		// print_r($result);

		for ($i=0; $i < COUNT($result); $i++) { 

			$dt_lancamento = $result[$i]['dt_lancamento'];
			$repetir_periodo = $result[$i]['repetir_periodo'];

			$data_conta = date('Y-m',strtotime($dt_lancamento));

			// echo $data_conta ;
			// echo $data_escolhida;

			while($data_conta <= $data_escolhida){
				// echo "while \n";

				if($data_conta == $data_escolhida){
						// echo "if \n";

					$result[$i]['id_parcela'] = "";
					$result[$i]['excluido'] = 0;
					$result[$i]['foi_pago'] = 0;
					$result[$i]['dt_parcela'] = $dt_lancamento;
					$result[$i]['valor_parcela'] = $result[$i]['valor'];
					array_push($newResult, $result[$i]);
				}else{
					// echo "else \n";
				}

				$dt_lancamento = getDateParcela($dt_lancamento, 1, $repetir_periodo);
				$data_conta = date('Y-m',strtotime($dt_lancamento));
			}

		}

		$result = $newResult;

	}

	// print_r($newResult);



	echo json_encode($result);

	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>