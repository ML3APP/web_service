<?php 

$obj = json_decode(file_get_contents('php://input'), true);

include("../connect/db_conect.php");
$connect = new Con();
$con = $connect->getCon();


$id_igreja = $obj["id_igreja"];
$id_usuario = $obj["id_usuario"];

try{

	$con->beginTransaction();

	$str = "SELECT 

	tb_evento_participante.*,

	(SELECT COUNT(id_interessado_evento) FROM tb_interessado_evento WHERE tb_interessado_evento.cod_evento = tb_evento.id_evento and tb_interessado_evento.cod_usuario = $id_usuario) as 'interessado', 

	(SELECT COUNT(id_evento_participante) FROM tb_evento_participante WHERE tb_evento_participante.desistiu = 0 and tb_evento_participante.cod_evento = tb_evento.id_evento) as 'qtd_participarao', 

	(SELECT COUNT(id_interessado_evento) FROM tb_interessado_evento WHERE tb_interessado_evento.cod_evento = tb_evento.id_evento) as 'qtd_interessados', 


	tb_evento.*

	FROM tb_evento 

	LEFT JOIN tb_evento_participante ON (tb_evento_participante.desistiu = 0 and tb_evento_participante.cod_evento = tb_evento.id_evento and tb_evento_participante.cod_usuario = $id_usuario)

	WHERE DATE(tb_evento.data) >= DATE(NOW()) and tb_evento.excluido = 0  and 
	(tb_evento.cod_igreja = $id_igreja  or tb_evento.cod_igreja = (SELECT tb_sede.id_igreja FROM tb_igreja as tb_sede INNER JOIN tb_igreja as tb_cong WHERE tb_cong.id_igreja = $id_igreja and tb_cong.cod_sede = tb_sede.id_igreja LIMIT 1 ))

	 ORDER BY tb_evento.data";

	// echo $str;

	$sql = $con->query($str);
	$result = $sql->fetchAll();

	echo json_encode($result);
	
	$con->commit();

}catch(Exception $e){
	$con->rollback();
}

?>