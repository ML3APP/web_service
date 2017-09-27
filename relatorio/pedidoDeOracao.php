<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_igreja 				= $obj['id_igreja'];
$data_inicio 			= $obj['data_inicio'];
$data_termino 			= $obj['data_termino'];

try{

	$con->beginTransaction();

	$str = "SELECT

	tb_pedido_oracao.*,
	tb_usuario.id_usuario, 
	tb_usuario.avatar, 
	tb_usuario.nome

	FROM tb_pedido_oracao 
	LEFT JOIN tb_usuario ON(tb_pedido_oracao.cod_usuario = tb_usuario.id_usuario) 

	WHERE 

	tb_pedido_oracao.cod_igreja = $id_igreja  and 
	DATE(tb_pedido_oracao.dt_pedido) BETWEEN DATE('$data_inicio') AND DATE('$data_termino')   ";

// echo $str;

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>