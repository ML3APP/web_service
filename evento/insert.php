<?php 


include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();


$evento = json_decode($_POST['evento'], true);

$avatar = $_POST['avatar'];
$id_igreja = $_POST['id_igreja'];

$titulo 				= $evento['titulo'];
$desc_evento 			= $evento['desc_evento'];
$valor 					= $evento['valor'];
$data 					= $evento['data'];
$hora 					= $evento['hora'];
$localizacao 			= $evento['localizacao'];
$capacidade 			= $evento['capacidade'];

$pago 					= $evento['pago'];


$cep 					= $evento['cep'];
$numero 				= $evento['numero'];
$rua 					= $evento['rua'];
$bairro 				= $evento['bairro'];
$estado 				= $evento['estado'];
$cidade 				= $evento['cidade'];
$endereco 				= $evento['endereco'];
$em_destaque 			= $evento['em_destaque'];



$codigo_evento = strtoupper(substr(uniqid(), -6, -1));

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

if(empty($localizacao)){
	$localizacao = "{}";
}else{
	$localizacao = json_encode($localizacao);
}

try{

	$con->beginTransaction();


	$str = "INSERT INTO tb_evento (

	titulo ,
	desc_evento ,
	valor ,
	data ,
	hora ,
	localizacao,
	cod_igreja,
	capa,
	capacidade,
	pago,
	cep,
	numero,
	rua,
	bairro,
	estado,
	cidade,
	endereco,
	em_destaque
	) 

	VALUE (

	'$titulo',
	'$desc_evento',
	$valor,
	'$data',
	'$hora',
	'$localizacao',
	$id_igreja,
	'$avatar',
	$capacidade,
	$pago,
	'$cep',
	'$numero',
	'$rua',
	'$bairro',
	'$estado',
	'$cidade',
	'$endereco',
	$em_destaque

	)";

	// echo $str;

	$sql_evento = $con->exec($str);	

	if($sql_evento){

		echo "deu_bom";

		if($avatar != "default.png"){
			if(move_uploaded_file($_FILES["file"]["tmp_name"], "../../upload/evento/".$avatar)){
			//echo "tudo certo";
			}else{
			//echo "deu ruim";
			}
		}

		$lastId = $con->lastInsertId();

	}else{
		echo "deu_ruim";
	}


	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>