<?php  


// require_once("sendEmail.php");
// require_once("sendNotificacao.php");

// include("header.php");

include("../connect/db_conect.php");

$connect = new Con();
$con = $connect->getCon();

$evento = json_decode($_POST['evento'], true);

$avatar = $_POST['avatar'];
$id_igreja = $_POST['id_igreja'];

$id_evento 				= $evento['id_evento'];
$titulo 				= $evento['titulo'];
$desc_evento 			= $evento['desc_evento'];
$valor 					= $evento['valor'];
$data 					= $evento['data'];
$hora 					= $evento['hora'];
$localizacao 			= $evento['localizacao'];
$capacidade 			= $evento['capacidade'];
$pago 					= $evento['pago'];
$em_destaque 			= $evento['em_destaque'];

$cep 					= $evento['cep'];
$numero 				= $evento['numero'];
$rua 					= $evento['rua'];
$bairro 				= $evento['bairro'];
$estado 				= $evento['estado'];
$cidade 				= $evento['cidade'];
$endereco 				= $evento['endereco'];

if(empty($localizacao)){
	$localizacao = "{}";
}else{
	$localizacao = json_encode($localizacao);
}

if($em_destaque){
	$em_destaque = 1;
}else{
	$em_destaque = 0;
}


if(empty($capacidade)){
	$capacidade = 0;
}

if(empty($valor)){
	$valor = 0;
}

if(empty($pago)){
	$pago = 0;
}


try{

	$con->beginTransaction();


	$str = "UPDATE tb_evento SET

	titulo = '$titulo' ,
	desc_evento = '$desc_evento' ,
	valor = $valor ,
	endereco = '$endereco' ,
	bairro = '$bairro' ,
	estado = '$estado' ,
	cidade = '$cidade' ,
	data = '$data' ,
	hora = '$hora' ,
	localizacao = '$localizacao',
	capa = '$avatar',
	capacidade = '$capacidade',
	pago = $pago,
	cep = '$cep',
	numero = '$numero',
	rua = '$rua',
	em_destaque = $em_destaque

	WHERE id_evento = $id_evento";

	echo $str;

	$sql = $con->exec($str);

	$con->commit();

	if($sql){	

		if($avatar != "default.png"){
			if(move_uploaded_file($_FILES["file"]["tmp_name"], "../../upload/evento/".$avatar)){
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