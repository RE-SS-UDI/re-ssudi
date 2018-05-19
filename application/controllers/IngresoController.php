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

        $titulo = "Ressudi UTJ";
        $cuerpo = "
            Hola ".$persona->nombre.",

            <p>El modelo del navegador de Skandia plantea que el valor de mercado de una compañía  viene determinado por un capital financiero y unos valores ocultos que, en su conjunto, se denominan capital intelectual.</p>

            <p>A partir de esta consideración se proponen la manera de medirlo, teniendo en cuenta que cualquier valoración  que se haga no solo debe contener indicadores pertinentes sino también presentar esas medidas en una forma que sea fácilmente inteligible, aplicable y comparable con otras empresas. Además de llevar en conjunto a producir un significado comprensible de la capacidad futura y sostenible de producir beneficios. Así como detectar cualquier excitación de la empresa, agotamiento, tensión, debilidad y enfermedad.</p>

            <p>Para lo que se le solicita, de la manera más atenta, responder las encuestas que se encuentran en el sistema, con el usuario y contraseña de acceso que a continuación se le proporcionan.</p>
            <br><br>
 

            <p><strong>Sistema Administrativo RESSUDI</strong></p>

 

            <a href=\"http://ca02.utj.edu.mx/\">http://ca02.utj.edu.mx/</a>

            Usuario:&nbsp;".$usuario->usuario."
            <br />
            Contrase&ntilde;a:&nbsp;".$usuario->contrasena."
        ";

        echo My_Comun::envioCorreo($titulo, $cuerpo,'ressudi@utj.edu.mx','Sinergia', $usuario->correo, $persona->nombre);
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

