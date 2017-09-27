<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$cargo = $obj['cargo'];
$cod_igreja = $obj['cod_igreja'];

try{

	$con->beginTransaction();

	$str = "INSERT INTO tb_cargo (desc_cargo, excluido, fixo, cod_igreja) VALUE ('$cargo', 0, 0 , $cod_igreja)";

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