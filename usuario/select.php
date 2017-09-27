<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_usuario = $obj["id_usuario"];
$cod_perfil = $obj["cod_perfil"];
$aniversariantes = $obj["aniversariantes"];
$id_igreja = $obj["id_igreja"];
$id_grupo = $obj["id_grupo"];

$where = 1;

if($cod_perfil > 0){
	$where .= " and tb_usuario.cod_perfil = $cod_perfil ";
}

if($id_usuario > 0){
	$where .= " and tb_usuario.id_usuario = $id_usuario ";
}

if($aniversariantes){
	$where .= " and MONTH(tb_usuario.dt_nascimento) = MONTH(NOW()) and DAY(tb_usuario.dt_nascimento) = DAY(NOW()) ";
}

$innerGrupo = "";

if($id_grupo > 0){
	$innerGrupo .= " INNER JOIN tb_grupo_participante ON (tb_grupo_participante.cod_grupo = $id_grupo and tb_grupo_participante.cod_participante = tb_usuario.id_usuario) ";
}

if($id_igreja > 0){
	$where .= " and tb_usuario.cod_igreja = $id_igreja ";
}


try{

	$con->beginTransaction();


	$str = "SELECT tb_cargo.*, tb_usuario.* FROM tb_usuario $innerGrupo LEFT JOIN tb_cargo ON(tb_cargo.id_cargo = tb_usuario.cod_cargo) WHERE $where and tb_usuario.excluido = 0 ORDER BY tb_usuario.nome";

// echo $str;
	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>