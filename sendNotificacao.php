<?php  

// require_once( 'Thread.php' );


class SendNotificacao{

	public static function sendNotificacaoDefault($id_para, $mensagem, $id_de, $tipo, $id_post, $titulo ) {

		if($tipo != 'checkin_ativo' && $tipo != 'checkin_inativo'){



			$insere_notificacao = Con::getCon()->exec("INSERT INTO tb_notificacao (mensagem, id_para, id_de, tipo, cod_post, titulo) VALUES ('$mensagem', $id_para, $id_de, '$tipo', $id_post, '$titulo')");

			echo("INSERT INTO tb_notificacao (mensagem, id_para, id_de, tipo, cod_post) VALUES ('$mensagem', $id_para, $id_de, '$tipo', $id_post)");
						echo("\n");
			echo("\n");
		}

		$sql_reg = Con::getCon()->query("SELECT tb_igreja.logomarca, reg_id, userDe.nome, userDe.avatar FROM tb_reg_id LEFT JOIN tb_usuario as userDe on(userDe.id_usuario = $id_de) LEFT JOIN tb_igreja ON(userDe.cod_igreja = tb_igreja.id_igreja) WHERE tb_reg_id.id_usuario = $id_para");

		$last_id = Con::getCon()->lastInsertId();

		foreach ($sql_reg as $item) {
			echo($item["reg_id"]);
			echo("\n");
			echo("\n");

			define( 'API_ACCESS_KEY', 'AIzaSyBtArMEW7VHz5JPyfH2zC4jHchw0LfREds' );
			$registrationIds = array( $item["reg_id"]);

			if($tipo == "aviso"){
				$image = 'http://35.198.54.48/upload/img/igreja/'.$item["logomarca"];
			}else{
				$titulo = $item["nome"];
				$image = 'http://35.198.54.48/upload/avatar/'.$item["avatar"];
			}
			

			$msg = array
			(
				'message' 	=> $mensagem,
				'title'		=> $titulo,
				'id_de'		=> $id_de,
				'id_para'		=> $id_para,
				'tipo'		=> $tipo,
				'id_post'		=> $id_post,
				'id_notificacao' => $last_id,
				'vibrate'	=> 1,
				'image' => $image,
				'sound'		=> 1
				);

			$fields = array
			(
				'registration_ids' 	=> $registrationIds,
				'data'			=> $msg
				);

			$headers = array
			(
				'Authorization: key=' . API_ACCESS_KEY,
				'Content-Type: application/json'
				);

			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
			$result = curl_exec($ch );
			curl_close( $ch );

		}
		
	}


	public static function sendNotificacaoDenuncia($id_denuncia) {
		SendNotificacao::runInBackground();

		$sql = Con::getCon()->query("SELECT tb_usuario.id_usuario as id_para, tb_post.id_post,

			(SELECT user_den.id_usuario FROM tb_usuario as user_den WHERE tb_denuncia.tb_usuario_id_usuario = user_den.id_usuario) as id_de,
			(SELECT user_den.nome FROM tb_usuario as user_den WHERE tb_denuncia.tb_usuario_id_usuario = user_den.id_usuario) as nome_denunciou,
			(SELECT user_denunciado.nome FROM tb_usuario as user_denunciado WHERE tb_post.tb_usuario_id_usuario = user_denunciado.id_usuario) as nome_denunciado

			FROM tb_denuncia 

			INNER JOIN tb_post ON (tb_post.id_post = tb_denuncia.tb_post_id_post) 
			INNER JOIN tb_usuario ON(tb_usuario.tb_instituicao_id_instituicao = tb_post.tb_instituicao_id_instituicao)

			WHERE tb_denuncia.id_denuncia = $id_denuncia 

			and (tb_usuario.tb_perfil_id_perfil = 3
			or tb_usuario.tb_perfil_id_perfil = 4
			or tb_usuario.tb_perfil_id_perfil = 7)");

		$result = $sql->fetchAll();


		for($i = 0 ; $i < COUNT($result); $i ++){
			SendNotificacao::sendNotificacaoDefault($result[$i]["id_para"], "Denúnciou a publicação de ". $result[$i]["nome_denunciado"], $result[$i]["id_de"], 'denuncia_post', $result[$i]["id_post"]);
		}

	}	

	public static function sendNotificacaoNovaAtividade($id_usuario, $id_post) {
		SendNotificacao::runInBackground();

		$sql_atividade = Con::getCon()->query("SELECT tb_usuario.id_usuario,
			(SELECT id_usuario FROM tb_usuario WHERE tb_post.tb_usuario_id_usuario = tb_usuario.id_usuario) as id_professor,
			(SELECT nome FROM tb_usuario WHERE tb_post.tb_usuario_id_usuario = tb_usuario.id_usuario) as nome_professor,
			(SELECT avatar FROM tb_usuario WHERE tb_post.tb_usuario_id_usuario = tb_usuario.id_usuario) as avatar,
			(SELECT desc_disciplina FROM tb_disciplina WHERE tb_post.tb_disciplina_id_disciplina = tb_disciplina.id_disciplina) as desc_disciplina	

			FROM tb_post 

			INNER JOIN tb_turma ON(tb_post.tb_turma_id_turma = tb_turma.id_turma) 

			LEFT JOIN tb_turma_usuario ON(tb_turma_usuario.tb_turma_id_turma = tb_post.tb_turma_id_turma and tb_turma_usuario.status_turma_usuario = 'ativo')

			INNER JOIN tb_usuario ON (tb_usuario.id_usuario = tb_turma_usuario.tb_usuario_id_usuario) 

			WHERE tb_post.id_post = $id_post");

		$result = $sql_atividade->fetchAll();

		for($i = 0 ; $i < COUNT($result); $i ++){
			SendNotificacao::sendNotificacaoDefault($result[$i]["id_usuario"], "Publicou uma nova atividade de ". $result[$i]["desc_disciplina"]. " no mural da turma", $result[$i]["id_professor"], 'nova_atividade', $id_post);
		}

	}	

	public static function sendNotificacaoAceitarAtividade($id_atividade_entregue) {
		SendNotificacao::runInBackground();

		$sql_atividade = Con::getCon()->query("SELECT aluno.id_usuario as id_para, professor.id_usuario as id_de, tb_post.id_post

			FROM tb_atividade_entregue 

			INNER JOIN tb_usuario as aluno ON (aluno.id_usuario = tb_atividade_entregue.tb_usuario_id_usuario) 
			INNER JOIN tb_post ON (tb_post.id_post = tb_atividade_entregue.tb_post_id_post) 
			INNER JOIN tb_usuario as professor ON (professor.id_usuario = tb_post.tb_usuario_id_usuario) 

			WHERE tb_atividade_entregue.id_atividade_entregue = $id_atividade_entregue");

		$result = $sql_atividade->fetchAll();

		for($i = 0 ; $i < COUNT($result); $i ++){
			SendNotificacao::sendNotificacaoDefault($result[$i]["id_para"], "Marcou sua atividade como entregue", $result[$i]["id_de"], 'aceitou_atividade', $result[$i]["id_post"]);
		}

	}

	public static function sendNotificacaoAmizadeAceita($id_amigo, $id_usuario) {
		SendNotificacao::runInBackground();

		// if(SendEmail::verificaNotificarEmail($id_amigo)){
		// 	SendEmail::sendEmailDefault($nome_para_email, "Amizade", $email,$nome ." ".$mensagem);
		// }

		SendNotificacao::sendNotificacaoDefault($id_amigo, "Aceitou sua solicitação de amizade", $id_usuario, 'aceitou_amizade', 0);


	}

	public static function sendNotificacaoAviso($id_para, $mensagem, $id_de, $tipo, $id_post, $titulo) {
		SendNotificacao::runInBackground();
		SendNotificacao::sendNotificacaoDefault($id_para, $mensagem, $id_de, $tipo, $id_post, $titulo);
	}

	public static function sendNotificacaoRejeitarAtividade($id_atividade_entregue) {
		SendNotificacao::runInBackground();

		$sql_atividade = Con::getCon()->query("SELECT aluno.id_usuario as id_para, professor.id_usuario as id_de, tb_post.id_post

			FROM tb_atividade_entregue 

			INNER JOIN tb_usuario as aluno ON (aluno.id_usuario = tb_atividade_entregue.tb_usuario_id_usuario) 
			INNER JOIN tb_post ON (tb_post.id_post = tb_atividade_entregue.tb_post_id_post) 
			INNER JOIN tb_usuario as professor ON (professor.id_usuario = tb_post.tb_usuario_id_usuario) 

			WHERE tb_atividade_entregue.id_atividade_entregue = $id_atividade_entregue");

		$result = $sql_atividade->fetchAll();

		for($i = 0 ; $i < COUNT($result); $i ++){
			SendNotificacao::sendNotificacaoDefault($result[$i]["id_para"], "Rejeitou sua atividade", $result[$i]["id_de"], 'rejeitou_atividade', $result[$i]["id_post"]);
		}

	}

	public static function sendNotificacaoAtividadeEntregue($id_post, $id_usuario) {
		SendNotificacao::runInBackground();

		$str = "SELECT tb_usuario.id_usuario, tb_usuario.nome,

		(SELECT id_usuario FROM tb_usuario WHERE tb_post.tb_usuario_id_usuario = tb_usuario.id_usuario) as id_professor,
		(SELECT nome FROM tb_usuario WHERE tb_post.tb_usuario_id_usuario = tb_usuario.id_usuario) as nome_professor,
		(SELECT avatar FROM tb_usuario WHERE tb_post.tb_usuario_id_usuario = tb_usuario.id_usuario) as avatar,
		(SELECT desc_disciplina FROM tb_disciplina WHERE tb_post.tb_disciplina_id_disciplina = tb_disciplina.id_disciplina) as desc_disciplina	

		FROM tb_post 

		INNER JOIN tb_turma ON(tb_post.tb_turma_id_turma = tb_turma.id_turma) 
		LEFT JOIN tb_usuario ON (tb_usuario.id_usuario = $id_usuario) 

		WHERE tb_post.id_post = $id_post";

		// echo $str;

		$sql_atividade = Con::getCon()->query($str);

		$result = $sql_atividade->fetchAll();

		for($i = 0 ; $i < COUNT($result); $i ++){
			SendNotificacao::sendNotificacaoDefault($result[$i]["id_professor"], "Entregou sua atividade de ". $result[$i]["desc_disciplina"], $result[$i]["id_usuario"], 'atividade_entregue', $id_post);
		}

	}

	public static function sendNotificacaoCheckInAtivo($id_turno, $id_usuario, $ativo) {
		SendNotificacao::runInBackground();

		$mensagem = "";

		if($ativo == 1){
			$mensagem = "Iniciou";
		}else{
			$mensagem = "Encerrou";
		}

		$sql_alunos_turno = Con::getCon()->query("SELECT tb_turno.desc_turno, tb_instituicao.nome as nome_instituicao, tb_usuario.id_usuario 
			FROM tb_usuario 

			INNER JOIN tb_turma_usuario ON (tb_turma_usuario.tb_usuario_id_usuario = tb_usuario.id_usuario and tb_turma_usuario.status_turma_usuario = 'ativo')

			INNER JOIN tb_turma ON (tb_turma.id_turma = tb_turma_usuario.tb_turma_id_turma)

			INNER JOIN tb_turno ON (tb_turno.id_turno = tb_turma.tb_turno_id_turno)

			INNER JOIN tb_instituicao ON (tb_turma.tb_instituicao_id_instituicao = tb_instituicao.id_instituicao)

			WHERE tb_turma.tb_turno_id_turno = $id_turno");

		$result = $sql_alunos_turno->fetchAll();

		for($i = 0 ; $i < COUNT($result); $i ++){
			SendNotificacao::sendNotificacaoDefault($result[$i]['id_usuario'], $mensagem ." o Check In para o turno ". $result[$i]['desc_turno'] . " da instituição: ". $result[$i]['nome_instituicao'], $id_usuario, 'checkin_ativo', 0);
		}

	}
	
	public static function sendNotificacaoAlunoPrimeiraChamada($id_aluno, $presente) {
		SendNotificacao::runInBackground();

		$mensagem = "";
		$tipo = "";

		if($presente == 1){
			$mensagem = "Está presente";
			$tipo = "aluno_presente_primeira_aula";
		}else{
			$mensagem = "Não está presente";
			$tipo = "aluno_nao_presente_primeira_aula";
		}

		$sql = Con::getCon()->query("SELECT tb_turma.desc_turma, tb_instituicao.nome as nome_instituicao, tb_usuario.id_usuario, tb_usuario.nome,tb_usuario.avatar 

			FROM tb_usuario 

			INNER JOIN tb_pai_filho ON(tb_pai_filho.id_pai = tb_usuario.id_usuario and aprovado = 1)

			INNER JOIN tb_turma ON(tb_turma.id_turma = 

			(SELECT tb_turma_usuario.tb_turma_id_turma FROM tb_turma_usuario WHERE tb_turma_usuario.tb_usuario_id_usuario = $id_aluno and tb_turma_usuario.status_turma_usuario = 'ativo')
			)
			LEFT JOIN tb_instituicao ON(tb_instituicao.id_instituicao = (SELECT tu.tb_instituicao_id_instituicao FROM tb_usuario as tu WHERE tu.id_usuario = $id_aluno))
			WHERE tb_pai_filho.id_filho = $id_aluno");

		$result = $sql->fetchAll();


		for($i = 0 ; $i < COUNT($result); $i ++){

			SendNotificacao::sendNotificacaoDefault($result[$i]["id_usuario"], 

				$mensagem." na primeira chamada da turma ". $result[$i]['desc_turma'] .", da instituição: ". $result[$i]['nome_instituicao'] . " no dia ".date("d/m/y")." às ". date("H:i"), $id_aluno, $tipo, 0);
		}

	}	

	public static function sendNotificacaoAlunoRealizouCheckin($id_aluno) {
		SendNotificacao::runInBackground();

		$sql = Con::getCon()->query("SELECT tb_instituicao.nome as nome_instituicao, tb_usuario.id_usuario, tb_usuario.nome,tb_usuario.avatar 

			FROM tb_usuario 

			INNER JOIN tb_pai_filho ON(tb_pai_filho.id_pai = tb_usuario.id_usuario and aprovado = 1)
			LEFT JOIN tb_instituicao ON(tb_instituicao.id_instituicao = (SELECT tu.tb_instituicao_id_instituicao FROM tb_usuario as tu WHERE tu.id_usuario = $id_aluno))
			WHERE tb_pai_filho.id_filho = $id_aluno");

		$result = $sql->fetchAll();

		for($i = 0 ; $i < COUNT($result); $i ++){

			SendNotificacao::sendNotificacaoDefault($result[$i]["id_usuario"], "Realizou check in na instituição: ". $result[$i]['nome_instituicao'] . " no dia ".date("d/m/y")." às ". date("H:i"), $id_aluno, 'aluno_realizou_checkin', 0);
		}

	}

	public static function sendNotificacaoNovoPai($id_pai, $id_filho) {
		SendNotificacao::runInBackground();

		$sql_atividade = Con::getCon()->query("SELECT id_usuario, nome,avatar FROM tb_usuario WHERE id_usuario = $id_pai");
		$result = $sql_atividade->fetchAll();
		for($i = 0 ; $i < COUNT($result); $i ++){
			SendNotificacao::sendNotificacaoDefault($id_filho, "Adicionou você como filho.", $result[$i]["id_usuario"], 'solicitacao_pai', 0);
		}

	}

	public static function sendNotificacaoRespostaPai($id_pai, $id_filho, $resposta) {
		SendNotificacao::runInBackground();

		if($resposta){				
			SendNotificacao::sendNotificacaoDefault($id_pai, "Confirmou ser seu filho.", $id_filho, 'aprovou_paternidade', 0);	
		}else{
			SendNotificacao::sendNotificacaoDefault($id_pai, "Negou ser seu filho.", $id_filho, 'negou_paternidade', 0);	
		}


	}

	public static function sendNotificacaoCurtirComentario($id_comentario, $id_usuario) {
		SendNotificacao::runInBackground();

		$sql_atividade = Con::getCon()->query("SELECT  tb_post.id_post, tb_usuario.id_usuario, tb_comentario.comentario

			FROM tb_comentario 

			INNER JOIN tb_post ON (tb_post.id_post = tb_comentario.tb_post_id_post) 
			INNER JOIN tb_usuario ON (tb_usuario.id_usuario = tb_comentario.tb_usuario_id_usuario) 

			WHERE tb_comentario.id_comentario = $id_comentario");

		$result = $sql_atividade->fetchAll();

		for($i = 0 ; $i < COUNT($result); $i ++){
			if($result[$i]["id_usuario"] != $id_usuario){
				SendNotificacao::sendNotificacaoDefault($result[$i]["id_usuario"], "Curtiu seu comentários (". $result[$i]["comentario"]. ")", $id_usuario, 'comentario_curtido', $result[$i]["id_post"]);			
			}
		}

	}

	public static function sendNotificacaoCurtir($id_post, $id_usuario) {
		SendNotificacao::runInBackground();

		$sql_atividade = Con::getCon()->query("SELECT 

			tb_usuario.avatar, 
			tb_usuario.nome, 
			tb_post.mensagem, 
			(SELECT email FROM tb_usuario WHERE tb_post.cod_usuario = tb_usuario.id_usuario) as email,
			(SELECT id_usuario FROM tb_usuario WHERE tb_post.cod_usuario = tb_usuario.id_usuario) as id_usuario,
			(SELECT nome FROM tb_usuario WHERE tb_post.cod_usuario = tb_usuario.id_usuario) as nome_postou 

			FROM tb_post 

			INNER JOIN tb_usuario ON (tb_usuario.id_usuario = $id_usuario) 

			WHERE tb_post.id_post = $id_post");


		$result = $sql_atividade->fetchAll();

		for($i = 0 ; $i < COUNT($result); $i ++){
			if($result[$i]["id_usuario"] != $id_usuario){
				SendNotificacao::sendNotificacaoDefault($result[$i]["id_usuario"], "Curtiu sua publicação (". $result[$i]["mensagem"]. ")", $id_usuario, 'post_curtido', $id_post);			
			}
		}

	}

	public static function sendNotificacaoCompartilharPost($id_post, $id_usuario) {
		SendNotificacao::runInBackground();

		$sql_atividade = Con::getCon()->query("SELECT 

			tb_usuario.avatar, 
			tb_usuario.nome, 
			tb_post.mensagem,
			usuario_postou.email,
			usuario_postou.id_usuario

			FROM tb_post 

			INNER JOIN tb_usuario as usuario_postou ON (usuario_postou.id_usuario = tb_post.tb_usuario_id_usuario ) 
			INNER JOIN tb_usuario ON (tb_usuario.id_usuario = $id_usuario) 

			WHERE tb_post.id_post = $id_post");

		$result = $sql_atividade->fetchAll();

		for($i = 0 ; $i < COUNT($result); $i ++){
			if($result[$i]["id_usuario"] != $id_usuario){
				SendNotificacao::sendNotificacaoDefault($result[$i]["id_usuario"], "Compartilhou sua Publicação (". $result[$i]["mensagem"]. ")", $id_usuario, 'post_compartilhado', $id_post);			
			}
		}

	}

	public static function sendNotificacaoPostMuralEscola($id_usuario, $id_post, $id_instituicao) {
		SendNotificacao::runInBackground();

		$sql_atividade = Con::getCon()->query("SELECT 

			tb_post.mensagem,
			aluno.id_usuario

			FROM tb_usuario as aluno 

			INNER JOIN tb_post ON (tb_post.id_post = $id_post) 

			WHERE aluno.tb_instituicao_id_instituicao = $id_instituicao");

		$result = $sql_atividade->fetchAll();

		for($i = 0 ; $i < COUNT($result); $i ++){
			if($result[$i]["id_usuario"] != $id_usuario){
				SendNotificacao::sendNotificacaoDefault($result[$i]["id_usuario"], "Publicou no mural da Escola (". $result[$i]["mensagem"]. ")", $id_usuario, 'post_mural_escola', $id_post);			
			}
		}

	}

	public static function sendNotificacaoPostMuralTurma($id_usuario, $id_post, $id_turma) {
		SendNotificacao::runInBackground();

		$sql_atividade = Con::getCon()->query("SELECT 

			tb_post.mensagem,
			tb_usuario.id_usuario

			FROM tb_usuario 

			INNER JOIN tb_post ON (tb_post.id_post = $id_post) 

			INNER JOIN tb_turma_usuario ON(tb_turma_usuario.tb_turma_id_turma = $id_turma and tb_turma_usuario.status_turma_usuario = 'ativo')

			WHERE tb_usuario.id_usuario = tb_turma_usuario.tb_usuario_id_usuario");

		$result = $sql_atividade->fetchAll();

		for($i = 0 ; $i < COUNT($result); $i ++){
			if($result[$i]["id_usuario"] != $id_usuario){
				SendNotificacao::sendNotificacaoDefault($result[$i]["id_usuario"], "Publicou no mural da Turma (". $result[$i]["mensagem"]. ")", $id_usuario, 'post_mural_turma', $id_post);			
			}
		}

	}

	public static function sendNotificacaoPaiFilhoForaEscola($id_filho) {
		SendNotificacao::runInBackground();

		$sql_atividade = Con::getCon()->query("SELECT tb_usuario.id_usuario, tb_usuario.avatar, tb_usuario.nome, tb_pai_filho.id_pai FROM tb_pai_filho INNER JOIN tb_usuario ON (tb_usuario.id_usuario = tb_pai_filho.id_filho) WHERE tb_pai_filho.id_filho = $id_filho");

		$result = $sql_atividade->fetchAll();

		for($i = 0 ; $i < COUNT($result); $i ++){

			SendNotificacao::sendNotificacaoDefault($result[$i]["id_pai"], "Não está na escola.", $result[$i]["id_usuario"], 'filho_fora_da_escola', 0);

			echo $i;
		}

	}
	public static function sendNotificacaoMensagem($id_usuario, $id_usuario_chat, $msg) {
		SendNotificacao::runInBackground();

		$sql_atividade = Con::getCon()->query("SELECT tb_usuario.avatar, tb_usuario.nome FROM tb_usuario WHERE tb_usuario.id_usuario = $id_usuario");
		$result = $sql_atividade->fetchAll();

		for($i = 0 ; $i < COUNT($result); $i ++){
			SendNotificacao::sendNotificacaoDefault($id_usuario_chat, "Enviou uma nova Mensagem (". $msg . ")", $id_usuario, 'nova_mensagem', 0);			
			
		}

	}

	public static function runInBackground(){
		ob_start();

		$size = ob_get_length();
		header("Content-Encoding: none");
		header("Content-Length: {$size}");
		header("Connection: close");
		ob_end_flush();
		ob_flush();
		flush();
	}

}

?>