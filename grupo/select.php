<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_grupo = $obj["id_grupo"];
$id_igreja = $obj["id_igreja"];

$id_usuario = $obj["id_usuario"];
$gerenciar = $obj["gerenciar"];

$where = "";

if(!empty($id_grupo)){
	$where .= " and tb_grupo.id_grupo = $id_grupo ";
}

if(!empty($id_igreja)){
	$where .= " and tb_grupo.cod_igreja = $id_igreja ";
}

if(!$gerenciar && $id_usuario > 0){
	$where .= " and tb_grupo_participante.cod_participante = $id_usuario ";
}

try{

	$con->beginTransaction();

	$str = "SELECT 

	tb_grupo.*, 

	tb_usuario.nome as lider_nome,
	tb_usuario.avatar as lider_avatar,
	tb_usuario.email as lider_email,
	tb_usuario.telefone as lider_telefone,


	COUNT(tb_grupo_participante.id_grupo_participante) as qtd_participantes

	FROM tb_grupo 

	INNER JOIN tb_usuario ON(tb_grupo.cod_lider = tb_usuario.id_usuario) 
	LEFT JOIN tb_grupo_participante ON(tb_grupo_participante.cod_grupo = tb_grupo.id_grupo and tb_grupo_participante.excluido = 0)

	WHERE tb_grupo.excluido = 0 $where GROUP BY tb_grupo.id_grupo";

	// echo $str;

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>