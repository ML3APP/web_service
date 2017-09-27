<?php

include("connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();


try{

	$con->beginTransaction();

	$sql = $con->query("SELECT * FROM tb_configuracoes WHERE ativo = 1");
	$result = $sql->fetchAll();

	$configuracoes = $result[0];

	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

// header("access-control-allow-origin: https://sandbox.pagseguro.uol.com.br");
header("access-control-allow-origin: https://pagseguro.uol.com.br");

if(isset($_POST['notificationType']) && $_POST['notificationType'] == 'transaction'){

	$email = $configuracoes['email_pagseguro'];
	$token = $configuracoes['token_pagseguro'];

	$notificationCode = $_POST['notificationCode'];

	// $url = 'https://ws.pagseguro.uol.com.br/v2/transactions/notifications/' . $notificationCode . '?email=' . $email . '&token=' . $token;
	$url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions/notifications/' . $notificationCode . '?email=' . $email . '&token=' . $token;

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$transaction= curl_exec($curl);
	curl_close($curl);

	if($transaction == 'Unauthorized'){
			//Insira seu código avisando que o sistema está com problemas, sugiro enviar um e-mail avisando para alguém fazer a manutenção
		echo "Error";
			exit;//Mantenha essa linha
		}

		$transaction = simplexml_load_string($transaction);
		$obj_resposta = json_encode($transaction);

		$code_transacao = $transaction->code;
		$status_pagamento = $transaction->status;


		try{

			$con->beginTransaction();

			$sql = $con->exec("INSERT INTO tb_historico_status  (code_transacao, obj_resposta,data_historico_status) VALUES ( '$code_transacao', '$obj_resposta', NOW())");


			$con->exec("UPDATE tb_mensalidade SET status_pagamento = $status_pagamento WHERE code_transacao = '$code_transacao'");
			$con->exec("UPDATE tb_evento_participante SET status_pagamento = $status_pagamento WHERE code_transacao = '$code_transacao'");

			$foi_pago = 0;
			if($status_pagamento == 3){
				
				$foi_pago = 1;

				$where_dt_pagamento = " , dt_pagamento = NOW() ";
				$con->exec("UPDATE tb_mensalidade SET data_pagamento = NOW() WHERE code_transacao = '$code_transacao'");

			}

			$con->exec("UPDATE tb_parcela SET foi_pago = $foi_pago, status_pagamento = $status_pagamento $where_dt_pagamento  WHERE code_transacao = '$code_transacao'");

			if($sql){
				echo "deu_bom";
			}else{
				echo "deu_ruim";
			}

			$con->commit();

		}catch(Exception $e){
			$con->rollback();
		}

	}else{
		$sql = $con->exec("INSERT INTO tb_cartao  (card) VALUES ('foi_nao')");
	}

	?>