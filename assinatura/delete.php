<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_plano = $obj['id_plano'];


try{

	$con->beginTransaction();

	$sql = $con->exec("UPDATE tb_plano SET excluido = 1 WHERE tb_plano.id_plano = $id_plano");

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