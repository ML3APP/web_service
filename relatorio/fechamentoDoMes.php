<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$id_igreja 				= $obj['id_igreja'];

$filtro 				= $obj['filtro'];
$ano_selecionado 		= $filtro['ano_selecionado'];

$where = 1;

try{

	$con->beginTransaction();

	$str = "

	SELECT 

	tb_i.id_igreja as ig_id_igreja,
	tb_i.cod_sede as ig_cod_sede,
	tb_i.repasse_pr as ig_repasse_pr,
	tb_i.tipo_repasse_pr as ig_tipo_repasse_pr,
	tb_i.valor_repasse_pr as ig_valor_repasse_pr,
	tb_i.porcentagem_repasse_pr as ig_porcentagem_repasse_pr,
	tb_i.repasse_sede as ig_repasse_sede,
	tb_i.tipo_repasse_sede as ig_tipo_repasse_sede,
	tb_i.valor_repasse_sede as ig_valor_repasse_sede,
	tb_i.porcentagem_repasse_sede as ig_porcentagem_repasse_sede,
	tb_f.*,

	(SELECT SUM(tb_parcela.valor_parcela) 

	FROM tb_lancamento 
	LEFT JOIN tb_parcela ON(tb_lancamento.id_lancamento = tb_parcela.cod_lancamento) 
	WHERE 
	tb_lancamento.cod_igreja = $id_igreja  and 
	tb_lancamento.excluido = 0 and 
	tb_parcela.foi_pago = 1 and 
	YEAR(tb_parcela.dt_pagamento) = $ano_selecionado and
	tb_lancamento.entrada_saida = 'S'and
	YEAR(tb_p.dt_pagamento) = YEAR(tb_parcela.dt_pagamento) and
	MONTH(tb_p.dt_pagamento) = MONTH(tb_parcela.dt_pagamento)
	GROUP BY YEAR(tb_parcela.dt_pagamento), MONTH(tb_parcela.dt_pagamento)) as saidas_pendente,



	(SELECT SUM(tb_parcela.valor_parcela) 
	FROM tb_lancamento 
	LEFT JOIN tb_parcela ON(tb_lancamento.id_lancamento = tb_parcela.cod_lancamento) 
	WHERE 
	tb_lancamento.cod_igreja = $id_igreja  and 
	tb_lancamento.excluido = 0 and 
	tb_parcela.foi_pago = 1 and 
	YEAR(tb_parcela.dt_pagamento) = $ano_selecionado and
	tb_lancamento.entrada_saida = 'E'	and
	YEAR(tb_p.dt_pagamento) = YEAR(tb_parcela.dt_pagamento) and
	MONTH(tb_p.dt_pagamento) = MONTH(tb_parcela.dt_pagamento)
	GROUP BY YEAR(tb_parcela.dt_pagamento), MONTH(tb_parcela.dt_pagamento)) as entradas_pendente,

	MONTH(tb_p.dt_pagamento) as mes,
	YEAR(tb_p.dt_pagamento) as ano
	
	FROM tb_lancamento as tb_l

	LEFT JOIN tb_parcela as tb_p ON(tb_l.id_lancamento = tb_p.cod_lancamento) 
	LEFT JOIN tb_fechamento_mes as tb_f ON(MONTH(tb_p.dt_pagamento) = MONTH(tb_f.data) and YEAR(tb_f.data) = $ano_selecionado and tb_f.cod_igreja = $id_igreja) 
	INNER JOIN tb_igreja as tb_i ON(tb_i.id_igreja = tb_l.cod_igreja) 

	WHERE 

	tb_l.cod_igreja = $id_igreja  and 
	tb_l.excluido = 0 and 
	tb_p.foi_pago = 1 and 
	YEAR(tb_p.dt_pagamento) = $ano_selecionado

	GROUP BY MONTH(tb_p.dt_pagamento) ORDER BY mes ASC

	";

// echo $str;

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>