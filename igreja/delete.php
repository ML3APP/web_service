<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_igreja = $obj['id_igreja'];


try{

	$con->beginTransaction();

	$sql = $con->exec("UPDATE tb_igreja SET excluido = 1 WHERE tb_igreja.id_igreja = $id_igreja");

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