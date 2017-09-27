<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$cargo = json_decode($obj['cargo'], true);

$desc_cargo = $cargo['desc_cargo'];
$excluido = $cargo['excluido'];
$fixo = $cargo['fixo'];
$id_cargo = $cargo['id_cargo'];

try{

	$con->beginTransaction();

	$str = "UPDATE tb_cargo SET desc_cargo = '$desc_cargo', excluido = $excluido, fixo = $fixo  WHERE id_cargo = $id_cargo";

	echo $str;

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