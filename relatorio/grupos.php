<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_igreja 				= $obj['id_igreja'];

try{

	$con->beginTransaction();

	$str = "SELECT

	tb_grupo.*, 
	COUNT(tb_grupo_participante.id_grupo_participante) as qtd_participantes,

	
	tb_usuario.nome as lider_nome,
	tb_usuario.avatar as lider_avatar,
	tb_usuario.email as lider_email,
	tb_usuario.telefone as lider_telefone


	FROM tb_grupo 

	INNER JOIN tb_usuario ON(tb_grupo.cod_lider = tb_usuario.id_usuario) 

	LEFT JOIN tb_grupo_participante ON(tb_grupo_participante.cod_grupo = tb_grupo.id_grupo) 
	

	WHERE 

	tb_grupo.cod_igreja = $id_igreja  and 
	tb_grupo.excluido = 0

	GROUP BY tb_grupo.id_grupo ";

// echo $str;

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>