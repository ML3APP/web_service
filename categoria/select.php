<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_igreja = $obj["id_igreja"];
$tipo = $obj["tipo"];


$where = " 1 ";

if(!empty($tipo)){
	$where .= " and tb_categoria.tipo = '$tipo' ";
}

try{

	$con->beginTransaction();

	$str = "SELECT tb_categoria.* FROM tb_categoria 

	INNER JOIN tb_igreja ON(tb_igreja.id_igreja = tb_categoria.cod_igreja)
	LEFT JOIN tb_igreja as tb_sede ON(tb_sede.id_igreja = tb_igreja.cod_sede)

	WHERE $where and (tb_categoria.cod_igreja = $id_igreja or (tb_sede.id_igreja = tb_categoria.cod_igreja and tb_categoria.fixo = 1)) and tb_categoria.excluido = 0 ";

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>