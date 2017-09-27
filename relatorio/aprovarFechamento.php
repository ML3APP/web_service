<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();


$saldo_atual 				= $obj['saldo_final'];
$entradas 					= $obj['entradas'];
$saidas 					= $obj['saidas'];
$repasse_sede 				= $obj['repasse_sede'];
$repasse_pr 				= $obj['repasse_pr'];
$cod_igreja 				= $obj['ig_id_igreja'];
$ig_cod_sede 				= $obj['ig_cod_sede'];

$data = $obj['ano'] ."-".$obj['mes']."-"."01";

$where = 1;

try{

	$con->beginTransaction();

	$str = "

	INSERT INTO tb_fechamento_mes (
	data_fechado,
	data,
	saldo_atual,
	entradas,
	saidas,
	repasse_sede,
	repasse_pr,
	cod_igreja
	) 
	
	VALUES

	(
	DATE(NOW()),
	'$data',
	'$saldo_atual',
	'$entradas',
	'$saidas',
	'$repasse_sede',
	'$repasse_pr',
	$cod_igreja
	)

	";

	// echo $str;

	$sql = $con->exec($str);

	if($sql){
		echo "deu_bom";
	}else{
		echo "deu_ruim";
	}
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>