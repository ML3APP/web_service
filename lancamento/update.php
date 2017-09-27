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
$excluido 			= 0;

if(empty($cod_denominacao)){
	$cod_denominacao = 0;
}

if(empty($cod_sede)){
	$cod_sede = 0;
}


try{

	$con->beginTransaction();


	$str = "UPDATE tb_usuario SET

	desc_igreja = '$desc_igreja',  
	telefone = '$telefone',  
	endereco = '$endereco',  
	cidade = '$cidade',  
	estado = '$estado',  
	cep = '$cep',  
	email = '$email',  
	cpf = '$cpf',  
	cnpj = '$cnpj',  
	bairro = '$bairro',  
	logomarca = '$avatar',  
	tipo = '$tipo',  
	cod_denominacao = $cod_denominacao,  
	dt_abertura = '$dt_abertura',  
	cod_sede = $cod_sede, 
	excluido = $excluido,
	descricao_igreja = '$descricao_igreja'

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