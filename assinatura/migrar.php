<?php  


// require_once("sendEmail.php");
// require_once("sendNotificacao.php");

// include("header.php");

include("../connect/db_conect.php");

$connect = new Con();
$con = $connect->getCon();

$obj = json_decode(file_get_contents('php://input'), true);

$assinatura = $obj['assinatura'];

$qtd_congregacao = $assinatura['qtd_congregacao'];
$valor_sede = $assinatura['valor_sede'];
$valor_congregacao = $assinaturaobj['valor_congregacao'];
$valor_total = $assinatura['valor_total'];

$id_sede = $obj['id_sede'];


try{

	$con->beginTransaction();


	$sql_assinatura = $con->exec("INSERT INTO tb_assinatura (cod_sede, ativo, data_inicio, qtd_congregacao, gratis,valor_sede,valor_congregacao,valor_total) VALUES ($id_sede, 1, NOW(), $qtd_congregacao, 0, '$valor_sede', '$valor_congregacao','$valor_total')");
	$lastId = $con->lastInsertId();

	if($sql_assinatura){

		$sql = $con->exec("UPDATE tb_assinatura SET	ativo = 0, data_termino = NOW()	WHERE cod_sede = $id_sede and tb_assinatura.id_assinatura != $lastId");
		
		if($sql){	
			$sql_mensalidade = $con->exec("INSERT INTO tb_mensalidade (data_vencimento, valor, status_pagamento, cod_assinatura, cod_sede) VALUES (NOW(), '$valor_total', 0, $lastId, $id_sede )");
			echo "deu_bom";				
		}else{
			echo "deu_ruim";		
		}
	}else{
		echo "deu_ruim";		
	}

	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}


?>