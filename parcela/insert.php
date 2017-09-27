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


if(empty($repetir)){
	$repetir = 0;
}

if(empty($repetir_vezes)){
	$repetir_vezes = 0;
}

if(empty($foi_pago)){
	$foi_pago = 0;
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
	dt_lancamento, 
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
	'$dt_lancamento', 
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
		dt_parcela

		) 

		VALUES (

		$lastId, 
		$foi_pago, 
		0, 
		$valor,	
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

		if(!empty($anexo)){
			if(move_uploaded_file($_FILES["file"]["tmp_name"], "../../upload/img/igreja/".$anexo)){
			}else{
			//echo "deu ruim";
			}
		}	

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