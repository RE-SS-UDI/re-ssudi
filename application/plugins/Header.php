<?php
  class Application_Plugin_Header extends Zend_Controller_Plugin_Abstract
  { 
	  public $module;
	  
	  public function preDispatch(Zend_Controller_Request_Abstract $request)
	  {
		 $view = new Zend_View();
		 $view->modulo = $request->getModuleName();
		 
		 $viewRenderer=new Zend_Controller_Action_Helper_ViewRenderer();
		 $viewRenderer->setView($view);
		 
		 Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
		 define("BASE_URL", $view->baseUrl());
 
	  }
	  
	  public function routeStartup(Zend_Controller_Request_Abstract $request)
	  {


	  }
  }
?>
