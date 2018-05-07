
<?php
	include ('../assets/conexionBD.php');
	
	$codigo = $_REQUEST['codigo'];
	$categoria = $_REQUEST['categoria'];
	$pregunta = $_REQUEST['pregunta'];
	$respuesta = $_REQUEST['respuesta'];
	
	$sql = "SELECT * FROM pregunta WHERE idPregunta = $codigo";
	$res = mysql_query($sql,$con);
	
	$TipoViejo  = mysql_result($res,0, "Tipo_idTipo");
	
	$sql="UPDATE pregunta
	SET descripcionPregunta='$pregunta', Tipo_idTipo='$respuesta', Categoria_idCategoria='$categoria'
	WHERE idPregunta=$codigo";
	$res = mysql_query($sql,$con);
	
	if ($TipoViejo == 2){
		$sql = "SELECT * FROM pregunta_opcion WHERE Pregunta_idPregunta = $codigo";
		$resPO = mysql_query($sql,$con);
		$numPO = mysql_num_rows($resPO);
	}
	if ($respuesta == 2) {
		$opc = $_REQUEST['opc'];	
		$opc1 = $_REQUEST['opc1'];
		$opc2 = $_REQUEST['opc2'];
		$opc3 = $_REQUEST['opc3'];
		$opc4 = $_REQUEST['opc4'];
		$opc5 = $_REQUEST['opc5'];
	}
	
	//Si son el mismo numero de opciones, solo actualizamos
	if ($TipoViejo == 2 && $respuesta == 2 && $numPO == $opc)
	{
		$j=1;
		for ($i=0; $i<$numPO; $i++)
		{
			$idOpcion  = mysql_result($resPO, $i, "Opcion_idOpcion");
			if ($j==1)
				$sql="UPDATE opcion SET descripcionOpcion='$opc1' WHERE idOpcion=$idOpcion";
			if ($j==2)
				$sql="UPDATE opcion SET descripcionOpcion='$opc2' WHERE idOpcion=$idOpcion";
			if ($j==3)
				$sql="UPDATE opcion SET descripcionOpcion='$opc3' WHERE idOpcion=$idOpcion";
			if ($j==4)
				$sql="UPDATE opcion SET descripcionOpcion='$opc4' WHERE idOpcion=$idOpcion";
			if ($j==5)
				$sql="UPDATE opcion SET descripcionOpcion='$opc5' WHERE idOpcion=$idOpcion";
			$res = mysql_query($sql,$con);
			$j++;
		}
	}

	//Si se agregaron mas opciones actualizamos e insertamos
	if ($TipoViejo == 2 && $numPO < $opc && $respuesta == 2){
		//Actualizamos las que sean posibles
		$j=1;
		for ($i=0; $i<$numPO; $i++)
		{
			$idOpcion  = mysql_result($resPO, $i, "Opcion_idOpcion");
			if ($j==1)
				$sql="UPDATE opcion SET descripcionOpcion='$opc1' WHERE idOpcion=$idOpcion";
			if ($j==2)
				$sql="UPDATE opcion SET descripcionOpcion='$opc2' WHERE idOpcion=$idOpcion";
			if ($j==3)
				$sql="UPDATE opcion SET descripcionOpcion='$opc3' WHERE idOpcion=$idOpcion";
			if ($j==4)
				$sql="UPDATE opcion SET descripcionOpcion='$opc4' WHERE idOpcion=$idOpcion";
			$res = mysql_query($sql,$con);
			$j++;
		}
		//Insertamos las opciones nuevas
		if ($numPO == 2 && $opc == 3){
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc3')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);	
		}
		if ($numPO == 2 && $opc == 4){
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc3')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
			
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc4')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
		}
		
		if ($numPO == 2 && $opc == 5){
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc3')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
			
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc4')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
			
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc5')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
		}
		if ($numPO == 3 && $opc == 4){
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc4')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);	
		}
		if ($numPO == 3 && $opc == 5){
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc4')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
			
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc5')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
		}
		if ($numPO == 4 && $opc == 5){
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc5')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);	
		}	
	}
	
	//Si se quitaron opciones actualizamos y eliminamos
	if ($TipoViejo == 2 && $numPO > $opc && $respuesta == 2){
		//Actualizamos las que sean posibles
		$j=1;
		for ($i=0; $i<$opc; $i++)
		{
			$idOpcion  = mysql_result($resPO, $i, "Opcion_idOpcion");
			if ($j==1)
				$sql="UPDATE opcion SET descripcionOpcion='$opc1' WHERE idOpcion=$idOpcion";
			if ($j==2)
				$sql="UPDATE opcion SET descripcionOpcion='$opc2' WHERE idOpcion=$idOpcion";
			if ($j==3)
				$sql="UPDATE opcion SET descripcionOpcion='$opc3' WHERE idOpcion=$idOpcion";
			if ($j==4)
				$sql="UPDATE opcion SET descripcionOpcion='$opc4' WHERE idOpcion=$idOpcion";
			$res = mysql_query($sql,$con);
			$j++;
		}
		//Actualizamos las opciones eliminadas
		if ($numPO == 5 && $opc == 4){
			$idOpcion  = mysql_result($resPO, 4, "Opcion_idOpcion");
			$sql = "UPDATE opcion SET elimina_opcion = 1 WHERE idOpcion = $idOpcion";
			$resp = mysql_query($sql,$con);
		}
		if ($numPO == 5 && $opc == 3){
			$idOpcion  = mysql_result($resPO, 4, "Opcion_idOpcion");
			$sql = "UPDATE opcion SET elimina_opcion = 1 WHERE idOpcion = $idOpcion";
			$resp = mysql_query($sql,$con);
			
			$idOpcion  = mysql_result($resPO, 3, "Opcion_idOpcion");
			$sql = "UPDATE opcion SET elimina_opcion = 1 WHERE idOpcion = $idOpcion";
			$resp = mysql_query($sql,$con);
		}
		if ($numPO == 5 && $opc == 2){
			$idOpcion  = mysql_result($resPO, 4, "Opcion_idOpcion");
			$sql = "UPDATE opcion SET elimina_opcion = 1 WHERE idOpcion = $idOpcion";
			$resp = mysql_query($sql,$con);
			
			$idOpcion  = mysql_result($resPO, 3, "Opcion_idOpcion");
			$sql = "UPDATE opcion SET elimina_opcion = 1 WHERE idOpcion = $idOpcion";
			$resp = mysql_query($sql,$con);
			
			$idOpcion  = mysql_result($resPO, 2, "Opcion_idOpcion");
			$sql = "UPDATE opcion SET elimina_opcion = 1 WHERE idOpcion = $idOpcion";
			$resp = mysql_query($sql,$con);
		}
		if ($numPO == 4 && $opc == 3){
			$idOpcion  = mysql_result($resPO, 3, "Opcion_idOpcion");
			$sql = "UPDATE opcion SET elimina_opcion = 1 WHERE idOpcion = $idOpcion";
			$resp = mysql_query($sql,$con);
		}
		if ($numPO == 4 && $opc == 2){
			$idOpcion  = mysql_result($resPO, 3, "Opcion_idOpcion");
			$sql = "UPDATE opcion SET elimina_opcion = 1 WHERE idOpcion = $idOpcion";
			$resp = mysql_query($sql,$con);
			
			$idOpcion  = mysql_result($resPO, 2, "Opcion_idOpcion");
			$sql = "UPDATE opcion SET elimina_opcion = 1 WHERE idOpcion = $idOpcion";
			$resp = mysql_query($sql,$con);
		}
		if ($numPO == 3 && $opc == 2){
			$idOpcion  = mysql_result($resPO, 2, "Opcion_idOpcion");
			$sql = "UPDATE opcion SET elimina_opcion = 1 WHERE idOpcion = $idOpcion";
			$resp = mysql_query($sql,$con);
		}
		
	}

	//Si se tenian opciones y se eligio otro tipo, eliminamos
	if ($TipoViejo == 2 && $respuesta != 2 ){
		for ($i=0; $i<$numPO; $i++) {
			$idOpcion = mysql_result($resPO, $i, "Opcion_idOpcion");
			$sql = "UPDATE opcion SET elimina_opcion = 1 WHERE idOpcion = $idOpcion";
			$resp = mysql_query($sql,$con);
		}
	}
	
	//Si no habia opciones guardadas insertamos.
	if ($TipoViejo!=2 && $respuesta == 2){
		if ($opc == 2){
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc1')";
			$res = mysql_query($sql,$con);
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);

			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc2')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
		}
		if ($opc == 3){
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc1')";
			$res = mysql_query($sql,$con);
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);

			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc2')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
			
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc3')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
		}
		if ($opc == 4){
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc1')";
			$res = mysql_query($sql,$con);
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);

			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc2')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
			
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc3')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
			
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc4')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
		}
		
		if ($opc == 5){
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc1')";
			$res = mysql_query($sql,$con);
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);

			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc2')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
			
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc3')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
			
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc4')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
			
			$sql = "INSERT INTO opcion (descripcionOpcion ) VALUES ('$opc5')";
			$res = mysql_query($sql,$con);	
			$opcion = mysql_insert_id();
			$sql = "INSERT INTO pregunta_opcion (Pregunta_idPregunta, Opcion_idOpcion) VALUES ('$codigo','$opcion')";
			$res = mysql_query($sql,$con);
		}
		
	}
?>