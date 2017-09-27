<?php 


include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$obj = json_decode(file_get_contents('php://input'), true);

$parcela = json_decode($obj['parcela'], true);

$foi_pago = $parcela['foi_pago'];
$id_parcela = $parcela['id_parcela'];
$excluido = $parcela['excluido'];
$repetir = $parcela['repetir'];

$tipo_excluir = $parcela['tipo_excluir'];

if(empty($excluido)){
	$excluido = 0;
}

$id_lancamento = $parcela['id_lancamento'];
$valor = $parcela['valor'];
$dt_parcela = $parcela['dt_parcela'];

$aux = "";

if($foi_pago){
	$aux = ", dt_pagamento = NOW()";
}

try{

	$con->beginTransaction();


	if(empty($id_parcela)){

		$str = "INSERT INTO tb_parcela (

		cod_lancamento,
		foi_pago,
		num_parcela,
		valor_parcela,
		dt_parcela,
		dt_pagamento,
		excluido

		) 

		VALUES (

		$id_lancamento, 
		$foi_pago, 
		0, 
		$valor,	
		'$dt_parcela',
		NOW(),
		$excluido

		)";

	}else{	
		$str = "UPDATE tb_parcela SET foi_pago = $foi_pago, excluido = $excluido $aux WHERE id_parcela = $id_parcela";
	}

	$sql = $con->exec($str);		

	if($sql){

		echo "deu_bom";

		// SendEmail::sendEmailDefault($nome,'Bem Vindo', $email, "Olá, ".$nome.". <br><br>Login: ". $email."<br>"."senha:". $senha);
		// SendNotificacao::sendNotificacaoNovoPai($id_usuario, $id_filho);
	}else{
		echo "deu_ruim";
	}


	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>