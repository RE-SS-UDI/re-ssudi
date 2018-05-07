<?php

class ZC_Auth_Adapter implements Zend_Auth_Adapter_Interface
{

    const NO_ENCONTRADO  = 'El usuario no existe';
    const CO_INCORRECTA  = 'Contraseña incorrecta';    
    const NO_DESCRIPCION = 'Ocurrió un error al tratar de ingresar al usuario, inténtelo de nuevo';

    protected $objeto;
    protected $usuario = '';
    protected $contrasena = '';
    protected $publico = '';
    
    public function __construct($_usuario, $_contrasena, $_publico="0"){

        $this->usuario = $_usuario;
        $this->contrasena = $_contrasena; 
        $this->publico = $_publico;       
    }

    public function authenticate(){

        try{
               // echo $publico; exit;
                $this->objeto = Usuario::ingresar($this->usuario,$this->contrasena);
            
            

            
            
            if(is_object($this->objeto)){

                if($this->objeto->status == "0")
                    return $this->createResult(Zend_Auth_Result::FAILURE, $this->objeto, array(self::NO_ENCONTRADO));

                if($this->objeto->contrasena != $this->contrasena)
                    return $this->createResult(Zend_Auth_Result::FAILURE, $this->objeto, array(self::CO_INCORRECTA));

                return $this->createResult(Zend_Auth_Result::SUCCESS, $this->objeto);
            }else
                return $this->createResult(Zend_Auth_Result::FAILURE, $this->objeto, array(self::NO_ENCONTRADO));
        }
        catch(Exception $e){

            return $this->createResult(Zend_Auth_Result::FAILURE, $this->objeto, array($e->getMessage()));
        }
    }

    private function createResult($codigo, $objeto, $mensajes = array()){

        return new Zend_Auth_Result($codigo, $objeto, $mensajes);
    }
}

?>