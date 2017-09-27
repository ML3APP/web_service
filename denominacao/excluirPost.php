<?php  

$obj = json_decode(file_get_contents('php://input'), true);

include("header.php"); include("db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_post = $obj['id_post'];


try{

	$con->beginTransaction();

	$sql = $con->exec("DELETE FROM tb_post WHERE id_post = $id_post");

	echo $sql;

	$con->commit();

}catch(Exception $e){
	$con->rollback();
}


?>