<?php  

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_notificacao = $obj['id_notificacao'];

try{

	$con->beginTransaction();

	$visualiza = $con->exec("UPDATE tb_notificacao SET visualizado = 1 WHERE id_notificacao = $id_notificacao ");

	echo $visualiza;

	$con->commit();

}catch(Exception $e){
	$con->rollback();
}


?>