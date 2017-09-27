<?php

header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Origin: ". $_SERVER['HTTP_ORIGIN']);
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Credentials: true");

$nome_imagem = $_POST['value1'];

if(move_uploaded_file($_FILES["file"]["tmp_name"], "../../upload/lancamento/".$nome_imagem)){
	echo "tudo certo";
}else{
	echo "deu ruim";
}

?>