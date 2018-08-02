<?php

class Backend_IndexController extends Zend_Controller_Action{

    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        /* Initialize action controller here */
             $this->view->headScript()->appendFile('/js/backend/index.js?'.time());


             
    }

    public function indexAction(){

       	 $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) { 
            $authns = new Zend_Session_Namespace($auth->getStorage()->getNamespace());
            $authns->setExpirationSeconds(100000); //Expira 10 segundos despues
        }  

       	$idPer =  Zend_Auth::getInstance()->getIdentity()->id;
       	$sess=new Zend_Session_Namespace('permisos');
        print_r($sess->permisos);

        $this->view->registro = My_Comun::obtenerSQL("usuario", "id", Zend_Auth::getInstance()->getIdentity()->id);        
        $this->view->persona = My_Comun::obtenerSQL("persona", "id", $this->view->registro->persona_id);

        $this->view->mensajes=My_Comun::obtenerFiltroSQLMensajes($idPer);


        $this->view->tipos_usuarios = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE 1 = 1 ', ' descripcion asc');

        $this->view->puedeVer=strpos($sess->cliente->permisos,"VER_INICIO")!==false;

    }




}

