<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_grupo = $obj['id_grupo'];


try{

	$con->beginTransaction();

	echo "UPDATE tb_grupo SET excluido = 1 WHERE tb_grupo.id_grupo = $id_grupo";

	$sql = $con->exec("UPDATE tb_grupo SET excluido = 1 WHERE tb_grupo.id_grupo = $id_grupo");

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