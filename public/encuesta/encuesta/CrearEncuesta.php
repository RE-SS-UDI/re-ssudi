<?php $titulo="Crear Encuesta"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('../assets/Atributo.php');?>
<?php
	if( $_SESSION["tipoUsuario"] != "Administrador" )
		header ("Location: ../miPerfil/miPerfil.php");
?>

<?php
    Atributo::configurar($conn, "encuesta");
    $obj = (object) array('nombreEncuesta' => new Atributo("nombreEncuesta", TipoDeDato::ALFANUMERICO, 20, $null=false, $unique=true));
	$obj->nombreEncuesta->error = "";

    $registroAgregado = false;
	$idEncuesta ="";

    //Si se recibio una respuesta por parte del formulario realiza lo siguiente:
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Vualve a cargar la pagina para agregar otro registro
        if (!empty($_POST["reload"])){
            unset($_POST["reload"]);
            header('Location:'.$_SERVER['REQUEST_URI']);
        }
        
        $esValido = true;
        foreach($obj as $key=>$value) {
            if(!$value->esValido()){
                $esValido = false;
                break;
            }
        }
		if( $esValido ){
			$esValido = false;
			if( $obj->nombreEncuesta->valor == "" )
				$obj->nombreEncuesta->error = "*Campo obligatorio";
			else
				$esValido = true;
		}
        
        //Si todo los datos son validos y no hay repetidos
        if($esValido){
			$date = date('Y-m-d');
            $stmt = $conn->prepare("INSERT INTO encuesta (nombreEncuesta, fechaCreacion) VALUES (?,'".$date."')");
            if($stmt->bind_param("s", $obj->nombreEncuesta->valor))
            //Parametros del primer argumento de bind_param:           
            //i - integer
            //d - double
            //s - string
            //b - BLOB
            
            if($stmt->execute())
                $registroAgregado = true;
			
			
            
        }
    }
    
    if(!$registroAgregado){
?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        
        <label>Nombre</label> <span class="error"><?php echo $obj->nombreEncuesta->error;?></span>
        <input type="text" name="<?php echo $obj->nombreEncuesta->nombre;?>" value="<?php echo $obj->nombreEncuesta->valor;?>">
        
        <br><br>
        <input type="submit" name="submit" value="Crear">  
    </form>

<?php } 
else { 
	$sql = "SELECT idEncuesta FROM encuesta WHERE nombreEncuesta = '".$obj->nombreEncuesta->valor."' AND fechaCreacion = '".$date."'";
	$resultadoOpcion = $conn->query($sql);
	if ($resultadoOpcion->num_rows > 0){
		if($rowEncuesta = $resultadoOpcion->fetch_assoc()) {
			$idEncuesta = $rowEncuesta["idEncuesta"];
		}
	}
?>

Se agregó la encuesta " <?php echo $obj->nombreEncuesta->valor;?> " con éxito 

<table>
	<tr>
	<td style='border: inset 0pt'>
		<form method="post" action="AgregarPreguntas.php<?php echo "?id=".$idEncuesta; ?>">
			<input type="submit" name="otro" value="Agregar Preguntas">
		</FORM>
	</td>
	<td style='border: inset 0pt'>
		<form method="get" action="CrearEncuesta.php">
			<input type="submit" name="otro" value="Agregar otra encuesta">
		</FORM>
	</td>
	</tr>
</table>

<?php } ?>

<?php include('../assets/FinDocumento.php'); ?>