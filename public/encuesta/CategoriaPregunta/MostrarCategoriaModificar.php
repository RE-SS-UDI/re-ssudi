<?php include('../assets/InicioDocumento.php'); ?>
<?php
	if( $_SESSION["tipoUsuario"] != "Administrador" )
		header ("Location: ../miPerfil/miPerfil.php");
?>    
	<input id="codigo" name="codigo" type="text" placeholder="C&oacute;digo de la categor&iacute;a" class="buscar"/>                    
    <input id="Modificar" name="Modificar" type="submit" value="Modificar" class="buscar" onclick="ModificarCategoria(); return false;"/>
	<div id="datos" class="datos"> </div>
	<br><br>
	<?php
	include ("../assets/conexionBD.php");

	$sql = "SELECT * FROM categoria WHERE elimina_categoria = 0";
	$res = mysql_query($sql,$con);
	$num = mysql_num_rows($res);
	
	echo"<table>";
	echo"<tr>";
		echo "<th>C&oacute;digo</th>";
        echo "<th>Nombre</th>";
		echo "<th>Descripci&oacute;n</th>";
		echo "<th>Acciones</th>";
	echo"</tr>"; 	
	for($i=0; $i<$num; $i++)
	{
		$idCategoria  = mysql_result($res, $i, "idCategoria");
		$nombreCategoria = mysql_result($res, $i, "nombreCategoria");
		$descripcionCategoria = mysql_result($res, $i, "descripcionCategoria");
		echo "<tr id=\"$idCategoria\">";
			echo "<td align='center'> $idCategoria</td>";
			echo "<td>$nombreCategoria </td>";
			echo "<td>$descripcionCategoria</td>";
			echo "<td><center><a href=\"#\" onclick=\"ImgModificarCategoria($idCategoria); return false;\"><img src=\"../assets/img/editar.png\" border=\"0\" height=20 /></a></center></td>";
		echo"</tr>";  
	}
	echo "</table>";
	?>
<?php include('../assets/FinDocumento.php'); ?>