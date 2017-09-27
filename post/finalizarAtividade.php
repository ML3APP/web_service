<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();

$atividade_finalizada = $obj['atividade_finalizada'];

$presentes = json_encode($atividade_finalizada['presentes']);
$oferta = $atividade_finalizada['oferta'];
$desc_atividade = $atividade_finalizada['desc_atividade'];
$visitantes = json_encode($atividade_finalizada['visitantes']);
$id_post = $atividade_finalizada['id_post'];

try{

	$con->beginTransaction();

	$str = "UPDATE tb_post SET 
	presentes = '$presentes', 
	oferta = $oferta, 
	desc_atividade = '$desc_atividade', 
	atividade_finalizada = 1, 
	visitantes = '$visitantes'  
	WHERE id_post = $id_post";

	echo $str;

	$sql = $con->exec($str);

	if($sql){
		echo "deu_bom";
	}else{
		echo "deu_ruim";
	}
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>