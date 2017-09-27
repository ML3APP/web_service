<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$cod_sede = $obj["cod_sede"];

try{

	$con->beginTransaction();

	$str = "SELECT tb_mensalidade.* 
	
	FROM tb_mensalidade 
	
	WHERE tb_mensalidade.cod_sede = $cod_sede ORDER BY id_mensalidade DESC";

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>