<?php  
$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");

$connect = new Con();
$con = $connect->getCon();

$cronograma = json_decode($obj['cronograma'], true);

$id_cronograma		= $cronograma['id_cronograma'];
$cod_igreja			= $cronograma['cod_igreja'];
$dia_semana			= $cronograma['dia_semana'];
$titulo_cronograma	= $cronograma['titulo_cronograma'];
$desc_cronograma	= $cronograma['desc_cronograma'];
$hr_inicio			= $cronograma['hr_inicio'];
$hr_termino			= $cronograma['hr_termino'];

try{

	$con->beginTransaction();

	$str = "UPDATE tb_cronograma SET

	dia_semana = $dia_semana,  
	titulo_cronograma = '$titulo_cronograma',  
	desc_cronograma = '$desc_cronograma',  
	hr_inicio = '$hr_inicio',  
	hr_termino = '$hr_termino'
	
	WHERE id_cronograma = $id_cronograma";

	echo $str;

	$sql = $con->exec($str);

	$con->commit();

	if($sql){			
		echo "deu_bom";				
	}else{
		echo "deu_ruim";		
	}
	

}catch(Exception $e){
	$con->rollback();
}


?>