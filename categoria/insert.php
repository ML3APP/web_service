<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$categoria = $obj['categoria'];
$tipo = $obj['tipo'];
$cod_igreja = $obj['cod_igreja'];

try{

	$con->beginTransaction();

	$str = "INSERT INTO tb_categoria (desc_categoria, excluido, fixo,tipo, cod_igreja) VALUE ('$categoria', 0, 0 , '$tipo', $cod_igreja)";

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