<?php $titulo="Ver Usuarios"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php
	if( $_SESSION["tipoUsuario"] == "Empresa" || $_SESSION["tipoUsuario"] == "Encuestador")
		header ("Location: ../miPerfil/miPerfil.php");
?>
    <form action="" method="get">
        <input name="buscar" type="text" placeholder="Nombre del usuario"/>                    
        <input type="submit" value="Buscar"/>
    </form>
                        
	<table>
		<tr>
			<th>ID</th>
            <th>Nombre</th>
            <th>Perfil</th>
			<th colspan="3" width="20%">Acciones</th>
		</tr>
        <?php
            if (!empty($_GET["buscar"])) {
				$sql = "SELECT idUsuario, nombreUsuario, Perfil_idPerfil FROM usuario WHERE nombreUsuario LIKE '%".htmlspecialchars($_GET["buscar"])."%' AND elimina_usuario = 0";
				if( $_SESSION["tipoUsuario"] == "Coordinador" ) {
					$sql = $sql." AND ( Perfil_idPerfil = 1  OR Perfil_idPerfil = 3 )";
				}
            }
			else{
				$sql = "SELECT idUsuario, nombreUsuario, Perfil_idPerfil FROM usuario where elimina_usuario = 0";
				if( $_SESSION["tipoUsuario"] == "Coordinador" ) {
					$sql = $sql." AND ( Perfil_idPerfil = 1  OR Perfil_idPerfil = 3 )";
				}
            }
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
					$sqlPerfil = "SELECT nombrePerfil FROM perfil WHERE idPerfil = '".$row["Perfil_idPerfil"]."'";
                    $rasultadoPerfil = $conn->query($sqlPerfil);
                    $perfil = $rasultadoPerfil->fetch_assoc();
		?>
					<tr>
						<td><?php echo $row["idUsuario"] ?></td>
						<td><?php echo $row["nombreUsuario"] ?></td>
						<td><?php echo $perfil["nombrePerfil"] ?></td>
						<td><a href="VerUsuario.php?id=<?php echo $row["idUsuario"] ?>"><img src="../assets/img/visualizar.png" border="0" height=20 /></a></td>
						<td><a href="ModificarUsuario.php?id=<?php echo $row["idUsuario"] ?>"><img src="../assets/img/editar.png" border="0" height=20 /></a></td>
						<td><a href="EliminarUsuario.php?id=<?php echo $row["idUsuario"] ?>"><img src="../assets/img/eliminar.png" border="0" height=20 /></a></td>
					</tr>
		<?php 
				}
			} 
		?>
	</table> 
	<br><br>
<?php include('../assets/FinDocumento.php'); ?>