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

	tb_evento.*, 
	COUNT(tb_evento_participante.id_evento_participante) as qtd_participantes,
	SUM(previsto.valor_parcela) as 'previsto',
	SUM(pago.valor_parcela) as 'pago',
	(DATE(tb_evento.data) < DATE(NOW())) as ja_aconteceu

	FROM tb_evento 

	LEFT JOIN tb_lancamento ON(tb_evento.id_evento = tb_lancamento.cod_evento and tb_lancamento.tipo = 'evento') 
	LEFT JOIN tb_parcela as previsto ON(tb_lancamento.id_lancamento = previsto.cod_lancamento) 
	LEFT JOIN tb_parcela as pago ON(tb_lancamento.id_lancamento = pago.cod_lancamento and pago.foi_pago = 1) 
	LEFT JOIN tb_evento_participante ON(tb_evento_participante.cod_evento = tb_evento.id_evento and tb_evento_participante.desistiu = 0) 

	LEFT JOIN tb_igreja ON(tb_igreja.id_igreja = tb_lancamento.cod_igreja_repasse) 

	WHERE 

	tb_evento.cod_igreja = $id_igreja  and 
	tb_evento.excluido = 0 and 	
	DATE(tb_evento.data) BETWEEN DATE('$data_inicio') AND DATE('$data_termino')  

	GROUP BY tb_evento.id_evento ";

// echo $str;

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>