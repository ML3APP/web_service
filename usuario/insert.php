<?php 



include("../connect/db_conect.php");
include("../sendEmail.php");

$connect = new Con();
$con = $connect->getCon();

$usuario = json_decode($_POST['usuario'], true);

$avatar = $_POST['avatar'];

$cod_cargo				= $usuario['cod_cargo'];
$nome				 	= $usuario['nome'];
$email				 	= $usuario['email'];
$telefone				= $usuario['telefone'];
$cpf				 	= $usuario['cpf'];
$trabalhando			= $usuario['trabalhando'];
$sexo				 	= $usuario['sexo'];
$bairro				 	= $usuario['bairro'];
$rua				 	= $usuario['rua'];
$estado					= $usuario['estado'];
$cidade					= $usuario['cidade'];
$dt_nascimento			= $usuario['dt_nascimento'];
$cod_igreja				= $usuario['cod_igreja'];
$responsavel			= $usuario['responsavel'];
$cod_perfil				= $usuario['cod_perfil'];
$celular				= $usuario['celular'];
$cep					= $usuario['cep'];
$estado_civil			= $usuario['estado_civil'];
$codigo_igreja			= $usuario['codigo_igreja'];
$permissoes				= $usuario['permissoes'];
$numero				    = $usuario['numero'];
$data_convercao			= $usuario['data_convercao'];
$data_batismo			= $usuario['data_batismo'];

$senha					= $usuario['senha'];

date_default_timezone_set('America/Sao_Paulo');

if(empty($senha)){
	$date = new DateTime();
	$senha = substr(sha1($date->getTimestamp()), 0, 4);
}

if(empty($cod_cargo)){	
	$cod_cargo = 0;
}

if(empty($permissoes)){	
	$permissoes = "{}";
}else{
	$permissoes = json_encode($permissoes);
}

if(empty($responsavel)){	
	$responsavel = 0;
	$permissoes = '{"editar_sede":true,"congregacoes":true,"usuarios":true,"cargo":true,"membros":true,"categorias":true,"entradas_e_saidas":true,"planos":true,"resumo":true,"relatorios":true,"mensalidades_do_plano":true,"gerenciar_grupos":true,"gerenciar_eventos":true,"editar_igreja":true,"enviar_notificacao":true}';
}

if(empty($cod_igreja)){	
	$cod_igreja = 0;
}

if(empty($trabalhando)){	
	$trabalhando = 0;
}



try{

	$con->beginTransaction();

	
	if(!empty($email)){

		$sql_usuario = $con->query("SELECT * FROM tb_usuario WHERE email = '$email' and excluido = 0");
		$result_usuario = $sql_usuario->fetchAll();

		if(COUNT($result_usuario) > 0){
			echo "email_ja_cadastrado";
			die();
		}

	}	

	if(!empty($cpf)){

		$sql_usuario = $con->query("SELECT * FROM tb_usuario WHERE cpf = '$cpf' and excluido = 0");
		$result_usuario = $sql_usuario->fetchAll();

		if(COUNT($result_usuario) > 0){
			echo "cpf_ja_cadastrado";
			die();
		}

	}

	if(!empty($codigo_igreja)){	
		$str_igreja = "SELECT id_igreja FROM tb_igreja WHERE excluido = 0 and codigo_igreja = '$codigo_igreja'";

		$sql_igreja = $con->query($str_igreja);
		$result_igreja = $sql_igreja->fetchAll();

		if(COUNT($result_igreja) == 0){
			echo "codigo_igreja_invalido";
			die();
		}else{
			$cod_igreja = $result_igreja[0]['id_igreja'];
		}

	}

	$str = "INSERT INTO tb_usuario (

	cod_cargo,
	nome,
	email,
	telefone,
	cpf,
	trabalhando,
	sexo,
	bairro,
	rua,
	estado,
	cidade,
	dt_nascimento,
	avatar,
	cod_igreja,
	cod_perfil,
	senha,
	celular,
	cep,
	estado_civil,
	notificar_email,
	permissoes,
	responsavel,
	numero,
	data_convercao,
	data_batismo
	
	) 

	VALUE (

	$cod_cargo,
	'$nome',
	'$email',
	'$telefone',
	'$cpf',
	$trabalhando,
	'$sexo',
	'$bairro',
	'$rua',
	'$estado',
	'$cidade',
	'$dt_nascimento',
	'$avatar',
	$cod_igreja,
	$cod_perfil,
	'$senha',
	'$celular',
	'$cep',
	'$estado_civil',
	1,
	'$permissoes',
	$responsavel,
	'$numero',
	'$data_convercao',
	'$data_batismo'

)";

	// echo $str;

$sql = $con->exec($str);

$lastId = $con->lastInsertId();


$con->commit();

if($sql){	
	$con->beginTransaction();

	if($avatar != "default.png"){
		if(move_uploaded_file($_FILES["file"]["tmp_name"], "../../upload/avatar/".$avatar)){
			//echo "tudo certo";
		}else{
			//echo "deu ruim";
		}
	}

	echo "deu_bom";

	SendEmail::sendEmailNovoUsuario($lastId);

	$con->commit();

		// SendNotificacao::sendNotificacaoNovoPai($id_usuario, $id_filho);
}else{
	echo "deu_ruim";		
}

}catch(Exception $e){
	$con->rollback();
}

?>