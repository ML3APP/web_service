<?php  

$obj = json_decode(file_get_contents('php://input'), true);

 include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_usuario = $obj['id_usuario'];

$offset = $obj['offset'];
$limit = $obj['limit'];

try{

	$con->beginTransaction();


	$sql_notificacao = $con->query("SELECT tb_igreja.logomarca, tb_notificacao.*, tb_usuario.nome, tb_usuario.avatar FROM tb_notificacao 

		LEFT JOIN tb_usuario ON(tb_usuario.id_usuario = tb_notificacao.id_de)
		LEFT JOIN tb_igreja ON(tb_usuario.cod_igreja = tb_igreja.id_igreja)

		WHERE tb_notificacao.id_para = $id_usuario and tb_notificacao.tipo != 'nova_mensagem' ORDER BY tb_notificacao.id_notificacao desc LIMIT $limit  OFFSET $offset");

	$result = $sql_notificacao->fetchAll();

	if(COUNT($result) > 0){
		echo json_encode($result);
	}	else{
		echo "nenhuma_notificacao";
	}

	$con->commit();

}catch(Exception $e){
	$con->rollback();
}


?>