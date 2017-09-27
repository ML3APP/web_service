<?php

ini_set('max_execution_time', 300);

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");


$checkout = json_decode($obj['checkout'], true);

$img_bandeira       = $checkout['img_bandeira'];
$tipo_pagamento     = $checkout['tipo_pagamento'];
$cartao             = $checkout['cartao'];
$cvv                = $checkout['cvv'];
$validade           = $checkout['validade'];
$senderName         = $checkout['senderName'];
$senderAreaCode     = $checkout['senderAreaCode'];
$senderPhone        = $checkout['senderPhone'];
$senderEmail        = $checkout['senderEmail'];
$senderCPF          = $checkout['senderCPF'];
$endereco_comprador = $checkout['endereco_comprador'];
$numero_comprador   = $checkout['numero_comprador'];
$bairro_comprador   = $checkout['bairro_comprador'];
$cep_comprador      = $checkout['cep_comprador'];
$cidade_comprador   = $checkout['cidade_comprador'];
$uf_comprador       = $checkout['uf_comprador'];
$pais_comprador     = $checkout['pais_comprador'];
$itemDescription1   = $checkout['itemDescription1'];
$itemAmount1        = $checkout['itemAmount1'];
$itemQuantity1      = $checkout['itemQuantity1'];
$endereco           = $checkout['endereco'];
$numero             = $checkout['numero'];
$bairro             = $checkout['bairro'];
$cep                = $checkout['cep'];
$cidade             = $checkout['cidade'];
$uf                 = $checkout['uf'];
$pais               = $checkout['pais'];
$card_hash          = $checkout['card_hash'];
$send_hash          = $checkout['send_hash'];
$bank_name          = $checkout['bank_name'];
$salvar_cartao      = $checkout['salvar_cartao'];
$cod_usuario        = $checkout['cod_usuario'];

$email_pagseguro          = $checkout['email_pagseguro'];
$token_pagseguro          = $checkout['token_pagseguro'];

$tipo_transacao          = $checkout['tipo'];

$dados_adicionais     = $checkout['dados_adicionais'];
$mensalidade          = $dados_adicionais['mensalidade'];

// header('Content-type: text/html; charset=UTF-8');

//URL da chamada para o PagSeguro
// $url = "https://ws.pagseguro.uol.com.br/v2/transactions/?email=" .$email_pagseguro ."&token=".$token_pagseguro;
$url = "https://ws.sandbox.pagseguro.uol.com.br/v2/transactions/?email=" .$email_pagseguro ."&token=".$token_pagseguro;

// Dados de pagamento
$dadosCompra['paymentMode'] = "default";
$dadosCompra['paymentMethod'] = $tipo_pagamento;
$dadosCompra['bankName'] = $bank_name; // Não tem limite de caracteres
$dadosCompra['creditCardToken'] = $card_hash; // Não tem limite de caracteres
$dadosCompra['installmentQuantity'] = 1; // Quantidade de parcelas escolhidas pelo cliente. 1 a 18
$dadosCompra['installmentValue'] = $itemAmount1; // Valor das parcelas obtidas no serviço de opções de parcelamento.
$dadosCompra['noInterestInstallmentQuantity'] = 2; // Quantidade de parcelas sem juros oferecidas ao cliente. O valor deve ser o mesmo indicado no método getInstallments, no parâmetro maxInstallmentNoInterest.
$dadosCompra['creditCardHolderName'] = $senderName; // Obrigatório para Cartão de Crédito. min = 1, max = 50 caracteres
$dadosCompra['creditCardHolderCPF'] = $senderCPF; // Obrigatorio, somente números.
$dadosCompra['creditCardHolderBirthDate'] = "01/01/1950"; // Obrigatório para Cartão de Crédito. Tipo: dd/MM/yyyy Formato: 31/12/2013
$dadosCompra['creditCardHolderAreaCode'] = $senderAreaCode; // Um número de 2 dígitos correspondente a um DDD válido.
$dadosCompra['creditCardHolderPhone'] = $senderPhone; // Um número entre 7 e 9 dígitos.


// Dados da compra
$dadosCompra['currency'] = "BRL"; // Case sensitive. Somente o valor BRL é aceito.
$dadosCompra['itemId1'] = "0001"; // com limite de 100 caracteres.
$dadosCompra['itemDescription1'] = $itemDescription1; // com limite de 100 caracteres.
$dadosCompra['itemAmount1'] = $itemAmount1; // Decimal, com duas casas decimais separadas por ponto (p.e., 1234.56), maior que 0.00 e menor ou igual a 9999999.00.
$dadosCompra['itemQuantity1'] = $itemQuantity1; // Um número inteiro maior ou igual a 1 e menor ou igual a 999
$dadosCompra['reference'] = "REF072017"; // Livre, com o limite de 200 caracteres. Ex: REF072017 - mês e ano referentes a mensalidade

$dadosCompra['notificationURL'] = "http://www.35.198.54.48/web_service/respostaPagamento.php"; //sualoja.com.br/notifica.html\


// dados do comprador
$dadosCompra['senderName'] = $senderName; // No mínimo duas sequências de caracteres, com o limite total de 50 caracteres.
$dadosCompra['senderCPF'] = $senderCPF;  // Um número de 11 dígitos.
$dadosCompra['senderAreaCode'] = $senderAreaCode; // Um número de 2 dígitos correspondente a um DDD válido.
$dadosCompra['senderPhone'] = $senderPhone; // Um número de 7 a 9 dígitos.
$dadosCompra['senderEmail'] = $senderEmail; // um e-mail válido (p.e., usuario@site.com.br), com no máximo 60 caracteres.    
$dadosCompra['senderHash'] = $send_hash; // gerado automaticamente
$dadosCompra['shippingType'] = 3; // 1 = Encomenda normal (PAC); 2 = SEDEX; 3 = Tipo de frete não especificado.
$dadosCompra['shippingAddressStreet'] = $endereco_comprador;
$dadosCompra['shippingAddressNumber'] = $numero_comprador;
//$dadosCompra['shippingAddressComplement'] = "";
$dadosCompra['shippingAddressDistrict'] = $bairro_comprador;
$dadosCompra['shippingAddressPostalCode'] = $cep_comprador;
$dadosCompra['shippingAddressCity'] = $cidade_comprador;
$dadosCompra['shippingAddressState'] = $uf_comprador;
$dadosCompra['shippingAddressCountry'] = $pais_comprador; // somente BRA é permitido


// Dados do Vendedor (Endereço completo do Vendedor - ML3 ou Sede/Congregações)
$dadosCompra['receiverEmail'] = $email_pagseguro; // email do vendedor (Responsável pela Sede ou Congregação)
$dadosCompra['billingAddressStreet'] = $endereco; // Livre, com limite de 80 caracteres.
$dadosCompra['billingAddressNumber'] = $numero; // Livre, com limite de 20 caracteres.
//$dadosCompra['billingAddressComplement'] = ""; // Livre, com limite de 40 caracteres.
$dadosCompra['billingAddressDistrict'] = $bairro; // Livre, com limite de 60 caracteres.
$dadosCompra['billingAddressPostalCode'] = $cep; // Um número de 8 dígitos. (somente números) 
$dadosCompra['billingAddressCity'] = $cidade; // Livre. Deve ser um nome válido de cidade do Brasil, com no mínimo 2 e no máximo 60 caracteres.
$dadosCompra['billingAddressState'] = $uf; // Duas letras, representando a sigla do estado brasileiro correspondente.
$dadosCompra['billingAddressCountry'] = $pais;


$cartao = array();

$cartao["pais_comprador"]           = $checkout['pais_comprador'];
$cartao["itemQuantity1"]            = $checkout['itemQuantity1'];
$cartao["senderEmail"]              = $checkout['senderEmail'];
$cartao["endereco"]                 = $checkout['endereco'];
$cartao["numero"]                   = $checkout['numero'];
$cartao["bairro"]                   = $checkout['bairro'];
$cartao["cep"]                      = $checkout['cep'];
$cartao["cidade"]                   = $checkout['cidade'];
$cartao["uf"]                       = $checkout['uf'];
$cartao["pais"]                     = $checkout['pais'];
$cartao["tipo_pagamento"]           = $checkout['tipo_pagamento'];
$cartao["senderCPF"]                = $checkout['senderCPF'];
$cartao["cartao"]                   = $checkout['cartao'];
$cartao["cvv"]                      = '';
$cartao["validade"]                 = $checkout['validade'];
$cartao["senderName"]               = $checkout['senderName'];
$cartao["endereco_comprador"]       = $checkout['endereco_comprador'];
$cartao["numero_comprador"]         = $checkout['numero_comprador'];
$cartao["bairro_comprador"]         = $checkout['bairro_comprador'];
$cartao["cep_comprador"]            = $checkout['cep_comprador'];
$cartao["uf_comprador"]             = $checkout['uf_comprador'];
$cartao["cidade_comprador"]         = $checkout['cidade_comprador'];
$cartao["anoValidade"]              = $checkout['anoValidade'];
$cartao["mesValidade"]              = $checkout['mesValidade'];
$cartao["senderPhone"]              = $checkout['senderPhone'];
$cartao["senderAreaCode"]           = $checkout['senderAreaCode'];

$cartao_json = json_encode($cartao, JSON_UNESCAPED_UNICODE);



// echo '<pre>';
// print_r($dadosCompra);
// die;

//Transformando os dados da compra no formato da URL
$dadosCompra = http_build_query($dadosCompra);

//Realizando a chamada
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $dadosCompra);
$respostaPagSeguro = curl_exec($curl);
$http = curl_getinfo($curl);

$respostaPagSeguro = simplexml_load_string($respostaPagSeguro);

if($http['http_code'] != "200"){
    foreach ($respostaPagSeguro->error as $key => $erro) {
        echo $erro->code.":".$erro->message;
    }
    echo "deu_ruim";   			

}else{

    $code_transacao = $respostaPagSeguro->code;
    $status_pagamento = $respostaPagSeguro->status;

    $obj_resposta = json_encode($respostaPagSeguro);

    $connect = new Con();
    $con = $connect->getCon();

    try{

        $con->beginTransaction();


        $lastIdCartao = 0;

        $sql = $con->exec("INSERT INTO tb_historico_status (code_transacao, obj_resposta,data_historico_status) VALUES ( '$code_transacao', '$obj_resposta', NOW())");

        if($salvar_cartao){
            $con->exec("INSERT INTO tb_cartao  (cod_usuario, card) VALUES ( $cod_usuario, '$cartao_json' )");
            $lastIdCartao = $con->lastInsertId();

        }

        if($tipo_transacao == "EVENTO"){

            $id_evento = $dados_adicionais['id_evento'];
            $id_igreja = $dados_adicionais['id_igreja'];

            $sql = $con->exec("INSERT INTO tb_evento_participante (status_pagamento, code_transacao,cod_evento, cod_usuario, valor, cod_cartao) VALUES ( $status_pagamento, '$code_transacao', $id_evento, $cod_usuario, '$itemAmount1',  $lastIdCartao )");


            $str = "INSERT INTO tb_lancamento (

            cod_igreja, 
            cod_usuario, 
            cod_quem_pagou, 
            descricao, 
            tipo, 
            entrada_saida, 
            dt_lancamento, 
            valor,
            cod_evento

            ) 

            VALUES (

            $id_igreja, 
            $cod_usuario, 
            $cod_usuario, 
            '$itemDescription1', 
            'evento', 
            'E', 
            NOW(), 
            '$itemAmount1',
            $id_evento

            )";

            $sql_lancamento = $con->exec($str);     

            $lastIdLancamento = $con->lastInsertId();

            if($lastIdLancamento){
                $str_parcela = "INSERT INTO tb_parcela (

                cod_lancamento, 
                foi_pago, 
                num_parcela, 
                valor_parcela,
                dt_parcela,
                status_pagamento,
                code_transacao

                ) 

                VALUES (

                $lastIdLancamento, 
                0, 
                0, 
                '$itemAmount1',    
                NOW(),
                $status_pagamento,
                '$code_transacao'

                )";

                $sql_parcela = $con->exec($str_parcela);        
            }


        }else if($tipo_transacao == "MENSALIDADE"){

            $id_mensalidade = $dados_adicionais['id_mensalidade'];

            $con->exec("UPDATE tb_mensalidade SET status_pagamento = $status_pagamento, code_transacao = '$code_transacao' WHERE id_mensalidade = $id_mensalidade");


        }else if($tipo_transacao == "OFERTA" || $tipo_transacao == "DÍZIMO"){

            if($tipo_transacao == "OFERTA" ){
                $tipo_aux = "oferta";
            }else{
                $tipo_aux = "dízimo";
            }

            $id_usuario = $dados_adicionais['id_usuario'];
            $id_igreja = $dados_adicionais['id_igreja'];

            $str = "INSERT INTO tb_lancamento (

            cod_igreja, 
            cod_usuario, 
            descricao, 
            tipo, 
            entrada_saida, 
            dt_lancamento, 
            valor,
            cod_quem_pagou

            ) 

            VALUES (

            $id_igreja, 
            $cod_usuario, 
            '$itemDescription1', 
            '$tipo_aux', 
            'E', 
            NOW(), 
            '$itemAmount1',
            $id_usuario

            )";

            $sql_lancamento = $con->exec($str);     

            $lastIdLancamento = $con->lastInsertId();

            if($lastIdLancamento){
                $str_parcela = "INSERT INTO tb_parcela (

                cod_lancamento, 
                foi_pago, 
                num_parcela, 
                valor_parcela,
                dt_parcela,
                status_pagamento,
                code_transacao

                ) 

                VALUES (

                $lastIdLancamento, 
                0, 
                0, 
                '$itemAmount1',    
                NOW(),
                $status_pagamento,
                '$code_transacao'

                )";

                $sql_parcela = $con->exec($str_parcela);        
            }



        }


        $con->commit();

    }catch(Exception $e){
        $con->rollback();
    }

    echo "deu_bom";   			

}


// Caso o HTTP for 200 será criada a URL de pagamento
// echo '<a href="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code='.$respostaPagSeguro->code.'">Ir para o Checkout</a>';
?>