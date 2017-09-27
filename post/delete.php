<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_usuario = $obj['id_usuario'];


try{

	$con->beginTransaction();

	$sql = $con->exec("UPDATE tb_usuario SET excluido = 1 WHERE tb_usuario.id_usuario = $id_usuario");

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