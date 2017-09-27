<?php  


// require_once("sendEmail.php");
// require_once("sendNotificacao.php");

// include("header.php");

include("../connect/db_conect.php");

$connect = new Con();
$con = $connect->getCon();

$obj = json_decode(file_get_contents('php://input'), true);


$igreja = json_decode($obj['igreja'], true);

$id_igreja = $igreja['id_igreja'];
$localizacao = $igreja['localizacao'];

$localizacao = json_encode($localizacao);

if(empty($cod_denominacao)){
	$cod_denominacao = 0;
}

if(empty($cod_sede)){
	$cod_sede = 0;
}


try{

	$con->beginTransaction();


	$str = "UPDATE tb_igreja SET

	localizacao = '$localizacao'

	WHERE id_igreja = $id_igreja";

	echo $str;

	$sql = $con->exec($str);

	$con->commit();

	if($sql){	
		echo "deu_bom";				
		// SendNotificacao::sendNotificacaoNovoPai($id_usuario, $id_filho);
	}else{
		echo "deu_ruim";		
	}
	

}catch(Exception $e){
	$con->rollback();
}


?>