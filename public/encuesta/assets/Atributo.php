<?php
    // Se programo las validaciones mas comunes que el sistema pudiera tener,
    // si se desea validar algo en especifico, declare el atributo como CADENA
    // y programe la validacion independientemente (no olvide guardar el error)
    class TipoDeDato{
        const CADENA = 1;                   //Acepta cualquier caracter
        const PALABRA = 2;                  //Solo Letras (sin espacios)
        const PALABRAS = 3;                 //Letras y Espacios
        const NUMERICO = 4;                 //Solo Numeros
        const ALFANUMERICO = 6;             //Letras, Numeros y Espacios
        const NICKNAME = 5;                 //Letras y Numeros sin Espacios
        const EMAIL = 7;                    //e-mail
    }
    
    class Atributo
    {
        // Declaración de una propiedad
        public $nombre;     //Tiene que ser el nombre del atributo/columna
        public $valor;      //Aqui se guarda el valor de la variable $_POST
        public $error;      //Aqui se guarda el error generado (si es que lo hay)
        
        private $tipoDeDato;        //Tipo de dato del atributo
        private $tamano;            //Ingresa el tamano del atributo
        private $null;              //El atributo puede ser nulo
        
        // Confguracion con la tabla a la cual pertenece el atributo
        // Es necesario para la funcion existe()
        private static $tabla;      // Tabla
        private static $conn;       //  Variable de coneccion
        
        //La clase Atributo se encarga de realizar las validaciones de integracion
        //El constructor revisara que los datos que se quieran guardar respeten los
        //parametros establecidos. Si se quiere validar algo mas, como un email,
        //estas validaciones deben realizarse aparte. Aqui solo se revisa por la bd.
        function __construct($nombre, $tipoDeDato, $tamano, $null=false, $unique=false) {
            $this->nombre = $nombre;
            $this->tipoDeDato = $tipoDeDato;
            $this->null = $null;
            $this->unique = $unique;
            $this->tamano = $tamano;
            
            //Lee la variable $_POST relacionada y lo limpia
            //Si no existe, guarda una cadena vacia
            $this->valor = empty($_POST[$nombre]) ? "" : limpiarCadena($_POST[$nombre]);
            
			$this->error = "";
            
            if(!is_numeric($this->tamano)){
                $this->error = "PROGRAMADOR, INGRESE UN TAMANO VALIDO";
            }
            
            $this->validar();
        }
        
        public static function configurar($conn, $tabla){
            self::$conn = $conn;
            self::$tabla = $tabla;
        }
        
        public function esValido(){
            return $this->error == "" ? true : false;
        }
        
        private function validar(){
            if($this->valor=="" && $this->null)   return;
            
            if($this->tipoDeDato != TipoDeDato::NUMERICO && strlen($this->valor) >= $this->tamano)
                $this->error = " *La cadena es demasiada larga";
            
            if($this->error != "")  return;
            
            //Valida dependiendo del valor indicado
            switch($this->tipoDeDato){
                case TipoDeDato::CADENA: break;
                case TipoDeDato::PALABRA:
                    if($this->tieneLetras($this->valor) && strpos($this->valor, ' ') !== false)
                        $this->error = " *Solo se aceptan caracteres alfabeticos sin espacios";
                    break;
                case TipoDeDato::PALABRAS:
                    if($this->tieneLetras($this->valor))
                        $this->error = " *Solo se aceptan caracteres alfabeticos";
                    break;
                case TipoDeDato::NUMERICO:
                    if($this->esNumerico($this->valor))
                        $this->error = " *Solo se aceptan caracteres numericos";
                    break;
                case TipoDeDato::ALFANUMERICO:
                    if($this->tieneAlfanumerico($this->valor))
                        $this->error = " *Solo se aceptan caracteres alfanumericos";
                    break;
                case TipoDeDato::NICKNAME:
                    if($this->tieneAlfanumerico($this->valor) && strpos($this->valor, ' ') !== false)
                        $this->error = " *Solo se aceptan caracteres alfanumericos sin espacios";
                    break;
                case TipoDeDato::EMAIL:
                    if($this->esEmail($this->valor))
                        $this->error = " *Ingrese un email valido";
                    break;
                default:
                    echo "Yolo";
            }
            
            if($this->error != "")  return;
            
            if($this->unique)
                $this->existe("*Este registro ya existe, ingrese otro");
        }
        
        //Para poder usar existe, se debe de establecer la variable de coneccion "conn"
        //y la variable que indica la tabla en donde se relizara la busqueda.
        //Estas asignaciones deben realizarse con la funcion estatica configurar.
        //De la siguiente manera: Atributo::configurar($conn, "tabla");
        private function existe($mensaje){
            $stm = "SELECT * FROM ".self::$tabla." WHERE ".$this->nombre." = '".$this->valor."'";
//            $resultado = sqlsrv_query( $conn, $stm);
            
            if($resultado = sqlsrv_query( self::$conn, $stm)){
//            if($resultado = self::$conn->query($stm)){
//                $row_cnt = $resultado->num_rows;
                $row_cnt = count($obje);
                //$resultado->close();
                if($row_cnt != 0){
                    $this->error = $mensaje;
                    return true;
                }
            }
            return false;
        }
        
        private function imprimir(){
            echo "Nombre: ".$this->nombre."<br>";
            echo "Valor: ".$this->valor."<br>";
            echo "Error: ".$this->error."<br>";
            echo "tamano: ".$this->tamano."<br>";
            echo "null: ".$this->null."<br>";
            echo "unique: ".$this->unique."<br><br>";
        }
        
        private function esNumerico($validar){
            return is_numeric($validar) ? false : true;
        }
        
        private function esEmail($validar){
            return !filter_var($validar, FILTER_VALIDATE_EMAIL);
        }
        
        private function tieneLetras($validar){
            return preg_match("/^[a-zA-ZñÑ ]*$/",$validar) ? false : true;
        }
        
        private function tieneAlfanumerico($validar){
            return preg_match("/^[a-zA-ZñÑ0-9 ]*$/",$validar) ? false : true;
        }
        
        private function limpiarCadena($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
    }
?>