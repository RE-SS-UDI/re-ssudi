<?php
	if( $_SESSION["tipoUsuario"] != "Administrador" )
		header ("Location: ../miPerfil/miPerfil.php");
?>
<?php
	include ('../assets/conexionBD.php');

	$codigo=$_REQUEST['codigo'];
	$nombre= $_REQUEST['nombre'];
	$descripcion= $_REQUEST['descripcion'];
	
	$sql="UPDATE categoria
	SET idCategoria='$codigo', descripcionCategoria='$descripcion', nombreCategoria='$nombre'
	WHERE idCategoria=$codigo";

	$res=mysql_query($sql,$con);
?>