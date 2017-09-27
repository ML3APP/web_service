<?php  


// require_once("sendEmail.php");
// require_once("sendNotificacao.php");

// include("header.php");

include("../connect/db_conect.php");

$connect = new Con();
$con = $connect->getCon();

$usuario = json_decode($_POST['usuario'], true);

$avatar = $_POST['avatar'];

$id_usuario				= $usuario['id_usuario'];
$cod_cargo				= $usuario['cod_cargo'];
$nome				 	= $usuario['nome'];
$email				 	= $usuario['email'];
$telefone				= $usuario['telefone'];
$cpf				 	= $usuario['cpf'];
$trabalhando			= $usuario['trabalhando'];
$sexo				 	= $usuario['sexo'];
$bairro				 	= $usuario['bairro'];
$estado					= $usuario['estado'];
$cidade					= $usuario['cidade'];
$dt_nascimento			= $usuario['dt_nascimento'];
$excluido				= $usuario['excluido'];
$celular				= $usuario['celular'];
$cep					= $usuario['cep'];
$estado_civil			= $usuario['estado_civil'];

if(empty($excluido)){
	$excluido = 0;
}

if(empty($cod_cargo)){	
	$cod_cargo = 0;
}

if(empty($cod_igreja)){	
	$cod_igreja = 0;
}

if(empty($trabalhando)){	
	$trabalhando = 0;
}


$aux = "";

if($avatar != "default.png"){
	$aux = " avatar = '$avatar', ";
}

try{

	$con->beginTransaction();


	$str = "UPDATE tb_usuario SET

	cod_cargo = $cod_cargo,
	nome = '$nome',
	email = '$email', 
	telefone = '$telefone', 
	cpf = '$cpf', 
	trabalhando = $trabalhando, 
	sexo = '$sexo', 
	bairro = '$bairro', 
	estado = '$estado', 
	cidade = '$cidade', 
	$aux
	dt_nascimento = '$dt_nascimento',
	celular = '$celular',
	cep = '$cep',
	estado_civil = '$estado_civil',
	excluido = $excluido

	WHERE id_usuario = $id_usuario";

	echo $str;

	$sql = $con->exec($str);

	$con->commit();

	if($sql){	

		if($avatar != "default.png"){
			if(move_uploaded_file($_FILES["file"]["tmp_name"], "../../upload/avatar/".$avatar)){
			//echo "tudo certo";
			}else{
			//echo "deu ruim";
			}
		}

		echo "deu_bom";				
		// SendNotificacao::sendNotificacaoNovoPai($id_usuario, $id_filho);
	}else{
		echo "deu_ruim";		
	}
	

}catch(Exception $e){
	$con->rollback();
}


?>