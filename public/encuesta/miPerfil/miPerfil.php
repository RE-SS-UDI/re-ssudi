<?php $titulo="Mi Perfil"; ?>
<?php include('../assets/InicioDocumento.php'); ?>					
<?php

	$perfilSeleccionado = $zonaSeleccionada = -1;
	$registroAgregado = false;
	
/*	$sql = "SELECT nombreZona FROM zona";
	$resultadoZona = $conn->query($sql);
	$sql = "SELECT nombrePerfil FROM perfil ";
	$resultadoPerfil = $conn->query($sql);
*/
//	$sql = "SELECT nombre FROM zona";
//	$resultadoZona = sqlsrv_query( $conn, $sql);
//	$resultadoZona = 0;
	$zona = '';
	$sql = "SELECT descripcion FROM tipo_usuario ";
//	$resultadoPerfil = $conn->query($sql);
	$resultadoPerfil = sqlsrv_query( $conn, $sql);
//	$sql = "SELECT  Zona_idZona, Perfil_idPerfil FROM usuario where idUsuario = '".$_SESSION["idUsuario"]."' and elimina_usuario = 0";
	   
	$sql = "SELECT  zona_id, tipo_usuario FROM usuario where id = '".$_SESSION["idUsuario"]."' and status = 1";
//	$resultadoUsuario = $conn->query($sql);
	$resultadoUsuario = sqlsrv_query( $conn, $sql);

/*	if( $resultadoUsuario->num_rows > 0 ){		
		if($row = $resultadoUsuario->fetch_assoc()) {
			$zonaSeleccionada = $row["Zona_idZona"];
			$perfilSeleccionado = $row["Perfil_idPerfil"];
			 //Revisar que existan perfiles y zonas en la BD
			$sql = "SELECT nombreZona FROM zona where idZona ='".$zonaSeleccionada."'";
			$resultadoZona = $conn->query($sql);
			$resultadoZona = $resultadoZona->fetch_assoc();
			$sql = "SELECT nombrePerfil FROM perfil where idPerfil ='".$perfilSeleccionado."'";
			$resultadoPerfil = $conn->query($sql);
			$resultadoPerfil = $resultadoPerfil->fetch_assoc();
		}
	}
*/
		if($obj = sqlsrv_fetch_object($resultadoUsuario)) {
			$zonaSeleccionada = $obj->zona_id;
			$perfilSeleccionado = $obj->tipo_usuario;
			 //Revisar que existan perfiles y zonas en la BD
			$sql = "SELECT nombre FROM zona where id ='".$zonaSeleccionada."'";
			$resultadoZona = sqlsrv_query( $conn, $sql);

			if($row = sqlsrv_fetch_object($resultadoZona)){
				if (count($row) > 0) {
					$zona = $row->nombre;
				}else{
					$resultadoZona = '';
				}

			}

			$sql = "SELECT descripcion FROM tipo_usuario where id ='".$perfilSeleccionado."'";

			if($row = sqlsrv_fetch_object($resultadoPerfil)) 
			$resultadoPerfil = $row->descripcion;

		}

	if($_SESSION["tipoUsuario"] == "Empresa" ){
		$sql = "SELECT 	rfc,
						razonSocial,
						nombreEmpresa,
						telefono,
						calle, 
						numExterior,
						numInterior,
						colonia,
						municipio,
						codigoPostal, 
						email
				FROM empresa 
				WHERE usuario_id = '".$_SESSION["idUsuario"]."' 
				and status = 1";
		$resultado = sqlsrv_query( $conn, $sql);
		$row = array();
		if( $obje = sqlsrv_fetch_array($resultado)){	
			$row[] = $obje;
		}
	}
?>
    <form method="post" >
        <div class="row">
			<div class="col-md-4 col-xs-12">
				<img src="../files/profile/foto.png" width="150" >
			</div>
			<p><b>Nombre Usuario:</b> <?php echo $_SESSION["Usuario"];?></p>
			<p><b>Zona:</b> <?php echo (($zona != '')?$zona:'');?></p>
			<p><b>Perfil:</b> <?php echo $resultadoPerfil;?></p>
			<?php if($_SESSION["tipoUsuario"] == "Empresa" ){	?>	
				<br><br><br><br><br>
				<p><b>RFC:</b> <?php echo $row["rfc"];?></p>
				<p><b>Razón Social: </b> <?php echo $row["razonSocial"];?></p>	
				<p><b>Nombre de la Empresa: </b> <?php echo $row["nombreEmpresa"];?></p>
				<p><b>Teléfono: </b> <?php echo $row["telefono"];?></p>	
				<p><b>Domicilio: </b> 
				<?php 	echo $row["calle"]." #".$row["numExterior"];
						if( $row["numInterior"] != "" )
							echo " int #".$row["numInterior"];
					
				?></p>
				<p><b>Colonia: </b> <?php echo $row["colonia"];?></p>	
				<p><b>Municipio: </b> <?php echo $row["municipio"];?></p>
				<p><b>Código Postal: </b> <?php echo $row["codigoPostal"];?></p>	
				<p><b>E-mail: </b> <?php echo $row["email"];?></p>
			<?php } ?>
			<div class="col-md-12">
				<div class="botones">
					<a href="../miPerfil/ModificarMiPerfil.php" class="boton">Editar perfil</a>
					<a href="../miPerfil/cerrarSesion.php" class="boton">Cerrar sesión</a>
				</div>
			</div>
		</div>
    </form>
	<br><br><br><br><br>
<?php include("../assets/FinDocumento.php"); ?>