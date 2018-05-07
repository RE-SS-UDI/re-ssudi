<?php $titulo="Asignar Encuestas  "; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('../assets/Atributo.php');?>
<?php
	if( $_SESSION["tipoUsuario"] == "Empresa" || $_SESSION["tipoUsuario"] == "Encuestador")
		header ("Location: ../miPerfil/miPerfil.php");
?>

<?php
//Revisar que se envio el get id por URL
    if(empty($_GET["id"])){
?>
        <form method="get" action="SeleccionarEmpresa.php">
            <input type="text" name="id" placeholder="id Encuesta">
            <input type="submit" value="Seleccionar Encuesta">
        </FORM>
<?php
        return; //Termina el codigo
    }
	$idModificar = $_GET["id"];	

	//indica la cantidad de filas por cada tabla
	$cantidadMostrar = 5;
    $idModificar = $_GET["id"];	
	$categoriaSeleccionada = 0;

    //Revisar que exista dicha empresa en la base de datos
    $sql = "SELECT nombreEncuesta FROM encuesta WHERE idEncuesta='".$idModificar."' and elimina_encuesta = 0";
    $resultado = $conn->query($sql);
    if ($resultado->num_rows <= 0){
?>
        La encuesta ' <?php echo $idModificar ?> ' no existe<br><br>
        <form action="ModificarEncuesta.php">
            <input type="submit" value="Modificar otra">
        </FORM>
<?php
        return; //Termina el codigo
    }
	else
		$nombre = $resultado->fetch_assoc();
    Atributo::configurar($conn, "encuesta");
    $obj = (object) array('nombreEncuesta' => new Atributo("nombreEncuesta", TipoDeDato::ALFANUMERICO, 20, $null=false, $unique=true));
	$obj->nombreEncuesta->valor = $nombre["nombreEncuesta"];
	$categoriaError = "";
    	
    $registroAgregado = false;    
	$cat = false;
		
    if( empty($_GET["ini"] ) ){
		$inicio = 0;
	}
	else
		$inicio = $_GET["ini"];

    //Si se recibio una respuesta por parte del formulario realiza lo siguiente:
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Vualve a cargar la pagina para agregar otro registro
        if (!empty($_POST["reload"])){
            unset($_POST["reload"]);
            header('Location:'.$_SERVER['REQUEST_URI']);
        }
		if( !empty($_POST["terminar"]) ){
			header ("Location: ../administracion/MostrarEncuestaEmpresa.php");
			
		}	
        
    }
	if( !empty($_GET["emp"]) ){		
		$sql = "SELECT Empresa_idEmpresa FROM encuesta_empresa WHERE ";
		$sql = $sql."Empresa_idEmpresa=".$_GET["emp"]." AND Encuesta_idEncuesta=".$idModificar;
		$resultadoExiste = $conn->query($sql);
		if ( $resultadoExiste->num_rows == 0){
			$sql = $conn->prepare("INSERT INTO encuesta_empresa (Empresa_idEmpresa, Encuesta_idEncuesta) VALUES (?,?)");
			if($sql->bind_param("ii", $_GET["emp"], $idModificar) )
				
			if($sql->execute())
				$registroAgregado = false;
		}
		else{?>
		<script> alert('Encuesta ya asignada a empresa')</script>
		<?php
		}
	}

    if(!$registroAgregado){
?>
  
	<center><h4>Encuesta: <?php echo $obj->nombreEncuesta->valor; ?></h4></center>   
    
			<label>Empresas</label>
			<table>
			<tr>
				<th>NombreEmpresa</th>
				<th>RFC</th>
				<th width="20%">Acciones</th>
			</tr>
			<?php
				$fin = $inicio+$cantidadMostrar;
				$sql = "SELECT 	DISTINCT e.idEmpresa, e.nombreEmpresa, e.rfc
						FROM 	empresa e	WHERE 	e.elimina_empresa = 0 ";
				$result = $conn->query($sql);
				$final = $result->num_rows;
				$sql = $sql."LIMIT ".$inicio.", ".$fin;
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					// output data of each row
					while($row = $result->fetch_assoc()) {
				?>
						<tr>
							<td><?php echo $row["nombreEmpresa"] ?></td>
							<td>
							<?php 
								echo $row["rfc"];
							?>
							</td>
							<td><a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$idModificar."&ini=".$inicio."&emp=".$row["idEmpresa"];?>">
								<img src="../assets/img/agregar.png" border="0" height=20 />
							</a></td>
						</tr>
				<?php 
					}
				} 
			?>
			</table>
			<br>
			<table>
				<tr>
					<td style='border: inset 0pt'>
						<?php if($inicio > 0 ) $inicio = $inicio-$cantidadMostrar; ?>
						<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$idModificar."&ini=".$inicio?>">
							<input type="submit" name="anterior" value="Anterior" <?php if($inicio < 0 ) echo "disabled"?>>  
						</form>			
					</td>			
					<td style='border: inset 0pt'>
						<?php if( $inicio < $final ) $inicio = $inicio+$cantidadMostrar; ?>
						<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$idModificar."&ini=".$inicio;?>">
							<input type="submit" name="siguiente" value="Siguiente" <?php if($inicio >= $final  ) echo "disabled"?>>  
						</form>						
					</td>
				</tr>
			</table>
			<br><br>
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$idModificar;?>">
			<input type="submit" name="terminar" value="Terminar">  
		</form>
		<br><br>
	<?php
	} 
?>


<?php include('../assets/FinDocumento.php'); ?>