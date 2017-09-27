<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_grupo = $obj["id_grupo"];
$id_usuario = $obj["id_usuario"];

try{

	$con->beginTransaction();

	$str = "UPDATE tb_grupo_participante SET excluido = 1 WHERE tb_grupo_participante.cod_grupo = $id_grupo and cod_participante = $id_usuario";

	$sql = $con->exec($str);

	// echo $str;

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