<?php $titulo="Ver Empresa"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('../assets/Atributo.php');?>

<?php
    //Revisar que se envio el get id por URL
    if(empty($_GET["id"])){
?>
        <form method="get" action="VerEmpresa.php">
            <input type="text" name="id" placeholder="id Empresa">
            <input type="submit" value="Ver Empresa">
        </FORM>
<?php
        return; //Termina el codigo
    }
	$idModificar = $_GET["id"];	

    //Revisar que exista dicha empresa en la base de datos
    $sql = "SELECT idEmpresa FROM empresa WHERE idEmpresa='".$idModificar."' and elimina_empresa = 0";
    $resultado = $conn->query($sql);
    if ($resultado->num_rows <= 0){
?>
        La empresa ' <?php echo $idModificar ?> ' no existe<br><br>
        <form action="VerEmpresa.php">
            <input type="submit" value="Ver otro">
        </FORM>
<?php
        return; //Termina el codigo
    }

    //Nota: se revisa que el RFC y el Usuario sean unicos posteriormente
    Atributo::configurar($conn, "empresa");
    $obj = (object) array('rfc'             => new Atributo("rfc", TipoDeDato::CADENA, 13, $null=true),
                          'razonSocial'     => new Atributo("razonSocial",  TipoDeDato::CADENA , 50, $null=false),
                          'telefono'        => new Atributo("telefono",  TipoDeDato::CADENA , 10, $null=true),
                          'calle'           => new Atributo("calle",  TipoDeDato::CADENA , 20, $null=true),
                          'numInterior'     => new Atributo("numInterior",  TipoDeDato::CADENA , 5, $null=true),
                          'numExterior'     => new Atributo("numExterior",  TipoDeDato::CADENA , 5, $null=true),
                          'colonia'         => new Atributo("colonia",  TipoDeDato::CADENA , 20, $null=true),
                          'municipio'       => new Atributo("municipio",  TipoDeDato::CADENA , 20, $null=true),
                          'email'           => new Atributo("email",  TipoDeDato::EMAIL , 30, $null=true),
                          'codigoPostal'    => new Atributo("codigoPostal",  TipoDeDato::NUMERICO , 11, $null=true),
                          'idUsuario'       => new Atributo("Usuario_idUsuario",  TipoDeDato::NUMERICO , 11, $null=false),
						  'nombreEmpresa'	=> new Atributo("nombreEmpresa", TipoDeDato::CADENA, 40, $null));
    
    $registroAgregado = false;
    

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
   }
	else{
		$sql = "SELECT * FROM empresa WHERE idEmpresa = '".$idModificar."' and elimina_empresa = 0";
		$resultadoUsuario = $conn->query($sql);
		if( $resultadoUsuario->num_rows > 0 ){		
			if($row = $resultadoUsuario->fetch_assoc()) {
				$obj->rfc->valor = $row["rfc"];
				$obj->razonSocial->valor = $row["razonSocial"];
				$obj->nombreEmpresa->valor = $row["nombreEmpresa"];
				$obj->telefono->valor = $row["telefono"];
				$obj->calle->valor = $row["calle"];
				$obj->numInterior->valor = $row["numInterior"];
				$obj->numExterior->valor = $row["numExterior"];
				$obj->colonia->valor = $row["colonia"];
				$obj->municipio->valor = $row["municipio"];
				$obj->email->valor = $row["email"];
				$obj->codigoPostal->valor = $row["codigoPostal"];
				$obj->idUsuario->valor = $row["Usuario_idUsuario"];
                
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
     <form method="post" action="MostrarEmpresas.php">
                        
        <label>RFC</label><br>
        <label><?php echo $obj->rfc->valor;?></label><br><br>
        
        <label>Razón Social</label> <br>
       <label><?php echo $obj->razonSocial->valor;?></label><br><br>
	   
        <label>Nombre de la Empresa</label> <br>
       <label><?php echo $obj->nombreEmpresa->valor;?></label><br><br>
        
        <label>Teléfono</label> <br>
        <label><?php echo $obj->telefono->valor;?></label><br><br>
        
        <label>Domicilio </label><br>
        <label>
			<?php 
				echo $obj->calle->valor." #".$obj->numExterior->valor." ";
				if($obj->numInterior->valor != "" )
					echo "int ".$obj->numInterior->valor." ";
			?>
		</label><br><br>
        
        <label>Colonia</label> <br>
        <label><?php echo $obj->colonia->valor;?></label><br><br>
        
        <label>Municipio</label> <br>
        <label><?php echo $obj->municipio->valor;?></label><br><br>
        
        <label>Código Postal</label><br>
        <label><?php echo $obj->codigoPostal->valor;?></label><br><br>
		
        <label>E-mail</label> <br>
        <label><?php echo $obj->email->valor;?></label><br><br>
        
        <input type="submit" name="submit" value="Eliminar" >  
    </form>
<?php } ?>

<?php include('../assets/FinDocumento.php'); ?>