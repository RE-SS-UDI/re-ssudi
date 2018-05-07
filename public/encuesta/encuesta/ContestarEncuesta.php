<?php $titulo="Contestar Encuestas"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php
	if( $_SESSION["tipoUsuario"] != "Empresa" )
		header ("Location: ../miPerfil/miPerfil.php");
?>                        
	<table>
		<tr>
            <th>Encuesta</th>
			<th  width="20%">Acciones</th>
		</tr>
        <?php
            $sql = "SELECT 	e.idEncuesta, 
							e.nombreEncuesta
					FROM 	encuesta 		 e
					JOIN	encuesta_empresa ee ON ee.Encuesta_idEncuesta = e.idEncuesta
					JOIN	empresa			 em ON em.idEmpresa = ee.Empresa_idEmpresa
					JOIN	usuario			 u  ON u.idUsuario = em.Usuario_idUsuario
					where 	e.elimina_encuesta = 0 AND u.idUsuario = ".$_SESSION["idUsuario"];
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
		?>
					<tr>
						<td><?php echo $row["nombreEncuesta"] ?></td>
						<td><a href="../app/encuesta.php?abrir=<?php echo $row["idEncuesta"] ?>"><img src="../assets/img/editar.png" border="0" height=20 /></a></td>
						
					</tr>
		<?php 
				}
			} 
		?>
	</table> 
	<br><br><br>
<?php include('../assets/FinDocumento.php'); ?>