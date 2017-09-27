<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_assinatura = $obj["id_assinatura"];
$id_igreja = $obj["id_igreja"];

$where = "";

if(!empty($id_assinatura)){
	$where .= " and tb_assinatura.id_assinatura = $id_assinatura ";
}

try{

	$con->beginTransaction();


		$str = "SELECT *

		FROM tb_assinatura 

		WHERE 

		tb_assinatura.cod_sede = $id_igreja and 
		tb_assinatura.ativo = 1";	

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>