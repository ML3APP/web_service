<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_igreja 				= $obj['id_igreja'];
$filtro 				= $obj['filtro'];
$mes 					= $filtro['mes'];

$where = 1;

if($mes != "todos"){
	$where .= " and MONTH(tb_usuario.dt_nascimento) = $mes ";
}


try{

	$con->beginTransaction();

	$str = "SELECT 

	tb_usuario.*

	FROM tb_usuario 

	WHERE 

	$where and
	tb_usuario.cod_igreja = $id_igreja  and 
	tb_usuario.excluido = 0
	";

	// echo $str;

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>