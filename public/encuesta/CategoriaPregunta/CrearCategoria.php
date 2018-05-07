<?php $titulo="Crear Categoria"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php
	if( $_SESSION["tipoUsuario"] != "Administrador" )
		header ("Location: ../miPerfil/miPerfil.php");
?>
	<form name="CrearCategoria" id="CrearCategoria" method="post" enctype="multipart/form-data">               
		<label>Nombre</label>
		<input id="nombre" name="nombre" type="text" placeholder='Nombre'/>
		<br>    
		<label>Descripci&oacute;n</label>
		<input id="descripcion" name="descripcion" type="text" placeholder='Descripci&oacute;n' />
		<br>
		<input id="Guardar" name="Guardar" type="submit" value="Guardar" onclick="VerificarCategoria(); return false;" />
	</form>
<?php include('../assets/FinDocumento.php'); ?>