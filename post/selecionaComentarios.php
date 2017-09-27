<?php  

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_post = $obj['id_post'];
$id_usuario = $obj['id_usuario'];

try{

	$con->beginTransaction();

	$sql = $con->query("SELECT *
		FROM tb_comentario 
		INNER JOIN tb_usuario ON(tb_comentario.tb_usuario_id_usuario = tb_usuario.id_usuario) 
		Where tb_comentario.tb_post_id_post = $id_post GROUP BY tb_comentario.id_comentario ORDER BY tb_comentario.id_comentario ASC");

	$result= $sql->fetchAll();

	echo json_encode($result);

	$con->commit();

}catch(Exception $e){
	$con->rollback();
}


?>