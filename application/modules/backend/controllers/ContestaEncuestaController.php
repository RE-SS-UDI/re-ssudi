<?php
class Backend_ContestaEncuestaController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/contesta-encuesta.js');
       
    }//function
 
    public function indexAction(){
        $this->view->encuestas = ContestaEncuesta::obtieneEncuestasUsuario(Zend_Auth::getInstance()->getIdentity()->id);
//        print_r($this->view->encuestas);
//        exit;
        if ($this->_getParam('encuesta') != '') {
            $this->view->num_encuesta = $this->_getParam('encuesta');
        } else {
            $this->view->num_encuesta = 0;
        }
        
        
    }//function

    public function guardarAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		
            $bitacora = array();
            $bitacora[0]["modelo"] = "Respuesta";
            $bitacora[0]["campo"] = "descripcion";
            $bitacora[0]["id"] = $_POST["id"];
            $bitacora[0]["agregar"] = "Agrega respuesta";
            $bitacora[0]["editar"] = "Editar respuesta";

//            print_r($_POST);
//exit;
            foreach ($_POST['opcion'] as $key => $value) {
                $data = array();
                $separaId = explode('_',$key);


                if (is_array($value)) {
//                    print_r('Es arreglo');
                    Respuesta::eliminaRespuestaTipo($_POST['persona_id'], $separaId[1], $separaId[2]);
                    foreach ($value as $v) {
                        $data2 = array();
                        $data2['descripcion'] = $v;
                        $data2['pregunta_id'] = $separaId[1];
                        $data2['persona_id'] = $_POST['persona_id'];
                        $data2['tipo'] = $separaId[2];
                        $data2['id'] = '';
                       $preId = My_Comun::guardarSQL("respuesta", $data2, $data2['id'], $bitacora);
//                        print_r($data2);

                    }                   

                } else {
//                    print_r('No es arreglo');
//                    $separaId = explode('_',$key);
                    $data['descripcion'] = $value;
                    $data['pregunta_id'] = $separaId[1];
                    $data['persona_id'] = $_POST['persona_id'];
                    $data['tipo'] = $separaId[2];
                    $data['id'] = $separaId[0];
//                    print_r($data);

                   $preId = My_Comun::guardarSQL("respuesta", $data, $data['id'], $bitacora);
                }
                
            }
            echo($preId);
    }//guardar
	
    function eliminarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
			
        $bitacora = array();
        $bitacora[0]["modelo"] = "Respuesta";
        $bitacora[0]["campo"] = "descripcion";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar respuesta";
        $bitacora[0]["deshabilitar"] = "Deshabilitar respuesta";
        $bitacora[0]["habilitar"] = "Habilitar respuesta";
            
//            print_r($_POST);
//            exit;
           echo Respuesta::eliminaRespuestaSeleccionada($_POST['persona_id'], $_POST['pregunta_id'], $_POST['tipo'], $_POST['valor']);

    }//function

    public function visualizarEncuestasAction()
    {
        $this->view->encuestas = ContestaEncuesta::obtieneEncuestasUsuario(Zend_Auth::getInstance()->getIdentity()->id);
    }
}//class
?>