<?php $titulo="Ver Encuestas"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php
	if( $_SESSION["tipoUsuario"] == "Empresa" || $_SESSION["tipoUsuario"] == "Encuestador")
		header ("Location: ../miPerfil/miPerfil.php");
?>
    <form action="" method="get">
        <input name="buscar" type="text" placeholder="Nombre de la enuesta"/>                    
        <input type="submit" value="Buscar"/>
    </form>
                        
	<table>
		<tr>
			<th>ID</th>
            <th>Nombre</th>
            <th>Fecha de Creación</th>
			<?php 
				$columna = 1;
				if( $_SESSION["tipoUsuario"] == "Administrador" )
					$columna = 3; 
			?>
			<th colspan="<?php echo $columna;?>" width="20%">Acciones</th>
		</tr>
        <?php
            if (!empty($_GET["buscar"])) {
                $sql = "SELECT idEncuesta, nombreEncuesta, fechaCreacion FROM encuesta WHERE nombreEncuesta LIKE '%".htmlspecialchars($_GET["buscar"])."%' AND elimina_encuesta = 0";
            }else{
                $sql = "SELECT idEncuesta, nombreEncuesta, fechaCreacion FROM encuesta where elimina_encuesta = 0";
            }
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
		?>
					<tr>
						<td><?php echo $row["idEncuesta"] ?></td>
						<td><?php echo $row["nombreEncuesta"] ?></td>
						<td><?php echo $row["fechaCreacion"] ?></td>
						<td><a href="MostrarPreguntas.php?id=<?php echo $row["idEncuesta"] ?>"><img src="../assets/img/visualizar.png" border="0" height=20 /></a></td>
						<?php if( $_SESSION["tipoUsuario"] == "Administrador" ){ ?>
						<td><a href="ModificarEncuesta.php?id=<?php echo $row["idEncuesta"] ?>"><img src="../assets/img/editar.png" border="0" height=20 /></a></td>
						<td><a href="EliminarEncuesta.php?id=<?php echo $row["idEncuesta"] ?>"><img src="../assets/img/eliminar.png" border="0" height=20 /></a></td>
						<?php } ?>
					</tr>
		<?php 
				}
			} 
		?>
	</table> 
	<br><br><br>
<?php include('../assets/FinDocumento.php'); ?>