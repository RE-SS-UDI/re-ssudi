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


    public function preRegisterOnchangeEstadoAction(){
      $this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender(TRUE);
      // $estado = $_POST["estado"];

      $estado=$this->_getParam('estado');
      $filtro = "WHERE status = 1";
        
      if($estado!='')
      {
          $filtro.=" AND (estado_id = $estado) ";
      }
      // $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', $filtro, ' nombre asc');
      $zonas = My_Comun::obtenerFiltroSQL('zona', $filtro, ' nombre asc');

      echo json_encode($zonas);
    }

    public function preRegisterOnchangeZonaAction(){
      $this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender(TRUE);
      // $estado = $_POST["estado"];

      $zona=$this->_getParam('zona');
      $filtro = "WHERE status = 1";
        
      if($zona!='')
      {
          $filtro.=" AND (zona_id = $zona) ";
      }
      // $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', $filtro, ' nombre asc');
      $tipo_Pregistro = My_Comun::obtenerFiltroSQL('tipo_persona',$filtro, ' descripcion asc');
      echo json_encode($tipo_Pregistro);
    }



    public function preRegisterAction()
    {

      //  $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1 ', ' nombre asc');
       $this->view->estados = My_Comun::obtenerFiltroSQL('estados', ' WHERE status = 1 ', ' estado asc');
      //$this->view->tipo_Pregistro = My_Comun::obtenerFiltroSQL('tipo_persona', ' WHERE status = 1 ', ' descripcion asc');

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