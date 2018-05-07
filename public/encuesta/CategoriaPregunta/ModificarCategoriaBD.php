
<?php

	$codigo = $_REQUEST['codigo'];

	include ("../assets/conexionBD.php");
	$sql = "SELECT * FROM categoria WHERE idCategoria = '$codigo'";
	$res = mysql_query($sql, $con);
	$codigo = mysql_result($res, 0, "idCategoria");
	$nombre = mysql_result($res, 0, "nombreCategoria");
	$descripcion = mysql_result($res, 0, "descripcionCategoria");
?>