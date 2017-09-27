<?php  


// require_once("sendEmail.php");
// require_once("sendNotificacao.php");

// include("header.php");

include("../connect/db_conect.php");

$connect = new Con();
$con = $connect->getCon();

$grupo = json_decode($_POST['grupo'], true);

$avatar = $_POST['avatar'];

$id_grupo			= $grupo['id_grupo'];
$titulo				= $grupo['titulo'];
$cod_lider			= $grupo['cod_lider'];
$tipo				= $grupo['tipo'];
$cod_igreja			= $grupo['cod_igreja'];

$participantes		= $grupo['participantes'];

$cep 					= $grupo['cep'];
$numero 				= $grupo['numero'];
$rua 					= $grupo['rua'];
$bairro 				= $grupo['bairro'];
$estado 				= $grupo['estado'];
$cidade 				= $grupo['cidade'];
$endereco 				= $grupo['endereco'];


$aux = "";

if($avatar != "default.png"){
	$aux = " capa = '$avatar', ";
}

try{

	$con->beginTransaction();

	$con->exec("DELETE FROM tb_grupo_participante WHERE cod_grupo = $id_grupo");	
	
	for ($i=0; $i < COUNT($participantes); $i++) { 

		$cod_participante = $participantes[$i];
		

		$str = "INSERT INTO tb_grupo_participante (

		cod_grupo,
		cod_participante

		) 

		VALUE (

		$id_grupo,
		$cod_participante

		)";

		$sqlParticipantes = $con->exec($str);	
	}


	$str = "UPDATE tb_grupo SET

	titulo = '$titulo',
	cod_lider = $cod_lider,
	tipo = '$tipo',
	$aux
	cod_igreja = $cod_igreja,
	cep = '$cep',
	numero = '$numero',
	rua = '$rua',
	bairro = '$bairro',
	estado = '$estado',
	cidade = '$cidade',
	endereco = '$endereco'

	WHERE id_grupo = $id_grupo";

	echo $str;

	$sql = $con->exec($str);

	$con->commit();

	if($sql || $sqlParticipantes){	

		echo "deu_bom";				
		if($avatar != "default.png"){
			if(move_uploaded_file($_FILES["file"]["tmp_name"], "../../upload/grupo/".$avatar)){
			//echo "tudo certo";
			}else{
			//echo "deu ruim";
			}
		}

		// SendNotificacao::sendNotificacaoNovoPai($id_usuario, $id_filho);
	}else{
		echo "deu_ruim";		
	}
	

}catch(Exception $e){
	$con->rollback();
}


?>