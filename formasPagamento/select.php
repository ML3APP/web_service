<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_usuario = $obj["id_usuario"];

try{

	$con->beginTransaction();

	$str = "SELECT tb_cartao.* FROM tb_cartao WHERE tb_cartao.cod_usuario = $id_usuario and excluido = 0 ORDER BY tb_cartao.id_dados_cartao DESC";

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>