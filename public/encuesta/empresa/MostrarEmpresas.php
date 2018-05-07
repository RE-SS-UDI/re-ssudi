<?php $titulo="Ver Empresa"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
    <form action="" method="get">
        <input name="buscar" type="text" placeholder="Razon Social"/>                    
        <input type="submit" value="Buscar"/>
    </form>
                        
	<table>
		<tr>
			<th>ID</th>
            <th>Razon Social</th>
            <th>Usuario</th>
			<th colspan="3" width="20%">Acciones</th>
		</tr>
        <?php
            if (!empty($_GET["buscar"])) {
                $sql = "SELECT idEmpresa, razonSocial, Usuario_idUsuario FROM empresa WHERE razonSocial LIKE '%".htmlspecialchars($_GET["buscar"])."%' AND elimina_empresa = 0";
            }else{
                $sql = "SELECT idEmpresa, razonSocial, Usuario_idUsuario FROM empresa where elimina_empresa=0";
            }
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    $sql = "SELECT nombreUsuario FROM usuario WHERE idUsuario = '".$row["Usuario_idUsuario"]."'";
                    $rasultadoUsuario = $conn->query($sql);
                    $usuario = $rasultadoUsuario->fetch_assoc();
        ?>
        <tr>
            <td><?php echo $row["idEmpresa"] ?></td>
            <td><?php echo $row["razonSocial"] ?></td>
            <td><?php echo $usuario["nombreUsuario"] ?></td>
            <td><a href="VerEmpresa.php?id=<?php echo $row["idEmpresa"] ?>"><img src="../assets/img/visualizar.png" border="0" height=20 /></a></td>
			<td><a href="ModificarEmpresa.php?id=<?php echo $row["idEmpresa"] ?>"><img src="../assets/img/editar.png" border="0" height=20 /></a></td>
			<td><a href="EliminarEmpresa.php?id=<?php echo $row["idEmpresa"] ?>"><img src="../assets/img/eliminar.png" border="0" height=20 /></a></td>
        </tr>
        <?php }} ?>
	</table> 
    <br>
<?php include('../assets/FinDocumento.php'); ?>