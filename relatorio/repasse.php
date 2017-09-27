<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_igreja 				= $obj['id_igreja'];
$data_inicio 			= $obj['data_inicio'];
$data_termino 			= $obj['data_termino'];

$filtro 				= $obj['filtro'];
$status_pagamento 		= $filtro['status_pagamento'];
$entrada_saida 			= $filtro['entrada_saida'];

$where = 1;

if($status_pagamento == 'pagas'){
	$where .= " and tb_parcela.foi_pago = 1 ";
}else if($status_pagamento == 'nao_pagas'){
	$where .= " and tb_parcela.foi_pago = 0 ";
}

if($entrada_saida == 'E'){
	$where .= " and tb_lancamento.entrada_saida = 'E' ";
}else if($entrada_saida == 'S'){
	$where .= " and tb_lancamento.entrada_saida = 'S' ";
}

try{

	$con->beginTransaction();

	$str = "SELECT tb_igreja.desc_igreja,

	quem_pagou.id_usuario as qp_id_usuario, quem_pagou.avatar as qp_avatar, quem_pagou.nome as qp_nome, tb_categoria.* ,tb_lancamento.* , tb_parcela.* 

	FROM tb_lancamento 

	LEFT JOIN tb_categoria ON(tb_categoria.id_categoria = tb_lancamento.cod_categoria) 
	LEFT JOIN tb_parcela ON(tb_lancamento.id_lancamento = tb_parcela.cod_lancamento) 
	LEFT JOIN tb_usuario as quem_pagou ON(tb_lancamento.cod_quem_pagou = quem_pagou.id_usuario) 

	LEFT JOIN tb_igreja ON(tb_igreja.id_igreja = tb_lancamento.cod_igreja_repasse) 

	WHERE 

	$where and
	tb_lancamento.cod_igreja = $id_igreja  and 
	tb_lancamento.excluido = 0 and 
	tb_lancamento.tipo = 'repasse' and 
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