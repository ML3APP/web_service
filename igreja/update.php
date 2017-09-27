<?php  


// require_once("sendEmail.php");
// require_once("sendNotificacao.php");

// include("header.php");

include("../connect/db_conect.php");

$connect = new Con();
$con = $connect->getCon();

$igreja = json_decode($_POST['igreja'], true);

$avatar = $_POST['avatar'];

$id_igreja 			= $igreja['id_igreja'];
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
$numero 			= $igreja['numero'];
$tipo_pessoa 			= $igreja['tipo_pessoa'];

$rua 				= $igreja['rua'];
$banco 				= $igreja['banco'];
$tipo_conta 		= $igreja['tipo_conta'];
$conta 				= $igreja['conta'];
$agencia 			= $igreja['agencia'];
$dados_bancarios 	= $igreja['dados_bancarios'];
$redes_sociais 		= json_encode($igreja['redes_sociais']);
$excluido 			= 0;

$email_pagseguro 	= $igreja['email_pagseguro'];
$token_pagseguro 	= $igreja['token_pagseguro'];

$cod_pastor_presidente 	= $igreja['cod_pastor_presidente'];

$localizacao 		= $igreja['localizacao'];

$repasse_pr 				= $igreja['repasse_pr'];
$tipo_repasse_pr 			= $igreja['tipo_repasse_pr'];
$valor_repasse_pr 			= $igreja['valor_repasse_pr'];
$porcentagem_repasse_pr 	= $igreja['porcentagem_repasse_pr'];
$repasse_sede 				= $igreja['repasse_sede'];
$tipo_repasse_sede 			= $igreja['tipo_repasse_sede'];
$valor_repasse_sede 		= $igreja['valor_repasse_sede'];
$porcentagem_repasse_sede 	= $igreja['porcentagem_repasse_sede'];


if(empty($repasse_pr)){					$repasse_pr = 0;}
if(empty($tipo_repasse_pr)){			$tipo_repasse_pr = '';}
if(empty($valor_repasse_pr)){			$valor_repasse_pr = 0;}
if(empty($porcentagem_repasse_pr)){		$porcentagem_repasse_pr = 0;}
if(empty($repasse_sede)){				$repasse_sede = 0;}
if(empty($tipo_repasse_sede)){			$tipo_repasse_sede = '';}
if(empty($valor_repasse_sede)){			$valor_repasse_sede = 0;}
if(empty($porcentagem_repasse_sede)){	$porcentagem_repasse_sede = 0;}


if(empty($localizacao)){
	$localizacao = "{}";
}else{
	$localizacao = json_encode($localizacao);
}


if($dados_bancarios){
	$dados_bancarios = 1;	
}else{
	$dados_bancarios = 0;		
}

if(empty($cod_denominacao)){
	$cod_denominacao = 0;
}

if(empty($cod_sede)){
	$cod_sede = 0;
}

if(empty($redes_sociais)){
	$redes_sociais = "{}";
}


try{

	$con->beginTransaction();


	$str = "UPDATE tb_igreja SET

	desc_igreja					= '$desc_igreja',  
	telefone					= '$telefone',  
	endereco					= '$endereco',  
	cidade						= '$cidade',  
	estado						= '$estado',  
	cep						 	= '$cep',  
	email						= '$email',  
	cpf						 	= '$cpf',  
	cnpj						= '$cnpj',  
	bairro						= '$bairro',  
	rua						 	= '$rua',  
	logomarca					= '$avatar',  
	tipo						= '$tipo',  
	cod_denominacao				= $cod_denominacao,  
	dt_abertura					= '$dt_abertura',  
	cod_sede					= $cod_sede, 
	excluido					= $excluido,
	descricao_igreja			= '$descricao_igreja',

	banco						= '$banco',
	tipo_conta					= '$tipo_conta',
	conta						= '$conta',
	agencia						= '$agencia',
	excluido					= '$excluido',
	redes_sociais				= '$redes_sociais',
	dados_bancarios				= $dados_bancarios,

	email_pagseguro				= '$email_pagseguro',
	token_pagseguro				= '$token_pagseguro',
	localizacao					= '$localizacao',
	numero						= '$numero',
	tipo_pessoa					= '$tipo_pessoa',
	cod_pastor_presidente		= $cod_pastor_presidente,

	repasse_pr					= $repasse_pr,
	tipo_repasse_pr				= '$tipo_repasse_pr',
	valor_repasse_pr			= '$valor_repasse_pr',
	porcentagem_repasse_pr		= $porcentagem_repasse_pr,
	repasse_sede				= $repasse_sede,
	tipo_repasse_sede			= '$tipo_repasse_sede',
	valor_repasse_sede			= '$valor_repasse_sede',
	porcentagem_repasse_sede	= $porcentagem_repasse_sede

	WHERE id_igreja = $id_igreja";

	echo $str;

	$sql = $con->exec($str);

	$con->commit();

	if($sql){	

		if($avatar != "default.png"){
			if(move_uploaded_file($_FILES["file"]["tmp_name"], "../../upload/img/igreja/".$avatar)){
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