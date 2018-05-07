<?php $titulo="Modificar Empresa"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('../assets/Atributo.php');?>
<?php include('ValidarRFC.php');?>

<?php
    //Revisar que se envio el get id por URL
    if(empty($_GET["id"])){
?>
        <form method="get" action="ModificarEmpresa.php">
            <input type="text" name="id" placeholder="id Empresa">
            <input type="submit" value="Modificar Empresa">
        </FORM>
<?php
        return; //Termina el codigo
    }
	$idModificar = $_GET["id"];	

    //Revisar que exista dicha empresa en la base de datos
    $sql = "SELECT idEmpresa FROM empresa WHERE idEmpresa='".$idModificar."' and elimina = 0";
    $resultado = $conn->query($sql);
    if ($resultado->num_rows <= 0){
?>
        La empresa ' <?php echo $idModificar ?> ' no existe<br><br>
        <form action="ModificarEmpresa.php">
            <input type="submit" value="Modificar otro">
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
        
        //VALIDANDO QUE EL RFC SEA UNICO, IGNORANDO EL REGISTRO QUE SE ESTA EDITANDO
        $sql = "SELECT idEmpresa FROM empresa WHERE rfc='".$obj->rfc->valor."' AND idEmpresa<>'".$idModificar."' and elimina_empresa=0";
        $resultado = $conn->query($sql);
        if ($resultado->num_rows > 0 && !empty($obj->rfc->valor)){
            $obj->rfc->error = "*Este registro ya existe, ingrese otro";
        }

        //VALIDAR RFC
        if (!empty($obj->rfc->valor) && !rfcValido($obj->rfc->valor)){
            $obj->rfc->error = " *Ingrese un RFC valido";
        }
        
        //VALIDAR USUARIO
        if (!empty($obj->idUsuario->valor) && is_numeric($obj->idUsuario->valor)){
            $sql = "SELECT nombreUsuario FROM usuario WHERE idUsuario = '".$obj->idUsuario->valor."' and elimna_usuario=0";
            $resultadoUsuario = $conn->query($sql);
            if ($resultadoUsuario->num_rows <= 0){
                $obj->idUsuario->error = " *Lo sentimos, ese usuario no existe";
            } 
			else {
                //VALIDANDO QUE EL USUARIO SEA UNICO, IGNORANDO EL REGISTRO QUE SE ESTA EDITANDO
                $sql = "SELECT idEmpresa FROM empresa WHERE Usuario_idUsuario='".$obj->idUsuario->valor."' AND idEmpresa<>'".$idModificar."' and elimina_empresa=0";
                $resultado = $conn->query($sql);
                if ($resultado->num_rows > 0 && !empty($obj->idUsuario->valor)){
                    $obj->idUsuario->error = "*Este registro ya existe, ingrese otro";
                }
				$row = $resultadoUsuario->fetch_assoc();
				$nombreUsuario = $row["nombreUsuario"];
            }
                
        }
		else if(is_numeric($obj->idUsuario->valor)){
            $obj->idUsuario->error = " *Ingrese un usuario";
        }
        
        $esValido = true;
        foreach($obj as $key=>$value) {
            if(!$value->esValido()){
                $esValido = false;
                break;
            }
        }
        
        //Si todo los datos son validos y no hay repetidos
        if($esValido){
			$sql="UPDATE empresa SET rfc=?, razonSocial=?, telefono=?, calle=?, numInterior=?, numExterior=?, colonia=?, municipio=?, email=?, codigoPostal=?, Usuario_idUsuario=?, nombreEmpresa=? where idEmpresa='".$idModificar."' and elimina_empresa=0";
            $stmt = $conn->prepare($sql);
            if($stmt->bind_param("sssssssssiis", $obj->rfc->valor, $obj->razonSocial->valor, $obj->telefono->valor,
                                 $obj->calle->valor, $obj->numInterior->valor, $obj->numExterior->valor,
                                 $obj->colonia->valor, $obj->municipio->valor, $obj->email->valor,
                                 $obj->codigoPostal->valor,$obj->idUsuario->valor, $obj->nombreEmpresa->valor))
            
            if($stmt->execute())
                $registroAgregado = true;
            
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
     <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$idModificar;?>">
        
        <label>Usuario (id usuario)</label> <span class="error"><?php echo $obj->idUsuario->error;?></span>
        <input type="text" name="<?php echo $obj->idUsuario->nombre;?>" value="<?php echo $obj->idUsuario->valor;?>">
        
        <a href="../usuario/CrearUsuario.php" target="_blank">+ Ir a crear usuario</a>
        <br><br>
        
        <label>RFC</label> <span class="error"><?php echo $obj->rfc->error;?></span>
        <input type="text" name="<?php echo $obj->rfc->nombre;?>" value="<?php echo $obj->rfc->valor;?>">
        
        <label>Razon Social</label> <span class="error"><?php echo $obj->razonSocial->error;?></span>
        <input type="text" name="<?php echo $obj->razonSocial->nombre;?>" value="<?php echo $obj->razonSocial->valor;?>">
		
        <label>Nombre de la Empresa</label> <span class="error"><?php echo $obj->nombreEmpresa->error;?></span>
        <input type="text" name="<?php echo $obj->nombreEmpresa->nombre;?>" value="<?php echo $obj->nombreEmpresa->valor;?>">
        
        <label>Teléfono</label> <span class="error"><?php echo $obj->telefono->error;?></span>
        <input type="text" name="<?php echo $obj->telefono->nombre;?>" value="<?php echo $obj->telefono->valor;?>">
        
        <label>Calle</label> <span class="error"><?php echo $obj->calle->error;?></span>
        <input type="text" name="<?php echo $obj->calle->nombre;?>" value="<?php echo $obj->calle->valor;?>">
		
        <label>Número Exterior</label> <span class="error"><?php echo $obj->numExterior->error;?></span>
        <input type="text" name="<?php echo $obj->numExterior->nombre;?>" value="<?php echo $obj->numExterior->valor;?>">
        
        <label>Número Interior</label> <span class="error"><?php echo $obj->numInterior->error;?></span>
        <input type="text" name="<?php echo $obj->numInterior->nombre;?>" value="<?php echo $obj->numInterior->valor;?>">
        
        <label>Colonia</label> <span class="error"><?php echo $obj->colonia->error;?></span>
        <input type="text" name="<?php echo $obj->colonia->nombre;?>" value="<?php echo $obj->colonia->valor;?>">
        
        <label>Municipio</label> <span class="error"><?php echo $obj->municipio->error;?></span>
        <input type="text" name="<?php echo $obj->municipio->nombre;?>" value="<?php echo $obj->municipio->valor;?>">
        
        <label>E-mail</label> <span class="error"><?php echo $obj->email->error;?></span>
        <input type="text" name="<?php echo $obj->email->nombre;?>" value="<?php echo $obj->email->valor;?>">
        
        <label>Código Postal</label> <span class="error"><?php echo $obj->codigoPostal->error;?></span>
        <input type="text" name="<?php echo $obj->codigoPostal->nombre;?>" value="<?php echo $obj->codigoPostal->valor;?>">
        
        <br><br>
        <input type="submit" name="submit" value="Modificar Empresa">  
    </form>

<?php } else { ?>


Se eliminó la empresa " <?php echo $obj->razonSocial->valor;?> " del usuario " <?php echo $nombreUsuario ?> " con éxito 

<form method="post" action="MostrarEmpresas.php">
    <input type="submit" name="reload" value="Ver empresas">
</FORM>

<?php } ?>

<?php include('../assets/FinDocumento.php'); ?>