<?php

class IngresoController extends Zend_Controller_Action
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
        $adaptador = new ZC_Auth_Adapter($_POST['correo_electronico'], $_POST['contrasena'],"1");
        $resultado = Zend_Auth::getInstance()->authenticate($adaptador);

        if(Zend_Auth::getInstance()->hasIdentity()){

            $sess = new Zend_Session_Namespace('permisos');
            $sess->cliente = Zend_Auth::getInstance()->getIdentity();
            echo $sess->cliente->id;

        }
        else{

            $usuario = $resultado->getIdentity();
            $mensajes = $resultado->getMessages();
            echo $usuario->correo_electronico.'|'.$mensajes[0];
        }

    }

    public function recuperaAction()
    {

    }

    public function recuperarAction(){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $persona = My_Comun::obtenerSQL('persona', 'correo', $_POST['correo_electronico'], ' and status = 1');
        $usuario = My_Comun::obtenerSQL('usuario', 'persona_id', $persona->id, ' and status = 1');

        // echo("<script>console.log('PHP: persona id: ".$persona->id."');</script>");


        $titulo = "Ressudi UTJ";
        $cuerpo = "
            Hola ".$persona->nombre.",

            <p><strong>Sistema Administrativo RESSUDI</strong></p>

            <a href=\"http://ca02.utj.edu.mx/\">http://ca02.utj.edu.mx/</a>

            <br />

            <p>A continuacion, usuario y contrase&ntilde;a:</p>

            
            <strong>Usuario:</strong>&nbsp;".$usuario->usuario."
            <br />
            <strong> Contrase&ntilde;a:</strong>&nbsp;".$usuario->contrasena."
        ";

        // echo("<script>console.log('PHP: usuario contra: ".$usuario->contrasena."');</script>");
        // echo("<script>console.log('PHP: usuario user: ".$usuario->usuario."');</script>");
        // echo("<script>console.log('PHP: usuario correo: ".$persona->correo."');</script>");
        
        echo My_Comun::envioCorreo($titulo, $cuerpo,'ressudi.utj@gmail.com','Sinergia', $persona->correo, $persona->nombre);

        // echo My_Comun::envioCorreo($titulo, $cuerpo,'ressudi@utj.edu.mx','Sinergia', $usuario->correo, $persona->nombre);
    }

    public function salirAction(){
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::namespaceUnset('permisos');
        Zend_Session::destroy();
        
        header("Location: http://".$_SERVER["SERVER_NAME"]);
    }

    
}

