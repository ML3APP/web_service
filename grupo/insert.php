<?php 



include("../connect/db_conect.php");
include("../sendEmail.php");

$connect = new Con();
$con = $connect->getCon();

$grupo = json_decode($_POST['grupo'], true);

$avatar = $_POST['avatar'];

$titulo				= $grupo['titulo'];
$cod_lider			= $grupo['cod_lider'];
$tipo				= $grupo['tipo'];
$cod_igreja			= $grupo['cod_igreja'];

$cep 					= $grupo['cep'];
$numero 				= $grupo['numero'];
$rua 					= $grupo['rua'];
$bairro 				= $grupo['bairro'];
$estado 				= $grupo['estado'];
$cidade 				= $grupo['cidade'];
$endereco 				= $grupo['endereco'];

$participantes		= $grupo['participantes'];

try{

	$con->beginTransaction();

	$str = "INSERT INTO tb_grupo (

	titulo,
	cod_lider,
	tipo,
	capa,
	cod_igreja,
	cep,
	numero,
	rua,
	bairro,
	estado,
	cidade,
	endereco
	
	) 

	VALUE (

	'$titulo',
	$cod_lider,
	'$tipo',
	'$avatar',
	$cod_igreja,
	'$cep',
	'$numero',
	'$rua',
	'$bairro',
	'$estado',
	'$cidade',
	'$endereco'

	)";

	// echo $str;

	$sql = $con->exec($str);	
	$lastId = $con->lastInsertId();

	for ($i=0; $i < COUNT($participantes); $i++) { 

		$cod_participante = $participantes[$i];
		
		$str = "INSERT INTO tb_grupo_participante (

		cod_grupo,
		cod_participante

		) 

		VALUE (

		$lastId,
		$cod_participante

		)";

		$sql = $con->exec($str);	
	}
	
	$con->commit();

	if($sql){	
		$con->beginTransaction();

		if($avatar != "default.png"){
			if(move_uploaded_file($_FILES["file"]["tmp_name"], "../../upload/grupo/".$avatar)){
			//echo "tudo certo";
			}else{
			//echo "deu ruim";
			}
		}

		echo "deu_bom";

		$con->commit();

		// SendEmail::sendEmailNovogrupo($lastId);
		// SendNotificacao::sendNotificacaoNovoPai($id_grupo, $id_filho);
	}else{
		echo "deu_ruim";		
	}

}catch(Exception $e){
	$con->rollback();
}

?>