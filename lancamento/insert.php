<?php 


include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();


$lancamento = json_decode($_POST['lancamento'], true);

$anexo 					= $lancamento['anexo'];
$cod_categoria 			= $lancamento['cod_categoria'];
$cod_igreja 			= $lancamento['cod_igreja'];
$cod_usuario 			= $lancamento['cod_usuario'];
$descricao 				= $lancamento['descricao'];
$foi_pago 				= $lancamento['foi_pago'];
$repetir 				= $lancamento['repetir'];
$repetir_periodo 		= $lancamento['repetir_periodo'];
$repetir_tipo 			= $lancamento['repetir_tipo'];
$repetir_vezes 			= $lancamento['repetir_vezes'];
$tipo 					= $lancamento['tipo'];
$valor 					= $lancamento['valor'];
$dt_lancamento 			= $lancamento['dt_lancamento'];
$entrada_saida 			= $lancamento['entrada_saida'];

$cod_igreja_repasse 	= $lancamento['cod_igreja_repasse'];
$cod_quem_pagou 	= $lancamento['cod_quem_pagou'];

$dt_pagamento 			= "";

if(empty($cod_categoria)){
	$cod_categoria = 0;
}

if(empty($cod_igreja_repasse)){
	$cod_igreja_repasse = 0;
}

if(empty($repetir)){
	$repetir = 0;
}

if(empty($repetir_vezes)){
	$repetir_vezes = 0;
}

if(empty($foi_pago)){
	$foi_pago = 0;
}

if($foi_pago){
	$foi_pago = 1;
	$dt_pagamento = date("Y-m-d");   
}

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

	ignore_user_abort(true);
	set_time_limit(0);
	ob_start();

	$con->beginTransaction();

	$str = "INSERT INTO tb_lancamento (

	anexo, 
	cod_categoria, 
	cod_igreja, 
	cod_usuario, 
	descricao, 
	repetir, 
	repetir_periodo, 
	repetir_tipo, 
	repetir_vezes, 
	tipo, 
	entrada_saida, 
	dt_lancamento, 
	cod_igreja_repasse, 
	cod_quem_pagou, 
	valor

	) 

	VALUES (

	'$anexo', 
	$cod_categoria, 
	$cod_igreja, 
	$cod_usuario, 
	'$descricao', 
	$repetir, 
	'$repetir_periodo', 
	'$repetir_tipo', 
	$repetir_vezes, 
	'$tipo', 
	'$entrada_saida', 
	'$dt_lancamento', 
	$cod_igreja_repasse, 
	$cod_quem_pagou, 
	$valor

	)";

	$sql_lancamento = $con->exec($str);		

	$lastId = $con->lastInsertId();

	if(!$repetir){
		$str_parcela = "INSERT INTO tb_parcela (

		cod_lancamento, 
		foi_pago, 
		num_parcela, 
		valor_parcela,
		dt_pagamento,
		dt_parcela

		) 

		VALUES (

		$lastId, 
		$foi_pago, 
		0, 
		$valor,	
		'$dt_pagamento',	
		'$dt_lancamento'

		)";

		$sql_parcela = $con->exec($str_parcela);		
	}

	if($repetir && $repetir_tipo == 'parcelada'){


		$valor_parcela = $valor / $repetir_vezes;

		for ($i=0; $i < $repetir_vezes; $i++) { 

			$dt_parcela = getDateParcela($dt_lancamento, $i, $repetir_periodo);

			$num_parcela = $i +1;

			$str_parcela = "INSERT INTO tb_parcela (

			cod_lancamento,
			foi_pago,
			num_parcela,
			valor_parcela,
			dt_parcela

			) 

			VALUES (

			$lastId, 
			$foi_pago, 
			$num_parcela, 
			$valor_parcela,	
			'$dt_parcela'

			)";

			$sql_parcela = $con->exec($str_parcela);

		}
	}

	// echo $str;

	if($sql_lancamento){		
		echo "deu_bom";	
	}else{
		echo "deu_ruim";
	}

	$con->commit();

	header('Connection: close');
	header('Content-Length: '.ob_get_length());
	ob_end_flush();
	ob_flush();
	flush();

	if($sql_lancamento && !empty($anexo)){
		if(move_uploaded_file($_FILES["file"]["tmp_name"], "../../upload/lancamento/".$anexo)){

		}else{
			//echo "deu ruim";
		}
	}	

	

	 // $response = file_get_contents('http://localhost/ml3/www/web_service/parcela/crontabContaFixaMes.php');
	$response = file_get_contents('http://liraedu.com/ml3/web_service/parcela/crontabContaFixaMes.php');

}catch(Exception $e){
	$con->rollback();
}

?>