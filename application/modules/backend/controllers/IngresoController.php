<?php

class Backend_IngresoController extends Zend_Controller_Action
{

    public function init(){

        $this->view->headScript()->appendFile('/js/backend/ingreso.js');
    }

    public function indexAction(){
        
    }

    public function ingresarAction(){
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        ### mandamos un parametro extra para indicar que se esta ingresando desde la parte publica
        $adaptador = new ZC_Auth_Adapter($_POST['usuario'], $_POST['contrasena'],"0");
        $resultado = Zend_Auth::getInstance()->authenticate($adaptador);

        if(Zend_Auth::getInstance()->hasIdentity()){

            $sess = new Zend_Session_Namespace('permisos');
            $sess->cliente = Zend_Auth::getInstance()->getIdentity();
            echo $sess->cliente->id;

            //Bitacora::Guardar(1, "Bitácora", "Inicio de sesión", "Acceso");
        }
        else{

            $usuario = $resultado->getIdentity();
            $mensajes = $resultado->getMessages();
            echo $usuario->correo_electronico.'|'.$mensajes[0];
        }

    }

    public function salirAction(){
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::namespaceUnset('permisos');
        Zend_Session::destroy();
        
        header("Location: http://".$_SERVER["SERVER_NAME"]);
    }

    public function recuperaAction(){
        //se ejecuta el controlador para obtener la vista,
        // y por javascript se hace un submit para ejecutar recuperarAction
        
        $this->_helper->layout->disableLayout();
        //header("Location: /backend");
    }

    public function recuperarAction(){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $usuario = My_Comun::obtener('Usuario', 'correo_electronico', $_POST['correo_electronico'], ' and status = 1');
        if(is_object($usuario)){
            $titulo = "Recuperar contraseña";
            $cuerpo = "
            Usuario:&nbsp;".$usuario->correo_electronico."
            <br />
                Contrase&ntilde;a:&nbsp;".$usuario->contrasena."";
        
              echo My_Comun::correoElectronico($titulo, $cuerpo, $correoElectronico, $nombre, $copia, $adjunto, $para);     
        }
    }
    
}

