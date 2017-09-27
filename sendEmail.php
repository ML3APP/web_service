<?php  

class SendEmail{

	public static function verificaNotificarEmail($id_usuario){

		$sql = Con::getCon()->query("SELECT notificar_email FROM tb_usuario WHERE id_usuario = $id_usuario");
		$notificar_email = $sql->fetchAll();

		print_r($id_usuario);

		if($notificar_email[0]["notificar_email"] == 1){
			return true;
		}else{
			return false;
		}

	}

	public static function sendEmailDefault($emaildestinatario ,$assunto, $mensagem) {

		$emailsender = "contato@noveltysolucoes.com.br";

		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8"."\r\n";	
		$headers .= utf8_decode("From: ML3 <$emailsender>"."\r\n"); 

		$body = '<html>
		<head>
			<meta charset="UTF-8"/>    
			<title>Email</title>  
		</head>
		<body>

			<div marginwidth="0" marginheight="0" bgcolor="#e6e6e6" style="width:100%!important;background:#e6e6e6">
				<table cellspacing="0" cellpadding="0" border="0" bgcolor="#e6e6e6" style="width:100%">            

					<tr>
						<td width="100%" bgcolor="#e6e6e6">
							<table cellspacing="0" cellpadding="0" border="0" align="center" style="width:576px">                        
								<tbody>
									<tr>
										<td width="576"> 
											<table cellspacing="0" cellpadding="0" border="0" style="background-color:#ffffff;width:576px">
												<tbody>

													<tr>
														<td><img width="576" height="83" border="0" style="margin:0;padding:0;display:block" src="http://www.lyce.com.br/app/upload/lira.png" alt="" class="CToWUd"></td>
													</tr>
													<tr>
														<td width="576" align="center" style="background-color:#ffffff;padding:0px 0px 0px 0px">
															<table width="400px" cellspacing="0" cellpadding="0" border="0">
																<tbody>
																	<tr>
																		<td><br>

																			'.$mensagem.'

																		</p><br>
																	</tr>

																</tbody>
															</table>

															<table width="400px">
																<tbody>

																	<tr>
																		<td width="400"><br>
																			<p align="justify" style="color:#747474;font-family:Helvetica Neue,Helvetica,Arial,sans-serif,arial,serif;font-size:12px;line-height:20px;padding-left:4px;padding:5px 0 5px 0;margin:0 0 0 0">
																				Muito obrigado pela confiança, e estou aqui sempre que precisar! Caso precise, é só responder este e-mail.<br>
																				<br><br>

																			</td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr>									
													</tbody>
												</table>
											</td>
										</tr>

									</tbody>
								</table>
							</td>
						</tr> 
					</table>	
				</div>


			</body>
			</html>';


			mail($emaildestinatario, $assunto, $body, $headers ,"-r".$emailsender);
		}

		public static function sendEmailNovoUsuario($id_usuario) {

			if(!SendEmail::verificaNotificarEmail($id_usuario)) return;

			$str =  "

			SELECT 

			tb_igreja.*, tb_usuario.*

			FROM tb_usuario 

			INNER JOIN tb_igreja ON(tb_usuario.cod_igreja = tb_igreja.id_igreja) 
			LEFT JOIN tb_cargo ON(tb_usuario.cod_cargo = tb_cargo.id_cargo) 

			WHERE tb_usuario.id_usuario = $id_usuario

			";

			$sql = Con::getCon()->query($str);		

			$result = $sql->fetchAll();

			$usuario = $result[0];



			$perfil = $usuario['cod_perfil'];
			$responsavel = $usuario['responsavel'];
			$desc_igreja = $usuario['desc_igreja'];
			$desc_cargo = $usuario['desc_cargo'];

			//responsável			
			if($responsavel == 1){
				$msg_perfil = "Você foi adicionado como Responsável da igreja ". $desc_igreja;
			}else 
			//usuario			
			if($perfil == 1){
				$msg_perfil = "Você foi adicionado como ".$desc_cargo." na igreja ". $desc_igreja;
			}else 
			//membro
			if($perfil == 2){
				$msg_perfil = "Você foi adicionado como Membro da igreja ". $desc_igreja;

			}

			SendEmail::sendEmailDefault(

				$result[0]['email'],
				"Bem Vindo", 
				
				'Olá '.$usuario['nome'].', seja bem vindo ao ml3.<br><br>'.
				$msg_perfil."<br><br>".
				"Informações para login:<br>".
				"E-mail: ".$usuario['email'].
				"<br>Senha: ".$usuario['senha']

				);

		}

	}

	?>