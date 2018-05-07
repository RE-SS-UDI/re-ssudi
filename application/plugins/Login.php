<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Application_Plugin_Login

 */
class Application_Plugin_Login extends Zend_Controller_Plugin_Abstract
{
    
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
	   
		$view = new Zend_View();	
    	$view->peticion = $request->isXmlHttpRequest();
    	$view->modulo = $request->getModuleName();			
		$view->accion = $this->obtenerAccion($request->getActionName());
		$layout = Zend_Layout::getMvcInstance();

	   
        if($view->modulo == 'backend')
		{           				
            if( Zend_Auth::getInstance()->hasIdentity() )
			{				
				$view->controlador = $this->obtenerPermiso($request->getControllerName(), $request->getModuleName() );		
				 
            	$layout->setLayout('backend');           
                
				if($view->controlador=="login"){ 
                    header("Location: /backend");
       			}
            }
            else{	
                $layout->setLayout('ingreso');                            
            }	
		}
		
		else if($view->modulo == 'default')
		{           				
            if(Zend_Auth::getInstance()->hasIdentity() )
			{				
					
				$view->controlador = $this->obtenerPermiso($request->getControllerName(), $request->getModuleName() );		
				 
            	$layout->setLayout('default');           
                
				if($view->controlador=="index"){ 
                    header("Location: /index");
       			}
            }
            else{	
                $layout->setLayout('publico');                            
            }	
		}
		
		/*else{			
            $layout->setLayout('index/index');	
		}*/
	
		$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
		$viewRenderer->setView($view);

		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }
    
    private function  obtenerPermiso($controlador, $modulo){		
		
		if($modulo == 'backend'){     
    		if(My_Comun::tienePermiso("VER_" . strtoupper(str_replace("-", "_", $controlador))) == "0" 
    			&& $controlador != "index"  
				&& $controlador != "comun" 
    			&& $controlador != "login" 
			)
    		header("Location: /SIN-PERMISO");
		}
	
    	return $controlador;
    }
    
    private function  obtenerAccion($accion){

        switch($accion){

            case "index"    : return "ver"; break;
            case "agregar"  : return "agregar"; break;
            case "eliminar" : return "eliminar"; break;
            case "exportar" : return "ver"; break;
            default         : return $accion; break;	
		}
    }
}?>