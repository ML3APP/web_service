<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$token = md5(uniqid(true));
$id_usuario = $obj['id_usuario'];
$valor = $obj['valor'];
$tipo = $obj['tipo'];
$cod_igreja = $obj['cod_igreja'];


try{

	$con->beginTransaction();

	$sql = $con->exec("DELETE FROM tb_token_pagseguro WHERE cod_usuario = $id_usuario");
	$sql = $con->exec("INSERT INTO tb_token_pagseguro (cod_usuario, token, valor, tipo, cod_igreja) VALUES ($id_usuario, '$token', '$valor', '$tipo', $cod_igreja)");

	if($sql){
		echo "$token";
	}else{
		echo "deu_ruim";
	}

	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>