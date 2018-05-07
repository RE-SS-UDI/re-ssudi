<?php $titulo="Ver Zonas"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
    <form action="" method="get">
        <input name="buscar" type="text" placeholder="Nombre de la zona"/>                    
        <input type="submit" value="Buscar"/>
    </form>
                        
	<table>
		<tr>
			<th>ID</th>
            <th>Nombre</th>
			<th colspan="3" width="20%">Acciones</th>
		</tr>
        <?php
            if (!empty($_GET["buscar"])) {
                $sql = "SELECT idZona, nombreZona FROM zona WHERE nombreZona LIKE '%".htmlspecialchars($_GET["buscar"])."%' AND elimina_zona = 0";
            }else{
                $sql = "SELECT idZona, nombreZona FROM zona where elimina_zona = 0";
            }
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
		?>
					<tr>
						<td><?php echo $row["idZona"] ?></td>
						<td><?php echo $row["nombreZona"] ?></td>
						<td><a href="VerZona.php?id=<?php echo $row["idZona"] ?>"><img src="../assets/img/visualizar.png" border="0" height=20 /></a></td>
						<td><a href="ModificarZona.php?id=<?php echo $row["idZona"] ?>"><img src="../assets/img/editar.png" border="0" height=20 /></a></td>
						<td><a href="EliminarZona.php?id=<?php echo $row["idZona"] ?>"><img src="../assets/img/eliminar.png" border="0" height=20 /></a></td>
					</tr>
		<?php 
				}
			} 
		?>
	</table> 
<?php include('../assets/FinDocumento.php'); ?>