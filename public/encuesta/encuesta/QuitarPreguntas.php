<?php $titulo="Quitar Preguntas de "; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('../assets/Atributo.php');?>
<?php
	if( $_SESSION["tipoUsuario"] != "Administrador" )
		header ("Location: ../miPerfil/miPerfil.php");
?>

<?php
//Revisar que se envio el get id por URL
    if(empty($_GET["id"])){
?>
        <form method="get" action="QuitarPreguntas.php">
            <input type="text" name="id" placeholder="id Encuesta">
            <input type="submit" value="Buscar Encuesta">
        </FORM>
<?php
        return; //Termina el codigo
    }
	$idModificar = $_GET["id"];	

    //Revisar que exista dicha empresa en la base de datos
    $sql = "SELECT idEncuesta FROM encuesta WHERE idEncuesta='".$idModificar."' and elimina_encuesta = 0";
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
	//indica la cantidad de filas por cada tabla
	$cantidadMostrar = 3;
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
		
    //Revisar que existan categorias en la BD
    $sql = "SELECT	DISTINCT
					p.Categoria_idCategoria AS idCategoria,
					c.nombreCategoria
			From	pregunta p
			JOIN	pregunta_encuesta pe ON p.idPregunta = pe.Pregunta_idPregunta
			JOIN	categoria		  c  ON p.Categoria_idCategoria = c.idCategoria
			WHERE	pe.Encuesta_idEncuesta = ".$idModificar." AND
					p.elimina_pregunta =0";
    $resultadoCategoria = $conn->query($sql);
    if ($resultadoCategoria->num_rows <= 0)
        $categoriaError = " *Lo sentimos, no existen categorías en la BD";
	$categoriaSeleccionada = "";
	
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
		if (!empty($_POST["idCategoria"]) && empty($_POST["idPregunta"])){
			$categoriaSeleccionada = $_POST["idCategoria"] ;
			$cat = true;
        }
		if( !empty($_POST["terminar"]) ){
				$registroAgregado = true;
			
		}	
        
    }
	if( !empty($_GET["cat"]) ) {
		$categoriaSeleccionada = $_GET["cat"];
		$cat = true;
	}
	
	if( !empty($_GET["pre"]) ){		
		$sql = "SELECT Pregunta_idPregunta FROM pregunta_encuesta WHERE ";
		$sql = $sql."Pregunta_idPregunta=".$_GET["pre"]." AND Encuesta_idEncuesta=".$idModificar;
		$resultadoExiste = $conn->query($sql);
		if ( $resultadoExiste->num_rows > 0){
			$sql = $conn->prepare("DELETE FROM pregunta_encuesta WHERE Pregunta_idPregunta=? AND Encuesta_idEncuesta=?");
			if($sql->bind_param("ii", $_GET["pre"], $idModificar) )
				
			if($sql->execute())
				$registroAgregado = false;
		}
	}

    if(!$registroAgregado){
?>
  
	<center><h4><?php echo $obj->nombreEncuesta->valor; ?></h4></center>   
    
        <label>Seleccione una categoría</label>
		<span class="error"><?php echo $categoriaError;?></span><br>	
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$idModificar."&ini=".$inicio;?>">
			<select name="idCategoria" >
			<?php
				if ($resultadoCategoria->num_rows > 0) {
				// output data of each row
				while($row = $resultadoCategoria->fetch_assoc()) {
					$select = $categoriaSeleccionada == $row["idCategoria"] ? "selected" : "";
			?>
				<option value="<?php echo $row["idCategoria"] ?>" <?php echo $select ?> ><?php echo $row["nombreCategoria"] ?></option>
			<?php }} ?>
			</select>
			<input type="submit" name="categoria" value="Seleccionar Categoría">
		</FORM>
    
		<?php
		if ( $cat ){
		?>
			<label>Preguntas</label>
			<table>
			<tr>
				<th>Descripción</th>
				<th>Tipo</th>
				<th colspan="2" width="20%">Acciones</th>
			</tr>
			<?php
				$fin = $inicio+$cantidadMostrar;
				$sql = "SELECT  DISTINCT p.idPregunta, p.descripcionPregunta, t.idTipo, t.descripcionTipo
						FROM 	pregunta p
						JOIN 	pregunta_encuesta pe ON p.idPregunta = pe.Pregunta_idPregunta
						JOIN 	tipo t ON t.idTipo = p.Tipo_idTipo
						WHERE 	p.elimina_pregunta =0
								AND p.Categoria_idCategoria ='".$categoriaSeleccionada."' AND 
									pe.Encuesta_idEncuesta = ".$idModificar;
				$result = $conn->query($sql);
				$final = $result->num_rows;
				$sql = $sql." LIMIT ".$inicio.", ".$fin;
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					// output data of each row
					while($row = $result->fetch_assoc()) {
				?>
						<tr>
							<td><?php echo $row["descripcionPregunta"] ?></td>
							<td>
							<?php 
								echo $row["descripcionTipo"];
							?>
							</td>
							<td>
							<?php 
								if ( $row["idTipo"] == 2 ){
									$pregunta = "";
									$sql = "SELECT descripcionOpcion FROM opcion WHERE idOpcion = ANY (";
									$sql = $sql." SELECT Opcion_idOpcion FROM  pregunta_opcion WHERE Pregunta_idPregunta = ".$row["idPregunta"]." ) AND elimina_opcion = 0";
									$resultadoOpcion = $conn->query($sql);
										$pregunta = $pregunta.$row["descripcionPregunta"];
									if ($resultadoOpcion->num_rows > 0){
										while($rowOpcion = $resultadoOpcion->fetch_assoc()) {
											$pregunta = $pregunta." *".$rowOpcion["descripcionOpcion"];
										}
									}
									$click = "alert('".$pregunta."')";
									
							?>
								<img src="../assets/img/visualizar.png" border="0" height=20 onclick="<?php echo $click; ?> "/>
							<?php 
								} 
							?>
							</td>
							<td><a onclick="return confirm('¿Esta seguro de quitar esta pregunta?')" href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$idModificar."&ini=".$inicio."&cat=".$categoriaSeleccionada."&pre=".$row["idPregunta"];?>">
								<img src="../assets/img/eliminar.png" border="0" height=20 />
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
						<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$idModificar."&ini=".$inicio."&cat=".$categoriaSeleccionada;?>">
							<input type="submit" name="anterior" value="Anterior" <?php if($inicio < 0 ) echo "disabled"?>>  
						</form>			
					</td>			
					<td style='border: inset 0pt'>
						<?php if( $inicio < $final ) $inicio = $inicio+$cantidadMostrar; ?>
						<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$idModificar."&ini=".$inicio."&cat=".$categoriaSeleccionada;?>">
							<input type="submit" name="siguiente" value="Siguiente" <?php if($inicio >= $final  ) echo "disabled"?>>  
						</form>						
					</td>
				</tr>
			</table>
			<br><br>
	<?php 
		}?>
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$idModificar;?>">
			<input type="submit" name="terminar" value="Terminar">  
		</form>
		<br><br>
	<?php
	} 
	else { 
?>

Se modificó la encuesta " <?php echo $obj->nombreEncuesta->valor;?> " con éxito 

<table>
	<tr>
	<td style='border: inset 0pt'>
		<form method="post" action="AgregarPreguntas.php?id=<?php echo $idModificar; ?>">
			<input type="submit" name="agregar" value="Agregar Preguntas">
		</FORM>
	</td>
	<td style='border: 0pt'>
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$idModificar; ?>">
			<input type="submit" name="quitar" value="Quitar Preguntas">
		</FORM>
	</td>
	</tr>
	<tr >
	<td  colspan="3" style='border: 0pt' class="noFondo">
		<form method="get" action="MostrarEncuestas.php">
			<input type="submit" name="otro" value="Ver Encuestas">
		</FORM>
	</td>
	</tr>
</table>

<?php } ?>

<?php include('../assets/FinDocumento.php'); ?>