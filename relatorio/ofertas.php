<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_igreja 				= $obj['id_igreja'];
$data_inicio 			= $obj['data_inicio'];
$data_termino 			= $obj['data_termino'];

try{

	$con->beginTransaction();

	$str = "SELECT tb_igreja.desc_igreja,

	quem_pagou.id_usuario as qp_id_usuario, 
	quem_pagou.avatar as qp_avatar, 
	quem_pagou.nome as qp_nome, 
	tb_categoria.*,
	tb_lancamento.*, 
	tb_parcela.* 

	FROM tb_lancamento 

	LEFT JOIN tb_categoria ON(tb_categoria.id_categoria = tb_lancamento.cod_categoria) 
	LEFT JOIN tb_parcela ON(tb_lancamento.id_lancamento = tb_parcela.cod_lancamento) 
	LEFT JOIN tb_usuario as quem_pagou ON(tb_lancamento.cod_quem_pagou = quem_pagou.id_usuario) 

	LEFT JOIN tb_igreja ON(tb_igreja.id_igreja = tb_lancamento.cod_igreja_repasse) 

	WHERE 

	tb_lancamento.cod_igreja = $id_igreja  and 
	tb_lancamento.excluido = 0 and 
	tb_lancamento.tipo = 'oferta' and 
	DATE(tb_lancamento.dt_lancamento) BETWEEN DATE('$data_inicio') AND DATE('$data_termino')   ";

// echo $str;

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>