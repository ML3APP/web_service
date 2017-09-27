<?php 

require_once("../sendEmail.php");
include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$json = file_get_contents('php://input');
$obj = json_decode($json, true);

$email = $obj['email'];

try{
	$con->beginTransaction();

	$sql = $con->query("SELECT nome, senha FROM tb_usuario WHERE email = '$email'"); 
	$result = $sql->fetchAll();

	if(COUNT($result)>0){

		$nome_usuario = $result[0]['nome'];
		$senha = $result[0]['senha'];

		SendEmail::sendEmailDefault($email, "ML3 - Recuperar Senha", "Sua senha para Login no ML3 é: ".$senha);

		echo 1;

	}else{
		echo 2;
	}

	$con->commit();

}catch(Exception $e){
	$con->rollback();
}


?>