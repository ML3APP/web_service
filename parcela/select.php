<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();


$id_igreja = $obj["id_igreja"];
$data = $obj["data"];

try{

	$con->beginTransaction();

	$sql = $con->query("SELECT tb_categoria.* ,tb_lancamento.* , tb_parcela.* 

		FROM tb_lancamento 

		INNER JOIN tb_categoria ON(tb_categoria.id_categoria = tb_lancamento.cod_categoria) 
		LEFT JOIN tb_parcela ON(tb_lancamento.id_lancamento = tb_parcela.cod_lancamento) 

		WHERE tb_lancamento.cod_igreja = $id_igreja and MONTH(dt_parcela) = MONTH('$data') and YEAR(dt_parcela) = YEAR('$data')");

	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>