<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$code_transacao = $obj['code_transacao'];

try{

	$con->beginTransaction();

	$sql = $con->query("SELECT * FROM tb_historico_status WHERE code_transacao = '$code_transacao'");
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>