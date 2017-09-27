<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$categoria = json_decode($obj['categoria'], true);

$desc_categoria = $categoria['desc_categoria'];
$excluido = $categoria['excluido'];
$fixo = $categoria['fixo'];
$id_categoria = $categoria['id_categoria'];

try{

	$con->beginTransaction();

	$str = "UPDATE tb_categoria SET desc_categoria = '$desc_categoria', excluido = $excluido, fixo = $fixo  WHERE id_categoria = $id_categoria";

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