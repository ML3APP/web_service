<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_evento = $obj['id_evento'];


try{

	$con->beginTransaction();

	$sql = $con->exec("UPDATE tb_evento SET excluido = 1 WHERE tb_evento.id_evento = $id_evento");

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