<?php $titulo="Crear Pregunta"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php
	if( $_SESSION["tipoUsuario"] != "Administrador" )
		header ("Location: ../miPerfil/miPerfil.php");
?>
	<form id="formulario" name="formulario" action="" method="post" >
		<label>Selecciona categor&iacute;a</label>
		<select name="categoria" id="categoria" >
		<option value="0"> Selecciona </option>
		<?php

		$sql = "SELECT * FROM categoria WHERE elimina_categoria = 0";
		$res = mysql_query($sql,$con);
		$num = mysql_num_rows($res);
	
		for($i=0; $i<$num; $i++){
			$idCategoria  = mysql_result($res, $i, "idCategoria");
			$nombreCategoria = mysql_result($res, $i, "nombreCategoria");
			echo "<option value='$idCategoria'> $nombreCategoria </option>";
		}
		?>
		</select>
		<br>
		<label>Ingresa pregunta</label>
		<input id="pregunta" name="pregunta" type="text"/>
		<br>  
		<label>Selecciona tipo de respuesta</label>
		<select name="respuesta" id="respuesta" onchange="crear_pregunta();">
			<option value="0"> Selecciona </option>
			<option value="1"> Verdadero/Falso </option>
			<option value="2"> Opci&oacute;n m&uacute;ltiple </option>
			<option value="3"> Abierta</option>
		</select>
		<br>
		<div id="numOpciones"> </div>                        
		<br>
		<br>
		<div id="opciones"> </div>                        
		<br>
		<input id="Guardar" name="Guardar" type="submit" value="Guardar" onclick="validar_pregunta(); return false;"/>
	</form>
<?php include('../assets/FinDocumento.php'); ?>