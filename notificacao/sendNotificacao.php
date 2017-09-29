<?php  

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
require_once("../sendNotificacao.php");
$connect = new Con();
$con = $connect->getCon();

$id_igreja = $obj['id_igreja'];
$titulo = $obj['titulo'];
$tipo = $obj['tipo'];
$id_de = $obj['id_de'];
$mensagem = $obj['mensagem'];

try{

	$con->beginTransaction();

	$sql = $con->query("SELECT * FROM tb_usuario WHERE cod_igreja = $id_igreja and excluido = 0");
	$result = $sql->fetchAll();

	$con->commit();

}catch(Exception $e){

	$con->rollback();

}

for ($i=0; $i < COUNT($result); $i++) { 
	SendNotificacao::sendNotificacaoAviso($result[$i]['id_usuario'], $mensagem, $id_de, $tipo, 0, $titulo );
}

?>