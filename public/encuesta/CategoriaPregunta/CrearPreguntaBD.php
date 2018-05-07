
<?php
	include ("../assets/conexionBD.php");
	
	$categoria = $_REQUEST['categoria'];
	$pregunta = $_REQUEST['pregunta'];
	$respuesta = $_REQUEST['respuesta'];
	
	$sql = "INSERT INTO pregunta (descripcionPregunta, Tipo_idTipo, Categoria_idCategoria )
			VALUES ('$pregunta','$respuesta', '$categoria')";
	$res = mysql_query($sql,$con);
	$codigo = mysql_insert_id();
	//Insertar opciones 
	if ($respuesta == 2){
		$opc = $_REQUEST['opc'];
		$opc1 = $_REQUEST['opc1'];
		$opc2 = $_REQUEST['opc2'];
		$opc3 = $_REQUEST['opc3'];
		$opc4 = $_REQUEST['opc4'];
		$opc5 = $_REQUEST['opc5'];
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