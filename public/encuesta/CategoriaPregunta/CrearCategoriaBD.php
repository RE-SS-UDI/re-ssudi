
<?php
	include ("../assets/conexionBD.php");
	$nombre = $_REQUEST['nombre'];
	$descripcion = $_REQUEST['descripcion'];
	
	$sql = "INSERT INTO categoria (descripcionCategoria, nombreCategoria )
			VALUES ('$descripcion','$nombre')";
	$res = mysql_query($sql,$con);
?>