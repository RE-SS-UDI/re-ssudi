<?php include('../assets/InicioDocumento.php'); ?>
<?php
	if( $_SESSION["tipoUsuario"] != "Administrador" )
		header ("Location: ../miPerfil/miPerfil.php");
?>
	<input id="codigo" name="codigo" type="text" placeholder="C&oacute;digo de la pregunta" class="buscar"/>                    
    <input id="Modificar" name="Modificar" type="submit" value="Modificar" class="buscar" onclick="ModificarPregunta(); return false;"/>
	<div id="datos" class="datos"> </div>
	<br><br>
	<?php
	include ("conexionBD.php");

	$sql = "SELECT * FROM categoria WHERE elimina_categoria = 0";
	$res = mysql_query($sql,$con);
	$num = mysql_num_rows($res);
	for($i=0; $i<$num; $i++)
	{
		$idCategoria  = mysql_result($res, $i, "idCategoria");
		$nombreCategoria = mysql_result($res, $i, "nombreCategoria");
		$sql = "SELECT * FROM pregunta WHERE Categoria_idCategoria = $idCategoria AND elimina_pregunta = 0";
		$resp = mysql_query($sql,$con);
		$nump = mysql_num_rows($resp);
		if ($nump >0){
			echo "<label>Categor&iacute;a $idCategoria: $nombreCategoria</label>";
			echo "<table>
					<tr>
						<th>C&oacute;digo</th>
						<th>Descripci&oacute;n</th>
						<th colspan='2'>Acciones</th>
					</tr>";
			for($j=0; $j<$nump; $j++){
				$cod = mysql_result($resp, $j, "idPregunta");
				$desc = mysql_result($resp, $j, "descripcionPregunta");
				echo "<tr>";
					echo "<td>$cod</td>";
					echo "<td>$desc</td>";
					echo "<td><center><a href=\"#\" onclick=\"ImgModificarPregunta($cod); return false;\"><img src=\"../assets/img/editar.png\" border=\"0\" height=20 /></a></center></td>";
				echo "</tr>";
			}
			echo "</table>";
			echo "<br><br>";
		}
	}
	?>
<?php include('../assets/FinDocumento.php'); ?>