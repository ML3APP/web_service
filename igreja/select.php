<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();


$id_igreja = $obj["id_igreja"];
$minha_igreja = $obj["minha_igreja"];
$cod_sede = $obj["cod_sede"];
$id_sede_congregacoes = $obj["id_sede_congregacoes"];

$financeiro = $obj["financeiro"];
$membros = $obj["membros"];

$where = 1;

$sql_membros = "";
$sql_minha_igreja = "";

if($minha_igreja){

	$sql_minha_igreja = " 
	
	,(
	SELECT COUNT(tb_congregacoes.id_igreja) FROM tb_igreja as tb_congregacoes WHERE tb_congregacoes.cod_sede = $id_igreja and tb_congregacoes.excluido = 0

	) as qtd_congregacoes


	";
}


if($membros){

	$sql_membros = " 
	
	,(
	SELECT COUNT(us_membro.id_usuario) FROM tb_usuario as us_membro WHERE us_membro.cod_perfil = 2 and tb_igreja.id_igreja = us_membro.cod_igreja and 

	MONTH(us_membro.dt_cadastro) = MONTH(NOW())
	and 
	YEAR(us_membro.dt_cadastro) = YEAR(NOW())

	and us_membro.excluido = 0

	) as qtd_membros_mes_atual,

	(
	SELECT COUNT(us_membro.id_usuario) FROM tb_usuario as us_membro WHERE us_membro.cod_perfil = 2 and tb_igreja.id_igreja = us_membro.cod_igreja 

	and MONTH(us_membro.dt_cadastro) = (MONTH(NOW()) - 1 ) and
	YEAR(us_membro.dt_cadastro) = YEAR(NOW())

	and us_membro.excluido = 0


	) as qtd_membros_mes_anterior,

	(
	SELECT COUNT(us_membro.id_usuario) FROM tb_usuario as us_membro WHERE us_membro.cod_perfil = 2 and tb_igreja.id_igreja = us_membro.cod_igreja

	) as qtd_membros_total


	";
}

$sql_financeiro = "";

if($financeiro){

	$sql_financeiro .= " 
	
	,(
	SELECT SUM(tb_parcela.valor_parcela) FROM tb_parcela LEFT JOIN tb_lancamento ON(tb_lancamento.id_lancamento = tb_parcela.cod_lancamento) WHERE tb_igreja.id_igreja = tb_lancamento.cod_igreja and tb_lancamento.entrada_saida = 'E' and tb_parcela.foi_pago = 1 and DATE(tb_parcela.dt_pagamento) <= DATE(NOW()) and
	tb_parcela.excluido = 0
	) as vl_entradas,

	(
	SELECT SUM(tb_parcela.valor_parcela) FROM tb_parcela LEFT JOIN tb_lancamento ON(tb_lancamento.id_lancamento = tb_parcela.cod_lancamento) WHERE tb_igreja.id_igreja = tb_lancamento.cod_igreja and tb_lancamento.entrada_saida = 'S' and tb_parcela.foi_pago = 1 and DATE(tb_parcela.dt_pagamento) <= DATE(NOW()) and
	tb_parcela.excluido = 0
	) as vl_saidas,

	(
	SELECT SUM(tb_parcela.valor_parcela) FROM tb_igreja as tb_congreg LEFT JOIN tb_lancamento ON(tb_lancamento.cod_igreja = tb_congreg.id_igreja)  LEFT JOIN tb_parcela ON (tb_parcela.cod_lancamento = tb_lancamento.id_lancamento) WHERE tb_congreg.cod_sede = tb_igreja.id_igreja and tb_lancamento.entrada_saida = 'E' and tb_parcela.foi_pago = 1 and DATE(tb_parcela.dt_pagamento) <= DATE(NOW()) and
	tb_parcela.excluido = 0
	) as vl_entradas_congregacoes,

	(
	SELECT SUM(tb_parcela.valor_parcela) FROM tb_igreja as tb_congreg LEFT JOIN tb_lancamento ON(tb_lancamento.cod_igreja = tb_congreg.id_igreja)  LEFT JOIN tb_parcela ON (tb_parcela.cod_lancamento = tb_lancamento.id_lancamento) WHERE tb_congreg.cod_sede = tb_igreja.id_igreja and tb_lancamento.entrada_saida = 'S' and tb_parcela.foi_pago = 1 and DATE(tb_parcela.dt_pagamento) <= DATE(NOW()) and
	tb_parcela.excluido = 0
	) as vl_saidas_congregacoes,

	(
	SELECT SUM(tb_parcela.valor_parcela) FROM tb_parcela LEFT JOIN tb_lancamento ON(tb_lancamento.id_lancamento = tb_parcela.cod_lancamento) WHERE tb_igreja.id_igreja = tb_lancamento.cod_igreja and tb_lancamento.entrada_saida = 'S' and tb_parcela.foi_pago = 1 and 

	MONTH(tb_parcela.dt_pagamento) = MONTH(NOW()) and YEAR(tb_parcela.dt_pagamento) = YEAR(NOW()) 
	
	and	tb_parcela.excluido = 0

	) as vl_saidas_mes

	,(
	SELECT SUM(tb_parcela.valor_parcela) FROM tb_parcela LEFT JOIN tb_lancamento ON(tb_lancamento.id_lancamento = tb_parcela.cod_lancamento) WHERE tb_igreja.id_igreja = tb_lancamento.cod_igreja and tb_lancamento.entrada_saida = 'E' and tb_parcela.foi_pago = 1 and 

	MONTH(tb_parcela.dt_pagamento) = MONTH(NOW()) and YEAR(tb_parcela.dt_pagamento) = YEAR(NOW()) 

	and	tb_parcela.excluido = 0

	) as vl_entradas_mes

	";
}

if($cod_sede > 0){
	$where .= " and tb_igreja.cod_sede = $cod_sede ";
}

if($id_igreja > 0){
	$where .= " and tb_igreja.id_igreja = $id_igreja ";
}

if(!empty($id_sede_congregacoes)){
	$where .= " and (tb_igreja.id_igreja = $id_sede_congregacoes or tb_igreja.cod_sede = $id_sede_congregacoes) ";
}

try{

	$con->beginTransaction();

	$str = "SELECT 

	tb_pr.nome as pr_nome,
	tb_pr.avatar as pr_avatar,
	tb_pr.email as pr_email,
	tb_pr.telefone as pr_telefone,
	tb_pr.id_usuario as pr_id_usuario,

	tb_igreja.*

	$sql_financeiro 
	$sql_membros 
	$sql_minha_igreja

	FROM tb_igreja 

	LEFT JOIN tb_usuario as tb_pr ON(tb_igreja.cod_pastor_presidente = tb_pr.id_usuario) 

	WHERE $where and tb_igreja.excluido = 0 GROUP BY tb_igreja.id_igreja";

	// echo $str;

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>