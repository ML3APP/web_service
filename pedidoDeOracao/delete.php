<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_cronograma = $obj['id_cronograma'];


try{

	$con->beginTransaction();

	$sql = $con->exec("UPDATE tb_cronograma SET tb_cronograma.excluido = 1 WHERE tb_cronograma.id_cronograma = $id_cronograma");

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