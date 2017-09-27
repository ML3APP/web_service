<?php  

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_post = $obj['id_post'];
$id_usuario = $obj['id_usuario'];
$comentario = $obj['comentario'];

// require_once("../sendEmail.php");
// require_once("../sendNotificacao.php");

try{

	$con->beginTransaction();

	$sql_usuario = $con->exec("INSERT INTO tb_comentario (comentario, tb_usuario_id_usuario, tb_post_id_post) VALUES ('$comentario', $id_usuario, $id_post)");

	if($sql_usuario){
		echo "sim_cadastrou";

		$sql = $con->query("SELECT tb_post.mensagem, tb_usuario.id_usuario, tb_usuario.nome, tb_usuario.email, 

			(SELECT nome from tb_usuario WHERE id_usuario = $id_usuario) as nome_usuario, 
			(SELECT avatar from tb_usuario WHERE id_usuario = $id_usuario) as avatar_usuario, 
			(SELECT avatar from tb_usuario WHERE id_usuario = $id_usuario) as img_usuario 

			FROM tb_usuario 

			INNER JOIN tb_post ON(tb_post.cod_usuario = tb_usuario.id_usuario) 

			Where tb_post.id_post = $id_post");

		$sql_email = $sql->fetchAll();

		$email = $sql_email[0]['email'];
		$nome2 = $sql_email[0]['nome'];

		$id_usuario_postou = $sql_email[0]['id_usuario'];
		$mensagem = $sql_email[0]['mensagem'];
		$nome_usuario = $sql_email[0]['nome_usuario'];
		$avatar_usuario = $sql_email[0]['avatar_usuario'];
		$url = "#/app/home/tabs/post_aberto/".$id_post;


		if($id_usuario != $id_usuario_postou){
			// if(SendEmail::verificaNotificarEmail($id_usuario_postou)){
			// 	SendEmail::sendEmailDefault($nome2, "Comentario", $email , $nome_usuario ." comentou seu post (". $mensagem." )");
			// }


			SendNotificacao::sendNotificacaoDefault($id_usuario_postou, 'Comentou seu Post ('. $mensagem.' )', $id_usuario, 'comentou_post', $id_post);
		}else{
			$sql_quem_comentou = $con->query("SELECT tb_usuario.id_usuario, tb_usuario.nome FROM tb_usuario INNER JOIN tb_comentario ON(tb_comentario.tb_usuario_id_usuario = tb_usuario.id_usuario) Where tb_usuario.id_usuario != $id_usuario_postou and tb_comentario.tb_post_id_post = $id_post GROUP BY tb_comentario.tb_usuario_id_usuario");

			foreach ($sql_quem_comentou as $ids) {

				// if(SendEmail::verificaNotificarEmail($ids["id_usuario"])){
				// 	SendEmail::sendEmailDefault($ids["nome"], "Comentario", $email , $nome_usuario ." também comentou o post (". $mensagem ." )");
				// }
				// SendNotificacao::sendNotificacaoDefault($ids["id_usuario"], ' Também comentou o post ( '. $mensagem .' )', $avatar_usuario, $url);
			}
		}
	}	else{
		echo "nao_cadastrou";
	}

	$con->commit();

}catch(Exception $e){
	$con->rollback();
}


?>