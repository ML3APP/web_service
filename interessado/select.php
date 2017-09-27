<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_usuario = $obj["id_usuario"];

$id_grupo = $obj["id_grupo"];
$id_igreja = $obj["id_igreja"];
$limit = $obj["limit"];
$offset = $obj["offset"];

$where = "";

if(!empty($id_grupo) && $id_grupo > 0){
	$where .= " and tb_post.cod_grupo = $id_grupo ";
}else if($id_grupo == 0 && $id_igreja > 0){
	$where .= " and tb_post.cod_grupo = 0 and tb_post.cod_igreja = $id_igreja ";
}

try{

	$con->beginTransaction();

	$str = "

	SELECT  tb_usuario.*, tb_post.*,

	(SELECT COUNT(id_curtida) FROM tb_curtida WHERE tb_curtida.tb_post_id_post = tb_post.id_post and tb_curtida.tb_usuario_id_usuario = $id_usuario) as 'ja_curtiu', 

	(SELECT COUNT(id_curtida) FROM tb_curtida WHERE tb_curtida.tb_post_id_post = tb_post.id_post) as quant_curtidas,

	(SELECT COUNT(id_comentario) FROM tb_comentario WHERE tb_comentario.tb_post_id_post = tb_post.id_post) as quant_comentarios

	FROM tb_post 

	INNER JOIN tb_usuario ON(tb_post.cod_usuario = tb_usuario.id_usuario) 

	WHERE tb_post.excluido = 0 

	$where  ORDER BY tb_post.id_post DESC LIMIT $limit OFFSET $offset";

	// echo $str;

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>