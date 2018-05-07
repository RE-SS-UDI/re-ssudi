<?php $titulo="Modificar Pregunta"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('ModificarPreguntaBD.php'); ?>
<?php
	if( $_SESSION["tipoUsuario"] != "Administrador" )
		header ("Location: ../miPerfil/miPerfil.php");
?>
	<form id="formulario" name="formulario" action="" method="post" >
		<?php
		echo "<label>Codigo</label>
			<input type='text' name='codigo' id='$idPregunta' readonly='readonly' value='$idPregunta'>";
		?>
		<br>
		<label>Selecciona categor&iacute;a</label>
		<select name="categoria" id="categoria" >
		<option value="0"> Selecciona </option>
		<?php
		include ("../assets/conexionBD.php");

		$sql = "SELECT * FROM categoria WHERE elimina_categoria = 0";
		$res = mysql_query($sql,$con);
		$num = mysql_num_rows($res);
	
		for($i=0; $i<$num; $i++){
			$idCategoria  = mysql_result($res, $i, "idCategoria");
			$nombreCategoria = mysql_result($res, $i, "nombreCategoria");
			if ($Categoria_idCategoria == $idCategoria)
				echo "<option selected value='$idCategoria'> $nombreCategoria </option>";
			else
				echo "<option value='$idCategoria'> $nombreCategoria </option>";
		}
		?>
		</select>
		<br>
		<label>Ingresa pregunta</label>
		<?php
		echo "<input id='pregunta' name='pregunta' type='text' value='$descripcionPregunta'/>";
		?>
		<br>  
		<label>Selecciona tipo de respuesta</label>
		<select name="respuesta" id="respuesta" onchange="crear_preguntaMod();">
		<option value="0"> Selecciona </option>
			<?php
			if ($Tipo_idTipo == 1)
				echo "<option selected value='1'> Verdadero/Falso </option>";
			else
				echo "<option value='1'> Verdadero/Falso </option>";
			if ($Tipo_idTipo == 2)
				echo "<option selected value='2'> Opci&oacute;n m&uacute;ltiple </option>";
			else
				echo "<option value='2'> Opci&oacute;n m&uacute;ltiple </option>";
			if ($Tipo_idTipo == 3)
				echo "<option selected value='3'> Abierta</option>";
			else
				echo "<option value='3'> Abierta</option>";
			?>
		</select>
		<br>
		<div id="numOpcionesMod"> 
		<?php
		if ($Tipo_idTipo == 2){
			$sql = "SELECT * FROM pregunta_opcion WHERE pregunta_idPregunta = $idPregunta";
			$res = mysql_query($sql,$con);
			$num = mysql_num_rows($res);
			echo "<label>Selecciona numero de opciones</label>";
			echo "<select name='opc' id='opc' onchange='crear_opcionesModificar($num);'>";
			echo "<option value='0'> Selecciona </option>";
			for ($i=2; $i<=5; $i++){
				if ($num == $i)
					echo "<option selected value='$i'> $i </option>";
				else
					echo "<option value='$i'> $i </option>";	
			}
			echo "</select>";
		}			
		?>
		</div>
		<div id="numOpciones"> 
		</div>
		<br>
		<br>
		<div id="opcionesMod">
		<?php
		if ($Tipo_idTipo == 2){
			$j=1;
			for ($i=0; $i<$num; $i++){
				$idOpcion  = mysql_result($res, $i, "Opcion_idOpcion");
				$sql = "SELECT * FROM opcion WHERE idOpcion = $idOpcion";
				$Ores = mysql_query($sql,$con);
				$DescOpcion  = mysql_result($Ores, 0, "descripcionOpcion");
				echo "<label>Opci&oacute;n $j:</label>";
				echo "<input type='text' id='opc$j' name='opc$j'value='$DescOpcion'/>";
				$j++;
			}
		}
		?>
		 </div> 
		<div id="opciones"> </div> 
		<br>
		<input id="Guardar" name="Guardar" type="submit" value="Guardar" onclick="validar_preguntaMod(); return false;"/>
	</form>
<?php include('../assets/FinDocumento.php'); ?>