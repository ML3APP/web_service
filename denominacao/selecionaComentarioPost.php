<?php  

$obj = json_decode(file_get_contents('php://input'), true);

include("header.php"); include("db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_post = $obj['id_post'];
$id_usuario = $obj['id_usuario'];

try{

	$con->beginTransaction();

	$sql_usuario = $con->query("SELECT 	

		tb_usuario.avatar, tb_comentario.id_comentario, tb_usuario.id_usuario, tb_usuario.nome, tb_comentario.comentario, tb_comentario.data_comentario, COUNT(tb_curtida_comentario.id_curtida_comentario) as qtd_curtidas ,

(SELECT COUNT(id_curtida_comentario) FROM tb_curtida_comentario WHERE tb_curtida_comentario.cod_usuario = $id_usuario and tb_curtida_comentario.cod_comentario = tb_comentario.id_comentario) as ja_curtiu

		FROM tb_comentario 
		LEFT JOIN tb_curtida_comentario ON(tb_curtida_comentario.cod_comentario = tb_comentario.id_comentario) 

		INNER JOIN tb_usuario ON(tb_comentario.tb_usuario_id_usuario = tb_usuario.id_usuario) 
		Where tb_comentario.tb_post_id_post = $id_post GROUP BY tb_comentario.id_comentario ORDER BY tb_comentario.id_comentario DESC");
	$result_usuario = $sql_usuario->fetchAll();

	if(COUNT($result_usuario) > 0){
		echo json_encode($result_usuario);
	}	else{
		echo "nenhum_comentario_encontrado";
	}

	$con->commit();

}catch(Exception $e){
	$con->rollback();
}


?>