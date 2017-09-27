<?php 



include("../connect/db_conect.php");
include("../sendEmail.php");

$connect = new Con();
$con = $connect->getCon();

$nova_mensagem = json_decode($_POST['nova_mensagem'], true);

$anexo = $_POST['anexo'];

$mensagem				= $nova_mensagem['mensagem'];
$cod_igreja				= $nova_mensagem['cod_igreja'];
$cod_grupo				= $nova_mensagem['cod_grupo'];
$cod_usuario			= $nova_mensagem['cod_usuario'];

$atividade				= $nova_mensagem['cod_usuario'];
$cod_usuario			= $nova_mensagem['cod_usuario'];
$cod_usuario			= $nova_mensagem['cod_usuario'];


try{

	$con->beginTransaction();

	$str = "INSERT INTO tb_post (

	mensagem,
	cod_igreja,
	cod_grupo,
	cod_usuario,
	anexo
	
	) 

	VALUE (

	'$mensagem',
	$cod_igreja,
	$cod_grupo,
	$cod_usuario,
	'$anexo'

	)";

	echo $str;

	$con->commit();

	if($sql){	
		$con->beginTransaction();

		if($anexo != "default.png"){
			if(move_uploaded_file($_FILES["file"]["tmp_name"], "../../upload/grupo/".$anexo)){
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