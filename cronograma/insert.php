<?php 


include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$obj = json_decode(file_get_contents('php://input'), true);


$cronograma = json_decode($obj['cronograma'], true);


$cod_igreja			= $cronograma['cod_igreja'];
$dia_semana			= $cronograma['dia_semana'];
$titulo_cronograma	= $cronograma['titulo_cronograma'];
$desc_cronograma	= $cronograma['desc_cronograma'];
$hr_inicio			= $cronograma['hr_inicio'];
$hr_termino			= $cronograma['hr_termino'];


if(empty($cod_denominacao)){
	$cod_denominacao = 0;
}

if(empty($cod_sede)){
	$cod_sede = 0;
}

try{

	$con->beginTransaction();

	$str = "INSERT INTO tb_cronograma (

	titulo_cronograma, 
	desc_cronograma, 
	dia_semana, 
	hr_inicio, 
	hr_termino,
	cod_igreja

	) 

	VALUE (

	'$titulo_cronograma', 
	'$desc_cronograma', 
	$dia_semana, 
	'$hr_inicio', 
	'$hr_termino',
	$cod_igreja

	)";

	echo $str;

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