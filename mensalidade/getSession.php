<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");

$email = $obj["email"];
$token = $obj["token"];

try{

  $url = "https://ws.sandbox.pagseguro.uol.com.br/v2/sessions/?email=" .$email ."&token=".$token;
  // $url = "https://ws.pagseguro.uol.com.br/v2/sessions/?email=" .$email ."&token=".$token;

        // gerando id da sessão PagSeguro
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $respostaPagSeguro = curl_exec($curl);
    $http = curl_getinfo($curl);

    if ($http['http_code'] == "200"){
            // Caso o HTTP for 200 será criada a URL de pagamento
        $respostaPagSeguro = simplexml_load_string($respostaPagSeguro);
            // echo '<pre>';
            // print_r($respostaPagSeguro);

        echo $respostaPagSeguro->id;
    } 
    else 
    {
		echo "deu_ruim";
        print_r($http);
    }

}catch(Exception $e){
	print_r($e);
}

?>