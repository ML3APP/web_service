<?php 

	// Report all errors except E_NOTICE
	// This is the default value set in php.ini

error_reporting(E_ALL ^ E_NOTICE);

header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Credentials: true");

date_default_timezone_set('America/Sao_Paulo');

Class Con{
	static function getCon(){
		$con = new PDO('mysql:host=35.198.48.143;dbname=db_ml3', "root", "soudejesus",array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		// $con = new PDO('mysql:host=db_ml3.mysql.dbaas.com.br;dbname=db_ml3', "db_ml3", "h4ck3r5215",array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		// $con = new PDO('mysql:host=localhost;dbname=db_ml3', "root", "",array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		return $con;
	}
}

?>