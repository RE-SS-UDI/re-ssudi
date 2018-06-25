<?php

class IndexController extends Zend_Controller_Action{

    public function init(){
        $this->view->headScript()->appendFile('/js/front/index.js?'.time());
       
    }//function
 
    public function indexAction(){
      
    }//function
 
    public function aboutAction(){
      
    }//function

    public function contactAction(){
      
    }//function

    public function loginAction()
    {
    	
    }



    public function preRegisterAction()
    {
      $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1 ', ' nombre asc');
    }


    public function guardarPreRegistroAction()
    {
      $this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender(TRUE);
      unset($_POST['correo2']);

      $preregistroID = My_Comun::guardarSQL("pre_registro", $_POST, $_POST["id"], "");
      echo $preregistroID;    
    }

  



}

