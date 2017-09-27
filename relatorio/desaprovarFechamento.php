<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();


$id_fechamento_mes 				= $obj['id_fechamento_mes'];

$where = 1;

try{

	$con->beginTransaction();

	$str = "DELETE FROM tb_fechamento_mes WHERE id_fechamento_mes = $id_fechamento_mes";

	// echo $str;

	$sql = $con->exec($str);

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