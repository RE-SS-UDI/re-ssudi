<?php $titulo="Ver Zona"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('../assets/Atributo.php');?>

<?php
//Revisar que se envio el get id por URL
    if(empty($_GET["id"])){
?>
        <form method="get" action="VerZona.php">
            <input type="text" name="id" placeholder="id Zona">
            <input type="submit" value="Ver Zona">
        </FORM>
<?php
        return; //Termina el codigo
    }
	$idModificar = $_GET["id"];	

    //Revisar que exista dicha empresa en la base de datos
    $sql = "SELECT idZona FROM zona WHERE idZona='".$idModificar."' and elimina_zona = 0";
    $resultado = $conn->query($sql);
    if ($resultado->num_rows <= 0){
?>
        La zona ' <?php echo $idModificar ?> ' no existe<br><br>
        <form action="Ver Zona.php">
            <input type="submit" value="Ver otra">
        </FORM>
<?php
        return; //Termina el codigo
    } 
    Atributo::configurar($conn, "zona");
    $obj = (object) array('nombreZona' => new Atributo("nombreZona", TipoDeDato::ALFANUMERICO, 50, $null=false, $unique=true));   
	$registroAgregado = false;
	  

    //Si se recibio una respuesta por parte del formulario realiza lo siguiente:
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Vualve a cargar la pagina para agregar otro registro
        if (!empty($_POST["reload"])){
            unset($_POST["reload"]);
            header('Location:'.$_SERVER['REQUEST_URI']);
        }			
	}
	else{
		$sql = "SELECT nombreZona FROM zona where idZona = '".$idModificar."' and elimina_zona = 0";
		$resultado = $conn->query($sql);
		if( $resultado->num_rows > 0 ){		
			if($row = $resultado->fetch_assoc()) {
				$obj->nombreZona->valor = $row["nombreZona"];
				
				//Limpiar errores
                foreach($obj as $key=>$value) {
                    if(!$value->esValido()){
                        $value->error = "";
                    }
                }
			}
		}
	}

    if(!$registroAgregado){
?>
    <form method="post" action="MostrarZonas.php">
        
		
        <label>Nombre Zona</label><br>
        <label><?php echo $obj->nombreZona->valor;?></label>
                
        <br><br>
        <input type="submit" name="submit" value="Regresar" >  
    </form>

<?php }  ?>

<?php include('../assets/FinDocumento.php'); ?>