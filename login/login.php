<?php  

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();


$usuario = json_decode($obj['usuario'], true);

$login = $usuario['login'];
$senha = $usuario['senha'];


try{

	$con->beginTransaction();

	$str = "SELECT tb_igreja.*, tb_perfil.*, tb_usuario.*

	FROM tb_usuario 

	INNER JOIN tb_perfil ON (tb_usuario.cod_perfil = tb_perfil.id_perfil) 
	INNER JOIN tb_igreja ON (tb_usuario.cod_igreja = tb_igreja.id_igreja) 

	Where (tb_usuario.email = '$login' or tb_usuario.cpf = '$login') and tb_usuario.senha = '$senha'";

	// echo $str;

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);


}catch(Exception $e){
	$con->rollback();
}


?>