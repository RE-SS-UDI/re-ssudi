<?php $titulo="Mostrar encuestas asignadas"; ?>
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
			<th>Encuesta</th>
            <th>Empresa</th>
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
					</tr>
		<?php 
				}
			} 
		?>
	</table> 
	<br><br><br>
<?php include('../assets/FinDocumento.php'); ?>