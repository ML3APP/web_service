<?php  

$obj = json_decode(file_get_contents('php://input'), true);

 include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id = $obj['id'];
$id_usuario = $obj['id_usuario'];

try{

	$con->beginTransaction();

	$sql = $con->query("SELECT * FROM tb_reg_id Where reg_id = '$id' and id_usuario = $id_usuario");

	$result = $sql->fetchAll();

	if(COUNT($result) == 0){

		// $insert = $con->exec("DELETE FROM tb_reg_id WHERE id_usuario = '$id_usuario' or reg_id = '$id'");
		$insert = $con->exec("INSERT INTO tb_reg_id (reg_id, id_usuario) VALUES ('$id', $id_usuario)");

		echo $insert;
	}else{ 
		echo 0;
	}
	

	$con->commit();

}catch(Exception $e){
	$con->rollback();
}


?>