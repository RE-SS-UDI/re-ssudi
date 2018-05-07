
<?php
	include ("../assets/conexionBD.php");
	$codigo = $_REQUEST['codigo'];
	
	$sql = "UPDATE categoria SET elimina_categoria = 1 WHERE idCategoria = $codigo";
	$res = mysql_query($sql,$con);
?>