
<?php

	$codigo = $_REQUEST['codigo'];

	include ("../assets/conexionBD.php");
	$sql = "SELECT * FROM pregunta WHERE idPregunta = '$codigo'";
	$res = mysql_query($sql, $con);
	
	if (mysql_num_rows($res) > 0) 
	{
		$bandera = 1;
	}	
	
	echo $bandera;
?>