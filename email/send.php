<?php 


$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
include("../sendEmail.php");

$para = $obj['para'];
$titulo = $obj['titulo'];
$descricao = $obj['descricao'];

try{
	SendEmail::sendEmailDefault($para, $titulo, $descricao);
}catch(Exception $e){
	$con->rollback();
}

?>