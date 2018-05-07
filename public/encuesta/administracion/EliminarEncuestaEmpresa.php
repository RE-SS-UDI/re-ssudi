<?php $titulo="Eliminar Encuestas asignadas"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php
	if( $_SESSION["tipoUsuario"] == "Empresa" || $_SESSION["tipoUsuario"] == "Encuestador")
		header ("Location: ../miPerfil/miPerfil.php");
?>
<?php
	if( !empty($_GET["emp"]) ){	
		$sql = "SELECT Empresa_idEmpresa FROM encuesta_empresa WHERE ";
		$sql = $sql."Empresa_idEmpresa=".$_GET["emp"]." AND Encuesta_idEncuesta=".$_GET["id"];
		$resultadoExiste = $conn->query($sql);
		if ( $resultadoExiste->num_rows > 0){
			$sql = $conn->prepare("DELETE FROM encuesta_empresa WHERE Empresa_idEmpresa=? AND Encuesta_idEncuesta=?");
			if($sql->bind_param("ii", $_GET["emp"], $_GET["id"]) )
				
			if($sql->execute())
				$registroAgregado = false;
		}
		else{?>
		<script> alert('Encuesta ya asignada a empresa')</script>
		<?php
		}
	}
?>
    <form action="" method="get">
        <input name="buscar" type="text" placeholder="Nombre de la enuesta"/>                    
        <input type="submit" value="Buscar"/>
    </form>          
	<table>
		<tr>
			<th>Encuesta</th>
            <th>Empresa</th>
			<th width="20%">Acciones</th>
		</tr>
        <?php
            if (!empty($_GET["buscar"])) {
                $sql = "SELECT 	en.idEncuesta, 
								en.nombreEncuesta,
								em.idEmpresa,
								em.nombreEmpresa
						FROM 	encuesta en
						JOIN 	encuesta_empresa ee ON ee.Encuesta_idEncuesta = en.idEncuesta
						JOIN 	empresa			 em ON em.idEmpresa = Empresa_idEmpresa
						where   en.nombreEncuesta LIKE '%".htmlspecialchars($_GET["buscar"])."%' AND en.elimina_encuesta = 0";
            }else{
                $sql = "SELECT 	en.idEncuesta, 
								en.nombreEncuesta,
								em.idEmpresa,
								em.nombreEmpresa
						FROM 	encuesta en
						JOIN 	encuesta_empresa ee ON ee.Encuesta_idEncuesta = en.idEncuesta
						JOIN 	empresa			 em ON em.idEmpresa = Empresa_idEmpresa
						where en.elimina_encuesta = 0";
            }
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
		?>
					<tr>
						<td><?php echo $row["nombreEncuesta"] ?></td>
						<td><?php echo $row["nombreEmpresa"] ?></td>
						<td><a onclick="return confirm('¿Esta seguro de quitar esta pregunta?')" href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$row["idEncuesta"]."&emp=".$row["idEmpresa"] ?>"><img src="../assets/img/eliminar.png" border="0" height=20 /></a></td>
					</tr>
		<?php 
				}
			} 
		?>
	</table> 
	<br><br><br>
<?php include('../assets/FinDocumento.php'); ?>