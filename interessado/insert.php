<?php  

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

// require_once("../sendEmail.php");
// require_once("../sendNotificacao.php");

$id_usuario = $obj['id_usuario'];
$id_evento = $obj['id_evento'];

try{

	$con->beginTransaction();

	$sql_usuario = $con->query("SELECT * FROM tb_interessado_evento Where cod_usuario = $id_usuario and cod_evento = $id_evento");
	$result_usuario = $sql_usuario->fetchAll();

	if(COUNT($result_usuario) > 0){
		$id_interessado_evento = $result_usuario[0]['id_interessado_evento'];
		$sql_usuario = $con->exec("DELETE FROM tb_interessado_evento WHERE id_interessado_evento = $id_interessado_evento");
	}	else{
		$sql_usuario = $con->exec("INSERT INTO tb_interessado_evento (cod_usuario, cod_evento ) VALUES ($id_usuario, $id_evento)");
		$inseriu = true;	
	}

	$con->commit();
	
	if($inseriu){
	// 	SendEmail::sendEmailCurtir( $id_evento,$id_usuario);		
	// 	SendNotificacao::sendNotificacaoCurtir($id_evento,$id_usuario);
		echo "inseriu";
	}


}catch(Exception $e){
	$con->rollback();
}


?>