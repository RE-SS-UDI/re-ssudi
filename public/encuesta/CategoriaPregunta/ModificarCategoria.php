<?php $titulo="Modificar Categoria"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('ModificarCategoriaBD.php'); ?>
<?php
	if( $_SESSION["tipoUsuario"] != "Administrador" )
		header ("Location: ../miPerfil/miPerfil.php");
?>
	<form id="forma2" name="forma2" method="post" enctype="multipart/form-data" >
		<?php
		echo "<label>C&oacute;digo</label>
		<input type='text' name='codigo' id='$codigo' readonly='readonly' value='$codigo'>
		<br>
		<label>Nombre</label>
		<input id='nombre' name='nombre' type='text' placeholder='Nombre' value='$nombre'/>
		<br>    
		<label>Descripci&oacute;n</label>
		<input id='descripcion' name='descripcion' type='text' placeholder='Descripci&oacute;n' value='$descripcion' />
		<br>
		<input id='Guardar' name='Guardar' type='submit' value='Guardar' onclick='ModificarDatosCategoria(); return false;'/>";
		?>
	</form>
<?php include('../assets/FinDocumento.php'); ?>