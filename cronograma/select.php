<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_igreja = $obj["id_igreja"];

try{

	$con->beginTransaction();

	$sql = $con->query("SELECT tb_cronograma.* FROM tb_cronograma WHERE cod_igreja = $id_igreja and tb_cronograma.excluido = 0");
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>