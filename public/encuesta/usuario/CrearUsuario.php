<?php $titulo="Crear Usuario"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('../assets/Atributo.php');?>
<?php
	if( $_SESSION["tipoUsuario"] == "Empresa" || $_SESSION["tipoUsuario"] == "Encuestador")
		header ("Location: ../miPerfil/miPerfil.php");
?>

<?php
    Atributo::configurar($conn, "perfil");
    $obj = (object) array('nombreUsuario' => new Atributo("nombreUsuario", TipoDeDato::PALABRAS, 15, $null=false, $unique=true),
                          'contrasenia'  => new Atributo("contrasenia",  TipoDeDato::CADENA , 20, $null=false),
                          'repetirContrasenia'  => new Atributo("repetirContrasenia",  TipoDeDato::CADENA , 20, $null=false));
    
    $perfilErr = $zonaErr = "";
    $perfilSeleccionado = $zonaSeleccionada = -1;
    $registroAgregado = false;

    //Revisar que existan perfiles y zonas en la BD
    $sql = "SELECT id, nombre FROM zona WHERE status=1";
    //$resultadoZona = $conn->query($sql);
    $resultadoZonas = array();
        $resultadoZona = sqlsrv_query( $conn, $sql);
        if( $obje = sqlsrv_fetch_object($resultadoZona)){
              if (count($obje) <= 0) {
               $perfilErr = " *Lo sentimos, no existen zonas registradas";
                $resultadoZona = '';
              }else{
                $resultadoZonas[] = $obje;
              }
        }
//    if ($resultadoZona->num_rows <= 0)
 
    $sql = "SELECT id, descripcion FROM tipo_usuario WHERE status=1";
//    $resultadoPerfil = $conn->query($sql);
        $resultadoPerfiles = array();
        $resultadoPerfil = sqlsrv_query( $conn, $sql);
        if( $obje = sqlsrv_fetch_object($resultadoPerfil)){
              if (count($obje) <= 0) {
                $perfilErr = " *Lo sentimos, no existen perfiles registrados";
                $resultadoPerfil = '';
              }else{
                $resultadoPerfiles[] = $obje;
              }
        }
//    if ($resultadoPerfil->num_rows <= 0)
//        $perfilErr = " *Lo sentimos, no existen perfiles en la BD";
    

    //Si se recibio una respuesta por parte del formulario realiza lo siguiente:
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Vualve a cargar la pagina para agregar otro registro
        if (!empty($_POST["reload"])){
            unset($_POST["reload"]);
            header('Location:'.$_SERVER['REQUEST_URI']);
        }
        
        //Conseguir los valores seleccionados de las listas
        if (!empty($_POST["Zona_idZona"])){
            $zonaSeleccionada = $_POST["Zona_idZona"];
        }
        if (!empty($_POST["Perfil_idPerfil"])){
            $perfilSeleccionado = $_POST["Perfil_idPerfil"];

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
			if( $obj->nombreUsuario->valor == "" )
				$obj->nombreUsuario->error = "*Campo obligatorio";
			else
				$esValido = true;
			
		}
        
        //Se valida la contraseña
        if($esValido){
            $esValido = false;
			if( $obj->contrasenia->valor == "" )
				$obj->contrasenia->error = "*Campo obligatorio";
            else if(strlen($obj->contrasenia->valor) < 5)
                $obj->contrasenia->error = " *Contraseña muy corta";
            else if($obj->nombreUsuario->valor == $obj->contrasenia->valor)
                $obj->contrasenia->error = " *La contraseña no debe ser igual al nombre de usuario";
			else{
				$obj->contrasenia->error = "";
				$esValido = true;
			}
			
			if( $esValido ){
				$esValido = false;
				if( $obj->repetirContrasenia->valor == "" )
					$obj->repetirContrasenia->error = "*Campo obligatorio";			
				else if($obj->contrasenia->valor != $obj->repetirContrasenia->valor)
					$obj->repetirContrasenia->error = " *La contraseña no coincide";			
				else{
					$obj->repetirContrasenia->error = "";
					$esValido = true;
				}
			}
            
        }
        
        //Si todo los datos son validos y no hay repetidos
        if($esValido){
            $stmt = $conn->prepare("INSERT INTO usuario (nombreUsuario, contrasenia, Zona_idZona, Perfil_idPerfil) VALUES (?,?,?,?)");
            if($stmt->bind_param("ssii", $obj->nombreUsuario->valor, $obj->contrasenia->valor,$_POST["Zona_idZona"],$_POST["Perfil_idPerfil"]))
            
            if($stmt->execute())
                $registroAgregado = true;
            
        }
    }

    if(!$registroAgregado){
?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label>Nombre</label>
        <input type="text" name="<?php echo $obj->nombreUsuario->nombre;?>" value="<?php echo $obj->nombreUsuario->valor;?>">
        <label>Apellido paterno</label>
        <input type="text" name="<?php echo $obj->nombreUsuario->nombre;?>" value="<?php echo $obj->nombreUsuario->valor;?>">
        <label>Apellido materno</label>
        <input type="text" name="<?php echo $obj->nombreUsuario->nombre;?>" value="<?php echo $obj->nombreUsuario->valor;?>">
        <label>Fecha de nacimiento</label>
        <input type="text" name="<?php echo $obj->nombreUsuario->nombre;?>" value="<?php echo $obj->nombreUsuario->valor;?>">
        <label>CURP</label> <span class="error"><?php echo $obj->nombreUsuario->error;?></span>
        <input type="text" name="<?php echo $obj->nombreUsuario->nombre;?>" value="<?php echo $obj->nombreUsuario->valor;?>">
        <label>RFC</label> <span class="error"><?php echo $obj->nombreUsuario->error;?></span>
        <input type="text" name="<?php echo $obj->nombreUsuario->nombre;?>" value="<?php echo $obj->nombreUsuario->valor;?>">
        <label>Genero</label> <span class="error"><?php echo $obj->nombreUsuario->error;?></span>
        <input type="text" name="<?php echo $obj->nombreUsuario->nombre;?>" value="<?php echo $obj->nombreUsuario->valor;?>">
        
        <label>Nombre Perfil</label> <span class="error"><?php echo $obj->nombreUsuario->error;?></span>
        <input type="text" name="<?php echo $obj->nombreUsuario->nombre;?>" value="<?php echo $obj->nombreUsuario->valor;?>">
        
        <label>Contraseña</label> <span class="error"><?php echo $obj->contrasenia->error;?></span><br>
        <input type="password" name="<?php echo $obj->contrasenia->nombre;?>" value="<?php echo $obj->contrasenia->valor;?>">
        
        <label>Repetir Contraseña</label> <span class="error"><?php echo $obj->repetirContrasenia->error;?></span><br>
        <input type="password" name="<?php echo $obj->repetirContrasenia->nombre;?>">
        
        <label>Zona</label> 
        <select name="Zona_idZona"> <span class="error"><?php echo $zonaErr;?></span><br>
<!--        <?php
            if ($resultadoZona->num_rows > 0) {
                // output data of each row
                while($row = $resultadoZona->fetch_assoc()) {
                    $select = $zonaSeleccionada == $row["idZona"] ? "selected" : "";
        ?>
            <option value="<?php echo $row["idZona"] ?>" <?php echo $select ?>><?php echo $row["nombreZona"] ?></option>
        <?php   }
            } ?>
 -->       <?php
                foreach($resultadoZonas as $rz) {
                    $select = $zonaSeleccionada == $rz->id ? "selected" : "";
        ?>
            <option value="<?php echo $rz->id ?>" <?php echo $select ?>><?php echo $rz->nombre ?></option>
        <?php   }
             ?>
        </select>
        
        <label>Perfil</label> <span class="error"><?php echo $perfilErr;?></span><br>
        <select name="Perfil_idPerfil">
<!--        <?php
            if ($resultadoPerfil->num_rows > 0) {
                // output data of each row
                while($row = $resultadoPerfil->fetch_assoc()) {
					$select = $perfilSeleccionado == $row["idPerfil"] ? "selected" : "";
						
					if( $_SESSION["tipoUsuario"] == "Coordinador" ){
						if( $row["nombrePerfil"] == "Empresa" || $row["nombrePerfil"] == "Encuestador" ){
							?><option value="<?php echo $row["idPerfil"] ?>" <?php echo $select ?>><?php echo $row["nombrePerfil"] ?></option><?php
						}
					}
					else{
						?><option value="<?php echo $row["idPerfil"] ?>" <?php echo $select ?>><?php echo $row["nombrePerfil"] ?></option><?php
					}
				}
			} 
		?>
-->        <?php
                // output data of each row
                foreach($resultadoPerfiles as $rp) {
                    $select = $perfilSeleccionado == $rp->id ? "selected" : "";
                        
                    if( $_SESSION["tipoUsuario"] == "Coordinador" ){
                        if( $rp->descripcion == "Empresa" || $rp->descripcion == "Encuestador" ){
                            ?><option value="<?php echo $rp->id ?>" <?php echo $select ?>><?php echo $rp->descripcion ?></option><?php
                        }
                    }
                    else{
                        ?><option value="<?php echo $rp->id ?>" <?php echo $select ?>><?php echo $rp->descripcion ?></option><?php
                    }
                }
        ?>
        </select>
        
        <br><br>
        <input type="submit" name="submit" value="Crear">  
    </form>

<?php } else { ?>

Se agregó el usuario " <?php echo $obj->nombreUsuario->valor;?> " con éxito 

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <input type="submit" name="reload" value="Agregar otro perfil">
</FORM>

<?php } ?>

<?php include('../assets/FinDocumento.php'); ?>