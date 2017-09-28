<?php 



include("../connect/db_conect.php");
include("../sendEmail.php");

$connect = new Con();
$con = $connect->getCon();

$obj = json_decode(file_get_contents('php://input'), true);
$usuarios = $obj['usuarios'];

$ids = [];

try{

	$con->beginTransaction();


	for ($i=0; $i < COUNT($usuarios); $i++) { 

		$usuario = $usuarios[$i];

		$avatar				= $usuario['avatar'];
		$nome				= $usuario['nome'];
		$email				= $usuario['email'];
		$cpf				= $usuario['cpf'];
		$bairro				= $usuario['bairro'];
		$cidade				= $usuario['cidade'];
		$estado				= $usuario['estado'];
		$cep				= $usuario['cep'];
		$endereco			= $usuario['endereco'];
		$dt_nascimento		= $usuario['dt_nascimento'];
		$telefone			= $usuario['telefone'];
		$celular			= $usuario['celular'];
		$sexo				= $usuario['sexo'];
		$trabalhando		= $usuario['trabalhando'];
		$data_convercao		= $usuario['data_convercao'];
		$data_batismo		= $usuario['data_batismo'];
		$estado_civil		= $usuario['estado_civil'];
		$cod_igreja			= $usuario['cod_igreja'];
		$cod_perfil			= $usuario['cod_perfil'];
		$permissoes			= $usuario['permissoes'];

		$senha				= 123;

		date_default_timezone_set('America/Sao_Paulo');

		$str = "INSERT INTO tb_usuario (

		avatar,
		nome,
		email,
		cpf,
		bairro,
		cidade,
		estado,
		cep,
		endereco,
		dt_nascimento,
		telefone,
		celular,
		sexo,
		trabalhando,
		data_convercao,
		data_batismo,
		estado_civil,
		cod_igreja,
		cod_perfil,
		permissoes,
		senha,
		notificar_email

		) 

		VALUE (


		'$avatar',
		'$nome',
		'$email',
		'$cpf',
		'$bairro',
		'$cidade',
		'$estado',
		'$cep',
		'$endereco',
		'$dt_nascimento',
		'$telefone',
		'$celular',
		'$sexo',
		$trabalhando,
		'$data_convercao',
		'$data_batismo',
		'$estado_civil',
		$cod_igreja,
		$cod_perfil,
		'$permissoes',
		'$senha',
		1	)";

		// echo $str;

		$sql = $con->exec($str);

		$lastId = $con->lastInsertId();

		array_push($ids, $lastId);

	}

	$con->commit();


	if($sql){	

		echo "deu_bom";

		for ($i=0; $i < COUNT($ids); $i++) { 
			SendEmail::sendEmailNovoUsuario($ids[$i]);
		}

		// SendNotificacao::sendNotificacaoNovoPai($id_usuario, $id_filho);
	}else{
		echo "deu_ruim";		
	}

}catch(Exception $e){
	$con->rollback();
}

?>