<?php 


include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$obj = json_decode(file_get_contents('php://input'), true);


$pedido_de_oracao = json_decode($obj['pedido_de_oracao'], true);

$id_igreja = $obj['id_igreja'];

$desc_pedido_oracao		= $pedido_de_oracao['desc_pedido_oracao'];
$id_usuario				= $pedido_de_oracao['id_usuario'];

try{

	$con->beginTransaction();

	$str = "INSERT INTO tb_pedido_oracao (

	desc_pedido_oracao,
	cod_usuario,
	cod_igreja

	) 

	VALUE (
	'$desc_pedido_oracao',
	$id_usuario,
	$id_igreja

	)";

	// echo $str;

	$sql = $con->exec($str);

	if($sql){
		echo "deu_bom";
	}else{
		echo "deu_ruim";
	}
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>