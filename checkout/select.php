<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_igreja = $obj["id_igreja"];

try{

	$con->beginTransaction();

	$str = "SELECT tb_cargo.* FROM tb_cargo 

	INNER JOIN tb_igreja ON(tb_igreja.id_igreja = tb_cargo.cod_igreja)
	LEFT JOIN tb_igreja as tb_sede ON(tb_sede.id_igreja = tb_igreja.cod_sede)

	WHERE (tb_cargo.cod_igreja = $id_igreja or (tb_sede.id_igreja = tb_cargo.cod_igreja and tb_cargo.fixo = 1)) and tb_cargo.excluido = 0 ";

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>