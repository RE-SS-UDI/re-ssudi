
<?php
	$codigo = $_REQUEST['codigo'];

	include ("../assets/conexionBD.php");
	$sql = "SELECT * FROM pregunta WHERE idPregunta = '$codigo' AND elimina_pregunta = 0";
	$res = mysql_query($sql, $con);
	$num = mysql_num_rows($res);
	
	if ($num > 0) 
	{
		$codigo = mysql_result($res, 0, "idPregunta");
		$desc = mysql_result($res, 0, "descripcionPregunta");
		$tipo = mysql_result($res, 0, "Tipo_idTipo");
		$categoria = mysql_result($res, 0, "Categoria_idCategoria");
		
		$sql = "SELECT * FROM categoria WHERE idCategoria = '$categoria' AND elimina_categoria = 0";
		$res = mysql_query($sql, $con);
		
		$nomCategoria = mysql_result($res, 0, "nombreCategoria");
		
		if ($tipo == 1)
			$data = "<br><br> C&oacute;digo : $codigo <br> Pregunta: $desc <br> Tipo: Verdadero/Falso <br> Categor&iacute;a: $nomCategoria <br><br><br>";
		
		if ($tipo == 2)
			$data = "<br><br> C&oacute;digo : $codigo <br> Pregunta: $desc <br> Tipo: Opci&oacute;n m&uacute;ltiple <br> Categor&iacute;a: $nomCategoria <br><br><br>";
		
		if ($tipo == 3)
			$data = "<br><br> C&oacute;digo : $codigo <br> Pregunta: $desc <br> Tipo: Abierta <br> Categor&iacute;a: $nomCategoria <br><br><br>";
		
	}
	
	echo $data;

?>