<?php
	$dms = 'mysql:host=localhost;dbname=twitter';
	$user = 'root';
	$pass = ''; 

	try {
		$pdo = new PDO($dms , $user , $pass);
	}catch(PDOException $e){

		echo "Connection error!" . $e->getMessage();

	}
?>

