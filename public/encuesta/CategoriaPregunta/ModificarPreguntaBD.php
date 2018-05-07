
<?php

	$codigo = $_REQUEST['codigo'];

	include ("../assets/conexionBD.php");
	$sql = "SELECT * FROM pregunta WHERE idPregunta = '$codigo'";
	$res = mysql_query($sql, $con);
	$idPregunta = mysql_result($res, 0, "idPregunta");
	$descripcionPregunta = mysql_result($res, 0, "descripcionPregunta");
	$Tipo_idTipo = mysql_result($res, 0, "Tipo_idTipo");
	$Categoria_idCategoria = mysql_result($res, 0, "Categoria_idCategoria");
?>