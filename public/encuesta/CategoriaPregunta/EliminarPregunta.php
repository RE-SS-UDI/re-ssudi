<?php $titulo="Eliminar Pregunta"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php
	if( $_SESSION["tipoUsuario"] != "Administrador" )
		header ("Location: ../miPerfil/miPerfil.php");
?>
	<form id="formulario" name="formulario" action="" method="post" >
		<label>Categor&iacute;a</label>
			<select name="categoria" id="categoria" disabled="disabled">
				<option value="0"> Selecciona </option>
				<option selected value="1"> General </option>
				<option value="2"> Ambiente </option>
			</select>
		<br>
        <label>Pregunta</label>
		<input id="pregunta" name="pregunta" type="text" value='Pregunta 1' readonly='readonly' />
		<br>  
		<label>Tipo de respuesta</label>
		<select name="respuesta" id="respuesta" disabled="disabled">
			<option value="0"> Selecciona </option>
			<option selected value="1"> Abierta </option>
			<option value="2"> Opci&oacute;n m&uacute;ltiple </option>
			<option value="3"> Verdadero/Falso </option>
		</select>
		<br>
		<div id="opciones"> </div>
		<br>
		<input id="Eliminar" name="Eliminar" type="submit" value="Eliminar" />
	</form>
<?php include('../assets/FinDocumento.php'); ?>