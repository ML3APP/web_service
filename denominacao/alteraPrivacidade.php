<?php  

$obj = json_decode(file_get_contents('php://input'), true);

include("header.php"); include("db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_usuario = $obj['id_usuario'];
$status = $obj['status'];


try{

	$con->beginTransaction();

	$sql_update = $con->exec("UPDATE tb_usuario SET privado = $status WHERE id_usuario = $id_usuario");

	echo $sql_update;

	$con->commit();

}catch(Exception $e){
	$con->rollback();
}


?>