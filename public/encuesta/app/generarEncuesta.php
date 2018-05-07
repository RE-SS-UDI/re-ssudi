<?php include('../assets/Atributo.php');?>
<?php
session_start();
    $usuario = 3;

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "encuestas2";
	
	

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    

    if(!empty($_GET["abrir"]))
        $encuesta = $_GET["abrir"];
    else if (!empty($_SESSION["_encuesta"]))
        $encuesta = $_SESSION["_encuesta"];
    
    //Si el usuario pide abrir una nueva encuesta
    if(isset($encuesta) && existeEncuesta($encuesta) && permiteContestar($encuesta)){
        //Limpia y carga las variables de sesion
        session_unset();
        $_SESSION["_encuesta"] = $encuesta;
        $_SESSION["_listaCategorias"] = generarListaCategorias($_SESSION["_encuesta"]);
        $_SESSION["_listaPregutnas"] = generarListaPreguntas($_SESSION["_encuesta"]);
        reset($_SESSION["_listaCategorias"]);
        $_SESSION["_categoria"] = key($_SESSION["_listaCategorias"]);
        $_SESSION["_listaCatPre"] = generarListaPreguntas($_SESSION["_encuesta"], $_SESSION["_categoria"]);
        reset($_SESSION["_listaPregutnas"]);
        $_SESSION["_pregunta"] = key($_SESSION["_listaPregutnas"]);
    }else{
        //no hay una encuesta seleccionada
        header("Location: ../404.php");
        exit;
    }
    
    $categorias = $_SESSION["_listaCategorias"];
    $preguntas = $_SESSION["_listaPregutnas"];
    
    if(!empty($_GET["categoria"]) && existeCategoria(getIdCategoria($_GET["categoria"])))
        abrirCategoria(getIdCategoria($_GET["categoria"]));

    $catpre = $_SESSION["_listaCatPre"];
    
    if(!empty($_GET["pregunta"]) && existePregunta(getIdPregunta($_GET["pregunta"]))){
        $_SESSION["_pregunta"] = getIdPregunta($_GET["pregunta"]);
    }
        

    $encuesta = $_SESSION["_encuesta"];
    $nombreEncuesta = getNombreEncuesta($encuesta);
    $categoria = $_SESSION["_categoria"];
    $pregunta = $_SESSION["_pregunta"];
    $numeroPregunta = array_search($pregunta, array_keys($preguntas));

    $mensaje = "";
    
    //var_dump($catpre);
    //var_dump($preguntas);

    function abrirCategoria($categoria){
        if(existeCategoria($categoria) && $categoria != $_SESSION["_categoria"]){
            $_SESSION["_categoria"] = $categoria;
            $_SESSION["_listaCatPre"] = generarListaPreguntas($_SESSION["_encuesta"], $_SESSION["_categoria"]);
            reset($_SESSION["_listaCatPre"]);
            $_SESSION["_pregunta"] = key($_SESSION["_listaCatPre"]);
            //var_dump($_SESSION["_listaCatPre"]);
        }
    }

    function getPorcentaje(){
        global $conn;
        global $preguntas;
        global $encuesta;
        global $usuario;
        $totalPreguntas = count($preguntas);
        
        $sql = "SELECT idRespuesta FROM respuesta WHERE Encuesta_idEncuesta = '".$encuesta."' 
        AND elimina_respuesta = 0 AND Empresa_idEmpresa = ".$usuario;
        $row = $conn->query($sql);
        $contestados =  $row->num_rows;
        
        $porcentaje = ($contestados * 100) / $totalPreguntas;
        return round($porcentaje);
    }

    function getIdPregunta($posicion){
        global $catpre;
        $i = 1;
        foreach($catpre as $key=>$value)
            if($i++ == $posicion)
                return $key;
        return -1;
    }

    function getIdCategoria($posicion){
        global $categorias;
        $i = 1;
        foreach($categorias as $key=>$value)
            if($i++ == $posicion)
                return $key;
        return -1;
    }

    function getPosPregunta($idPregunta){
        global $catpre;
        $i = 1;
        foreach($catpre as $key=>$value){
            if($key == $idPregunta)
                return $i;
            $i++;
        }
        return -1;
    }

    function getPosCategoria($idCategoria){
        global $categorias;
        $i = 1;
        foreach($categorias as $key=>$value){
            if($key == $idCategoria)
                return $i;
            $i++;
        }
        return -1;
    }
    
    function getNombreEncuesta($encuesta){
        global $conn;
        
        $sql = "SELECT nombreEncuesta FROM encuesta WHERE idEncuesta = ".$encuesta;
        $rasultadoPregunta = $conn->query($sql);
        
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            $encuesta = $result->fetch_assoc();
            return $encuesta["nombreEncuesta"];
        }
        return "Encuesta";
    }

    function existeEncuesta($encuesta){
        global $conn;
        
        $sql = "SELECT idEncuesta FROM encuesta WHERE idEncuesta = ".$encuesta;
        $rasultadoPregunta = $conn->query($sql);
        
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            return true;
        }
        return false;
    }

    function existePregunta($pregunta){
        global $preguntas;
        if(isset($preguntas[$pregunta]))
            return true;
        return false;
    }

    function existeCategoria($categoria){
        global $categorias;
        if(isset($categorias[$categoria]))
            return true;
        return false;
    }
    
    //Si no se proporsiona la categoria, se muestran las preguntas de toda la encuesta
    //Llave: idpregunta Valor: idCategoria
    function generarListaPreguntas($encuesta, $categoria = ""){
        global $conn;
        $preguntas = array();
        $where = "";
        
        if(!empty($categoria)){
            $where = " WHERE pregunta.Categoria_idCategoria = ".$categoria;
        }
        
        $sql = "SELECT  pregunta.idpregunta, 
                        categoria.idCategoria 
                FROM pregunta
                INNER JOIN categoria
                ON pregunta.Categoria_idCategoria = categoria.idCategoria
                INNER JOIN tipo
                ON pregunta.Tipo_idTipo = tipo.idTipo
                INNER JOIN pregunta_encuesta
                ON pregunta.idpregunta = pregunta_encuesta.Pregunta_idPregunta 
                   AND pregunta_encuesta.Encuesta_idEncuesta = ".$encuesta.$where; 
        $rasultadoPregunta = $conn->query($sql);

        if ($rasultadoPregunta->num_rows > 0) {
            // output data of each row
            while($pregunta = $rasultadoPregunta->fetch_assoc()) {
                $preguntas[(int)$pregunta["idpregunta"]]=(int)$pregunta["idCategoria"];
            }
        } else {
            echo "No existen preguntas";
        }
        return $preguntas;
    }
    
    //Llave: idCategoria Valor: nombreCategoria
    function generarListaCategorias($encuesta){
        global $conn;
        $categorias = array();
        
        $sql = "SELECT DISTINCT  categoria.idCategoria, categoria.nombreCategoria
                FROM pregunta
                INNER JOIN categoria
                ON pregunta.Categoria_idCategoria = categoria.idCategoria
                INNER JOIN tipo
                ON pregunta.Tipo_idTipo = tipo.idTipo
                INNER JOIN pregunta_encuesta
                ON pregunta.idpregunta = pregunta_encuesta.Pregunta_idPregunta 
                   AND pregunta_encuesta.Encuesta_idEncuesta = ".$encuesta;
        $rasultadoPregunta = $conn->query($sql);

        if ($rasultadoPregunta->num_rows > 0) {
            // output data of each row
            while($pregunta = $rasultadoPregunta->fetch_assoc()) {
                $categorias[$pregunta["idCategoria"]]=$pregunta["nombreCategoria"];
            }
        }
        
        return $categorias;
    }

    function irSiguiente(){
        
        /*if(!empty($_POST["accion"]) && $_POST["accion"] == "anterior")
            irAnterior();
        if(!empty($_POST["accion"]) && $_POST["accion"] == "guardarSalir"){
            header("Location: ../404.php");
            exit;
        }*/
          
        
        global $catpre;
        global $pregunta;
        global $categorias;
        global $categoria;
        $posPreActual = getPosPregunta($pregunta);
        $posCatActual = getPosCategoria($categoria);
        
        if(count($catpre) == $posPreActual){
                header('Location: '.$_SERVER['PHP_SELF']."?categoria=".($posCatActual+1));
                die;
        } else {
            header('Location: '.$_SERVER['PHP_SELF']."?pregunta=".($posPreActual+1));
            die;
        }
    }

    function irAnterior(){
        global $encuesta;
        global $catpre;
        global $pregunta;
        global $categorias;
        global $categoria;
        $posPreActual = getPosPregunta($pregunta);
        $posCatActual = getPosCategoria($categoria);
        
        
        if($posPreActual == 1){

            if($posCatActual == 1){
                
                $up = count(generarListaPreguntas($encuesta,getIdCategoria($posCatActual-1)));
                header('Location: '.$_SERVER['PHP_SELF']."?categoria=".($posCatActual-1)."&pregunta=".$up);
                die;
            }else{
                header('Location: '.$_SERVER['PHP_SELF']."?pregunta=".($posPreActual-1));
            die;
            }
        } else {
            header('Location: '.$_SERVER['PHP_SELF']."?pregunta=".($posPreActual-1));
            die;
        }
    }

    function dibujarNavegacion(){
        dibujarAnterior();
        echo " ";
        dibujarSiguiente();
    }

    function dibujarGuardarTermina(){
        global $catpre;
        global $pregunta;
        global $categorias;
        global $categoria;
        $posPreActual = getPosPregunta($pregunta);
        $posCatActual = getPosCategoria($categoria);
        
        
        echo '<button type="submit" name="accion" value="guardarSalir" class="btn btn-default pull-right">Guardar y Salir</button>';
        if(count($catpre) == $posPreActual && count($categorias) == $posCatActual){
            echo '<button type="submit" name="accion" value="terminar" class="btn btn-default pull-right">Terminar</button>';
        }
    }
    
    function dibujarSiguiente(){
        global $catpre;
        global $pregunta;
        global $categorias;
        global $categoria;
        $posPreActual = getPosPregunta($pregunta);
        $posCatActual = getPosCategoria($categoria);
        
        
        if(count($catpre) == $posPreActual){
            if(count($categorias) != $posCatActual)
            echo '<button type="submit" class="btn btn-default">Siguiente Categoria -></button>';
        } else {
            echo '<button type="submit" class="btn btn-default">Siguiente</button>';
        }
    }

    function dibujarAnterior(){
        global $encuesta;
        global $catpre;
        global $pregunta;
        global $categorias;
        global $categoria;
        $posPreActual = getPosPregunta($pregunta);
        $posCatActual = getPosCategoria($categoria);
        
        
        if($posPreActual == 1){
            if($posCatActual != 1){
                echo '<button type="submit" name="accion" value="anterior" class="btn btn-default">Anterior</button>';
            }
        } else {
            echo '<button type="submit" name="accion" value="anterior" class="btn btn-default">Anterior</button>';
        }
    }
    
    function dibujarPregunta($idpregunta){
        global $conn;
        $Tipo_idTipo = "";
        $descripcionPregunta = "";
        
        $sql = "SELECT Tipo_idTipo, descripcionPregunta FROM pregunta WHERE idpregunta = ".$idpregunta;
        $rasultadoPregunta = $conn->query($sql);
        
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            $data = $result->fetch_assoc();
            $Tipo_idTipo = $data ["Tipo_idTipo"];
            $descripcionPregunta = $data ["descripcionPregunta"];
        }
        
        switch($Tipo_idTipo){
            case 1:     //Verdadero/Falso
                dibujarVerdaderoFalso($idpregunta, $descripcionPregunta);
                break;
            case 2:     //Opción múltiple
                dibujarOpcionMultiple($idpregunta, $descripcionPregunta);
                break;
            case 3:     //Abierta
                dibujarPreguntaAbierta($idpregunta, $descripcionPregunta);
                break;
            default:
                echo "ERROR FATAL";
        }
    }

    
    
    function dibujarPreguntaAbierta($idpregunta, $descripcionPregunta){
        global $conn;
        $descripcionRespuesta = ResponderPregunta($idpregunta);

        echo    '<div class="form-group">
                    <label>'.utf8_decode($descripcionPregunta).'</label>'.$descripcionRespuesta->error.'
                    <textarea class="form-control" name="'.$descripcionRespuesta->nombre.'" rows="4">'.$descripcionRespuesta->valor."".'</textarea>
                </div>';
    }
    
    function dibujarVerdaderoFalso($idpregunta, $descripcionPregunta){
        global $conn;
        $descripcionRespuesta = ResponderPregunta($idpregunta);
        
        //echo "VALOR - ".$descripcionRespuesta->valor;
        //echo "VALOR - ".$descripcionRespuesta->valor;
        
        $checkedVerdadero = ($descripcionRespuesta->valor == "Verdadero") ? "checked" : "";
        $checkedFalso = ($descripcionRespuesta->valor == "Falso") ? "checked" : "";
        echo    '<label>'.utf8_decode($descripcionPregunta).'</label>
                <div class="radio">
                    <label><input type="radio" name="'.$descripcionRespuesta->nombre.'" value="Verdadero" '.$checkedVerdadero.'>Verdadero</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="'.$descripcionRespuesta->nombre.'" value="Falso" '.$checkedFalso.'>Falso</label>
                </div>';
    }
    
    function dibujarOpcionMultiple($idpregunta, $descripcionPregunta){
        global $conn;
        $descripcionRespuesta = ResponderPregunta($idpregunta);
        
        //ARMAR LA RESPUESTA
        $sql = "SELECT opcion.descripcionOpcion
                FROM opcion
                INNER JOIN pregunta_opcion
                ON opcion.idOpcion = pregunta_opcion.Opcion_idOpcion
                WHERE pregunta_opcion.Pregunta_idPregunta = ".$idpregunta;
        $rasultadoPregunta = $conn->query($sql);
        
        echo    '<label>'.utf8_decode($descripcionPregunta).'</label> ';
        echo    '<label>'.$descripcionRespuesta->error.'</label><br>';
        
        if ($rasultadoPregunta->num_rows > 0) {
            while($pregunta = $rasultadoPregunta->fetch_assoc()) {
                $checked = ($descripcionRespuesta->valor == $pregunta["descripcionOpcion"]) ? "checked" : "";
                echo    '<div class="radio">
                            <label><input type="radio" name="'.$descripcionRespuesta->nombre.'" value="'.$pregunta["descripcionOpcion"].'" '.$checked.'>'.utf8_decode($pregunta["descripcionOpcion"]).'</label>
                        </div>';
            }
        } else {
            echo "No existen opciones";
        }
    }

    function ResponderPregunta($idpregunta){
        global $conn;
        global $usuario;
        global $encuesta;
        global $categoria;
        
        Atributo::configurar($conn, "respuesta");
        $descripcionRespuesta = new Atributo("descripcionRespuesta", TipoDeDato::CADENA, 300, $null=false);
        $msjError = " <<< ERROR FATAL >>>";
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            /*if(!empty($_POST["accion"]) && $_POST["accion"] == "terminar")
                terminar();*/
            if($descripcionRespuesta->valor=="")
                irSiguiente();
            
            
            $sql = "SELECT * FROM respuesta WHERE Pregunta_idPregunta = '".$idpregunta."' 
                    AND elimina_respuesta = 0 AND Empresa_idEmpresa = ".$usuario;
            $resultadoUsuario = $conn->query($sql);
            
            //Si ya existe una respuesta, actualizala
            if( $resultadoUsuario->num_rows == 1){
                $respuesta = $descripcionRespuesta->valor;
                $sql = "UPDATE respuesta SET descripcionRespuesta='".$respuesta."', fechaRespuesta=now()
                        WHERE Pregunta_idPregunta=".$idpregunta." AND Empresa_idEmpresa=".$usuario;
                
                if ($conn->query($sql) === TRUE) 
                    irSiguiente();
                else
                    $descripcionRespuesta->error = $msjError;
            } else {
                //Si aun no hay respuesta a una pregunta, creala
                $stmt = $conn->prepare("INSERT INTO respuesta 
                (Pregunta_idPregunta, Empresa_idEmpresa, Encuesta_idEncuesta, Categoria_idCategoria, descripcionRespuesta, fechaRespuesta) 
                VALUES (?,?,?,?,?,now())");
                
                
                //Si todo los datos son validos y no hay repetidos
                if($descripcionRespuesta->esValido()){
                    
                    if($stmt->bind_param("iiiis", 
                                         $idpregunta,
                                         $usuario,
                                         $encuesta,
                                         $categoria,
                                         $descripcionRespuesta->valor)){
                        if($stmt->execute())
                            irSiguiente();
                    }
                }
                $descripcionRespuesta->error = $msjError;
            }
        } else {
            //Extraer de la bd la respuesta de una pregunta
            $sql = "SELECT descripcionRespuesta FROM respuesta WHERE Pregunta_idPregunta = '".$idpregunta."' 
                    AND elimina_respuesta = 0 AND Empresa_idEmpresa = ".$usuario;
            $resultadoUsuario = $conn->query($sql);
            if( $resultadoUsuario->num_rows > 0){
                if($row = $resultadoUsuario->fetch_assoc()) {
                    $descripcionRespuesta->valor = $row["descripcionRespuesta"];
                    $descripcionRespuesta->error = "";      //Limpiar errores
                    /*echo "row - ".$descripcionRespuesta->valor."<br>";
                    echo "Pregunta: ".$idpregunta."<br>";
                    echo "Empresa : ".$usuario."<br>";
                    echo "Encuesta: ".$encuesta."<br>";
                    echo "Categoria: ".$categoria."<br>";*/
                }
            }
        } 
        return $descripcionRespuesta;
    }

    function permiteContestar($encuesta){
        global $usuario;
        global $conn;
        $sql = "SELECT  fechaFin FROM encuesta_empresa WHERE Encuesta_idEncuesta = '".$encuesta."' 
                AND Empresa_idEmpresa = ".$usuario;
        $result = $conn->query($sql);
        if( $result->num_rows > 0){
                return true;
        }
        return false;
    }
            
        

    function terminar(){
        global $mensaje;
        global $encuesta;
        global $conn;
        global $usuario;
        if(getPorcentaje() != 100){
            $mensaje = "Debe terminar la encuesta para poder terminar";
        } else {
            $sql = "UPDATE encuesta_empresa SET fechaFin=now()
                        WHERE Encuesta_idEncuesta=".$encuesta." AND Empresa_idEmpresa=".$usuario;
            if ($conn->query($sql) === TRUE) 
                $mensaje = "Yey";
        }
    }
    
?>