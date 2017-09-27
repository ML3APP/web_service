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
$dt_atividade			= $nova_mensagem['dt_atividade'];

$atividade			= $nova_mensagem['atividade'];

if(empty($atividade)){
	$atividade = 0;
}

try{

	$con->beginTransaction();

	$str = "INSERT INTO tb_post (

	mensagem,
	cod_igreja,
	cod_grupo,
	cod_usuario,
	atividade,
	dt_atividade,
	anexo
	
	) 

	VALUE (

	'$mensagem',
	$cod_igreja,
	$cod_grupo,
	$cod_usuario,
	$atividade,
	'$dt_atividade',
	'$anexo'

	)";

	echo $str;

	$sql = $con->exec($str);	

	if($sql){	

		if($anexo != "default.png"){
			if(move_uploaded_file($_FILES["file"]["tmp_name"], "../../upload/grupo/".$anexo)){
			//echo "tudo certo";
			}else{
			//echo "deu ruim";
			}
		}

		echo "deu_bom";


		// SendEmail::sendEmailNovogrupo($lastId);
		// SendNotificacao::sendNotificacaoNovoPai($id_grupo, $id_filho);
	}else{
		echo "deu_ruim";		
	}

	$con->commit();


}catch(Exception $e){
	$con->rollback();
}

?>