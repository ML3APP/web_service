<?php  

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

// require_once("../sendEmail.php");
require_once("../sendNotificacao.php");

$id_usuario = $obj['id_usuario'];
$id_post = $obj['id_post'];

try{

	$con->beginTransaction();

	$sql_usuario = $con->query("SELECT * FROM tb_curtida Where tb_usuario_id_usuario = $id_usuario and tb_post_id_post = $id_post");
	$result_usuario = $sql_usuario->fetchAll();

	if(COUNT($result_usuario) > 0){
		$id_curtida = $result_usuario[0]['id_curtida'];
		$sql_usuario = $con->exec("DELETE FROM tb_curtida WHERE id_curtida = $id_curtida");
	}	else{
		$sql_usuario = $con->exec("INSERT INTO tb_curtida (tb_usuario_id_usuario, tb_post_id_post ) VALUES ($id_usuario, $id_post)");
		$inseriu = true;	
	}

	$con->commit();
	
	if($inseriu){
	// 	SendEmail::sendEmailCurtir( $id_post,$id_usuario);		
		SendNotificacao::sendNotificacaoCurtir($id_post,$id_usuario);
		echo "inseriu";
	}


}catch(Exception $e){
	$con->rollback();
}


?>