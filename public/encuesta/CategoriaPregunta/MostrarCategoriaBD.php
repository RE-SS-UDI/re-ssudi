
<?php
	$nombre = $_REQUEST['nombre'];

	include ("../assets/conexionBD.php");
	$sql = "SELECT * FROM categoria WHERE nombreCategoria = '$nombre' AND elimina_categoria = 0";
	$res = mysql_query($sql, $con);
	$num = mysql_num_rows($res);
	
	if ($num > 0) 
	{
		for($i=0; $i<$num; $i++){
			$codigo = mysql_result($res, $i, "idCategoria");
			$nom = mysql_result($res, $i, "nombreCategoria");
			$desc = mysql_result($res, $i, "descripcionCategoria");
			$data = "$data <br><br> C&oacute;digo : $codigo <br> Nombre: $nom <br> Descripci&oacute;n: $desc";
		}
		$data = "$data <br><br><br>";
	}
	
	echo $data;

?>