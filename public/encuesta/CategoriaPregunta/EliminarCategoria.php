<?php $titulo="Eliminar Categoria"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php
	if( $_SESSION["tipoUsuario"] != "Administrador" )
		header ("Location: ../miPerfil/miPerfil.php");
?>
	<form id="formulario" action="" method="post" >
		<label>Codigo</label>
		<input id="codigo" name="codigo" type="text" readonly='readonly' value='1'/>
		<br> 
		<label>Nombre</label>
		<input id="nombre" name="nombre" type="text" readonly='readonly' value='General'/>
		<br>    
		<label>Descripci&oacute;n</label>
		<input id="descripcion" name="descripcion" type="text" readonly='readonly' value='Informaci&oacute;n general' />
		<br>
		<input id="Eliminar" name="Eliminar" type="submit" value="Eliminar" />
	</form>
<?php include('../assets/FinDocumento.php'); ?>
