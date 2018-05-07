
<?php
	include ("../assets/conexionBD.php");
	$codigo = $_REQUEST['codigo'];
	
	$sql = "UPDATE pregunta SET elimina_pregunta = 1 WHERE idPregunta = $codigo";
	$res = mysql_query($sql,$con);
	
	$sql = "SELECT * FROM pregunta WHERE idPregunta = $codigo";
	$res = mysql_query($sql,$con);
	$Tipo = mysql_result($res, 0, "Tipo_idTipo");
	
	if ($Tipo == 2){
		$sql = "SELECT * FROM pregunta_opcion WHERE Pregunta_idPregunta = $codigo";
		$res = mysql_query($sql,$con);
		$num = mysql_num_rows($res);	
	
		for ($i=0; $i<$num; $i++) {
			$idOpcion = mysql_result($res, $i, "Opcion_idOpcion");
			$sql = "UPDATE opcion SET elimina_opcion = 1 WHERE idOpcion = $idOpcion";
			$resp = mysql_query($sql,$con);
		}
	}
?>