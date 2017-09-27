<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_igreja 				= $obj['id_igreja'];
$data_inicio 			= $obj['data_inicio'];
$data_termino 			= $obj['data_termino'];

$filtro 				= $obj['filtro'];

$dizimistas 		= $filtro['dizimistas'];

$where = 1;

if($dizimistas == 'dizimistas'){
	$where .= " and tb_lancamento.id_lancamento > 0 ";
}else if($dizimistas == 'nao_dizimistas'){
	$where .= " and tb_lancamento.id_lancamento IS NULL ";
}

try{

	$con->beginTransaction();

	$str = "SELECT 

	tb_usuario.*,
	SUM(tb_parcela.valor_parcela) as valor_dizimo,
	COUNT(tb_lancamento.id_lancamento) as qtd_dizimou


	FROM tb_usuario 

	LEFT JOIN tb_lancamento ON(tb_usuario.id_usuario = tb_lancamento.cod_quem_pagou and tb_lancamento.excluido = 0 and tb_lancamento.tipo = 'dízimo' and DATE(tb_lancamento.dt_lancamento) BETWEEN DATE('$data_inicio') AND DATE('$data_termino')) 
	LEFT JOIN tb_parcela ON(tb_lancamento.id_lancamento = tb_parcela.cod_lancamento) 

	WHERE 

	$where and
	tb_usuario.cod_igreja = $id_igreja  and 
	tb_usuario.excluido = 0 
	 GROUP BY tb_usuario.id_usuario ";

// echo $str;

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>