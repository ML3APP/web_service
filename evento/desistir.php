<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_evento = $obj['id_evento'];
$id_usuario = $obj['id_usuario'];

try{

	$con->beginTransaction();

	$str = "UPDATE tb_evento_participante SET desistiu = 1  WHERE cod_evento = $id_evento and cod_usuario = $id_usuario";

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