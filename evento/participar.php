<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_evento = $obj['id_evento'];
$id_usuario = $obj['id_usuario'];

try{

	$con->beginTransaction();

	$str = "INSERT INTO tb_evento_participante (cod_evento, cod_usuario) VALUE ($id_evento, $id_usuario)";

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