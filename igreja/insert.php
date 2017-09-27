<?php 


include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();


$igreja = json_decode($_POST['igreja'], true);

$avatar = $_POST['avatar'];

$desc_igreja 		= $igreja['desc_igreja'];
$telefone 			= $igreja['telefone'];
$endereco 			= $igreja['endereco'];
$cidade 			= $igreja['cidade'];
$estado 			= $igreja['estado'];
$cep 				= $igreja['cep'];
$email 				= $igreja['email'];
$cpf 				= $igreja['cpf'];
$cnpj 				= $igreja['cnpj'];
$bairro 			= $igreja['bairro'];
$logomarca 			= $igreja['logomarca'];
$tipo 				= $igreja['tipo'];
$cod_denominacao 	= $igreja['cod_denominacao'];
$dt_abertura 		= $igreja['dt_abertura'];
$cod_sede 			= $igreja['cod_sede'];
$descricao_igreja 	= $igreja['descricao_igreja'];
$banco 				= $igreja['banco'];
$tipo_conta 		= $igreja['tipo_conta'];
$conta 				= $igreja['conta'];
$agencia 			= $igreja['agencia'];
$redes_sociais 		= $igreja['redes_sociais'];
$rua 				= $igreja['rua'];
$dados_bancarios 	= $igreja['dados_bancarios'];
$localizacao 		= $igreja['localizacao'];
$numero 			= $igreja['numero'];


$repasse_pr 				= $igreja['repasse_pr'];
$tipo_repasse_pr 			= $igreja['tipo_repasse_pr'];
$valor_repasse_pr 			= $igreja['valor_repasse_pr'];
$porcentagem_repasse_pr 	= $igreja['porcentagem_repasse_pr'];
$repasse_sede 				= $igreja['repasse_sede'];
$tipo_repasse_sede 			= $igreja['tipo_repasse_sede'];
$valor_repasse_sede 		= $igreja['valor_repasse_sede'];
$porcentagem_repasse_sede 	= $igreja['porcentagem_repasse_sede'];


$cod_pastor_presidente 	= $igreja['cod_pastor_presidente'];

$email_pagseguro 	= $igreja['email_pagseguro'];
$token_pagseguro 	= $igreja['token_pagseguro'];

$tipo_pessoa 	= $igreja['tipo_pessoa'];

$excluido 			= 0;

$codigo_igreja = strtoupper(substr(uniqid(), -6, -1));

if($dados_bancarios){
	$dados_bancarios = 1;	
}else{
	$dados_bancarios = 0;		
}


if(empty($repasse_pr)){					$repasse_pr = 0;}
if(empty($tipo_repasse_pr)){			$tipo_repasse_pr = '';}
if(empty($valor_repasse_pr)){			$valor_repasse_pr = 0;}
if(empty($porcentagem_repasse_pr)){		$porcentagem_repasse_pr = 0;}
if(empty($repasse_sede)){				$repasse_sede = 0;}
if(empty($tipo_repasse_sede)){			$tipo_repasse_sede = '';}
if(empty($valor_repasse_sede)){			$valor_repasse_sede = 0;}
if(empty($porcentagem_repasse_sede)){	$porcentagem_repasse_sede = 0;}



if(empty($cod_denominacao)){
	$cod_denominacao = 0;
}

if(empty($cod_pastor_presidente)){
	$cod_pastor_presidente = 0;
}

if(empty($redes_sociais)){
	$redes_sociais = "{}";
}else{
	$redes_sociais = json_encode($redes_sociais);
}

if(empty($localizacao)){
	$localizacao = "{}";
}else{
	$localizacao = json_encode($localizacao);
}

if(empty($cod_sede)){
	$cod_sede = 0;
}

try{

	$con->beginTransaction();



	if(!empty($email)){

		$sql_usuario = $con->query("SELECT * FROM tb_igreja WHERE email = '$email' and excluido = 0");
		$result_usuario = $sql_usuario->fetchAll();

		if(COUNT($result_usuario) > 0){
			echo "email_ja_cadastrado";
			die();
		}

	}	

	if(!empty($cpf)){

		$sql_usuario = $con->query("SELECT * FROM tb_igreja WHERE cpf = '$cpf' and excluido = 0");
		$result_usuario = $sql_usuario->fetchAll();

		if(COUNT($result_usuario) > 0){
			echo "cpf_ja_cadastrado";
			die();
		}

	}

	if(!empty($cnpj)){

		$sql_usuario = $con->query("SELECT * FROM tb_igreja WHERE cnpj = '$cnpj' and excluido = 0");
		$result_usuario = $sql_usuario->fetchAll();

		if(COUNT($result_usuario) > 0){
			echo "cnpj_ja_cadastrado";
			die();
		}

	}


	$str = "INSERT INTO tb_igreja (

	desc_igreja, 
	telefone, 
	endereco, 
	cidade, 
	estado, 
	cep, 
	email, 
	cpf, 
	cnpj, 
	bairro, 
	logomarca, 
	tipo, 
	cod_denominacao, 
	dt_abertura, 
	cod_sede, 
	excluido,
	descricao_igreja,
	banco,
	tipo_conta,
	conta,
	agencia,
	dados_bancarios,
	rua,
	redes_sociais,
	localizacao,
	codigo_igreja,
	email_pagseguro,
	token_pagseguro,
	numero,
	tipo_pessoa,
	cod_pastor_presidente,

	repasse_pr,
	tipo_repasse_pr,
	valor_repasse_pr,
	porcentagem_repasse_pr,
	repasse_sede,
	tipo_repasse_sede,
	valor_repasse_sede,
	porcentagem_repasse_sede

	) 

	VALUE (

	'$desc_igreja', 
	'$telefone', 
	'$endereco', 
	'$cidade', 
	'$estado', 
	'$cep', 
	'$email', 
	'$cpf', 
	'$cnpj', 
	'$bairro', 
	'$avatar', 
	'$tipo', 
	$cod_denominacao, 
	'$dt_abertura', 
	$cod_sede,
	$excluido,
	'$descricao_igreja',
	'$banco',
	'$tipo_conta',
	'$conta',
	'$agencia',
	$dados_bancarios,
	'$rua',
	'$redes_sociais',
	'$localizacao',
	'$codigo_igreja',
	'$email_pagseguro',
	'$token_pagseguro',
	'$numero',
	'$tipo_pessoa',
	$cod_pastor_presidente,

	$repasse_pr,
	'$tipo_repasse_pr',
	'$valor_repasse_pr',
	$porcentagem_repasse_pr,
	$repasse_sede,
	'$tipo_repasse_sede',
	'$valor_repasse_sede',
	$porcentagem_repasse_sede

)";

	// echo $str;

$sql_igreja = $con->exec($str);	

if($sql_igreja){

	if($avatar != "default.png"){
		if(move_uploaded_file($_FILES["file"]["tmp_name"], "../../upload/img/igreja/".$avatar)){
			//echo "tudo certo";
		}else{
			//echo "deu ruim";
		}
	}

	$lastId = $con->lastInsertId();

	if($tipo == "SEDE"){

		$sql_dias_gratis = $con->query("SELECT qtd_dias_gratis FROM tb_configuracoes WHERE tb_configuracoes.ativo = 1");
		$result_dias_gratis = $sql_dias_gratis->fetchAll();

		$qtd_dias_gratis = $result_dias_gratis[0]['qtd_dias_gratis'];

		$str = "INSERT INTO tb_assinatura (

		cod_sede, 
		data_inicio,
		qtd_dias_gratis,
		ativo,
		gratis

		) 

		VALUE (

		$lastId, 
		DATE(NOW()),
		$qtd_dias_gratis,
		1,
		1

	)";

	$sql = $con->exec($str);

}

}

echo json_encode(array('success' => $sql_igreja, 'lastId'=> $lastId));		

$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>