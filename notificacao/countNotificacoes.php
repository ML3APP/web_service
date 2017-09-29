<?php  

$obj = json_decode(file_get_contents('php://input'), true);
 
include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_usuario = $obj['id_usuario'];

try{

	$con->beginTransaction();

	$sql_usuario = $con->query("SELECT COUNT(id_notificacao) as num FROM tb_notificacao

		WHERE id_para = $id_usuario and tipo != 'nova_mensagem' and visualizado = 0 GROUP BY id_para");

	$result = $sql_usuario->fetchAll();

	echo json_encode($result);

	$con->commit();

}catch(Exception $e){
	$con->rollback();
}


?>