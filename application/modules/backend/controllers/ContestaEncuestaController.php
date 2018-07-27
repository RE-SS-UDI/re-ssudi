<?php
class Backend_ContestaEncuestaController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/contesta-encuesta.js?'.time());
       
    }//function
 
    public function indexAction(){
//        $this->view->puedeObtenerTodos=strpos($sess->cliente->permisos,"PERMISOS_CONTESTA_ENCUESTA")!==false;
        // $this->view->encuestas = ContestaEncuesta::obtieneEncuestasUsuario(Zend_Auth::getInstance()->getIdentity()->id);
        $this->view->encuestas = ContestaEncuesta::obtieneEncuestasUsuarioZona(Zend_Auth::getInstance()->getIdentity()->persona_id);
        $this->view->zonasName = ContestaEncuesta::obtieneZona_UsuarioZona(Zend_Auth::getInstance()->getIdentity()->persona_id);

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
                    Respuesta::eliminaRespuestaTipo($_POST['persona_id'], $separaId[1], $separaId[2],$_POST['tipo_persona_id']);
                    foreach ($value as $v) {
                        $data2 = array();
                        $data2['descripcion'] = $v;
                        $data2['pregunta_id'] = $separaId[1];
                        $data2['persona_id'] = $_POST['persona_id'];
                        $data2['tipo'] = $separaId[2];
                        $data2['zona_id'] = $_POST['zona_ID'];
                        $data2['tipo_persona_id'] = $_POST['tipo_persona_id'];
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
                    $data['zona_id'] = $_POST['zona_ID'];
                    $data['tipo_persona_id'] = "";
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
        $sess=new Zend_Session_Namespace('permisos');
        $this->view->puedeObtenerTodos=strpos($sess->cliente->permisos,"PERMISOS_CONTESTA_ENCUESTA")!==false;
        $this->view->encuestas = ContestaEncuesta::obtieneEncuestasUsuarioZona(Zend_Auth::getInstance()->getIdentity()->persona_id);
        $this->view->zonasName = ContestaEncuesta::obtieneZona_UsuarioZona(Zend_Auth::getInstance()->getIdentity()->persona_id);

        // $this->view->encuestas = ContestaEncuesta::obtieneEncuestasUsuario(Zend_Auth::getInstance()->getIdentity()->id);
    }




    public function exportarAction(){
        ### Deshabilitamos el layout y la vista
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
          
       
        $filtro=" WHERE 1=1 ";
        $i=6;
        $data = array();
        $zona=$this->_getParam('zona');
        
       
        $i++;
        
        // $encuestas = ContestaEncuesta::obtieneEncuestasUsuarioZona(Zend_Auth::getInstance()->getIdentity()->id);
        
        $encuestas = ContestaEncuesta::obtieneEncuestas_UsuarioZonaById(Zend_Auth::getInstance()->getIdentity()->persona_id, $zona);
        // $encuestas = ContestaEncuesta::obtieneEncuestasUsuario(Zend_Auth::getInstance()->getIdentity()->persona_id);
//        $registros=  My_Comun::obtenerFiltro("Usuario", $filtro, "nombre ASC");

        ini_set("memory_limit", "130M");
        ini_set('max_execution_time', 0);

        $objPHPExcel = new My_PHPExcel_Excel();
        
        
            $columns_name = array
            (
                    "A$i" => array(
                            "name" => 'ENCUESTA',
                            "width" => 30
                            ),
                    "B$i" => array(
                            "name" => 'PREGUNTA',
                            "width" => 50
                            ),
                    "C$i" => array(
                            "name" => 'RESPUESTA',
                            "width" => 50
                            )                           
            );
        //Datos tabla
        foreach($encuestas as $encuesta)
        {


            $preguntas = Preguntas::obtienePreguntasEncuesta($encuesta->id);

            $i++;
            $data[] = array(                
                    "A$i" =>$encuesta->nombre
                    );
            
            foreach ($preguntas as $pregunta) {
                $respuesta = Respuesta::obtieneRespuesta(Zend_Auth::getInstance()->getIdentity()->persona_id, $pregunta->id, $zona, $encuesta->tipo_persona_id);
                $i++;
                if ($pregunta->tipo == 1 || $pregunta->tipo == 2 || $pregunta->tipo == 3) {
                    $data[] = array(                
                            "B$i" => $pregunta->descripcion,
                            "C$i" => $respuesta->descripcion
                            );
                } else {
                    $respuestas_usuario = '';
                    $opciones = My_Comun::obtenerFiltroSQL('opciones_pregunta',' WHERE status=1 AND pregunta_id='.$pregunta->id,' opcion ASC ');
                    foreach ($opciones as $opcion) {
                        $respuesta2 = Respuesta::obtieneRespuestaEspecial($opcion->opcion, $pregunta->id, $zona, $encuesta->tipo_persona_id);
                        if ($respuesta2->id!= ''){
                            $respuestas_usuario .= $opcion->opcion.', ';
                        }
                    }
                    $respuestas_usuario = substr($respuestas_usuario, 0, -2);
                    $data[] = array(                
                            "B$i" => $pregunta->descripcion,
                            "C$i" =>$respuestas_usuario
                            );
                }
                
            }
            $i++;
        }       
     $objPHPExcel->createExcel('Encuestas', $columns_name, $data, 10,array('rango'=>'A4:C4','texto'=>'Encuestas contestadas'));
    }


    public function exportarTodosAction()
    {
        ### Deshabilitamos el layout y la vista
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
          
       
        $filtro=" WHERE status=1 ";
        $i=6;
        $data = array();

        ini_set("memory_limit", "130M");
        ini_set('max_execution_time', 0);

        $objPHPExcel = new My_PHPExcel_Excel();
        
       
        $i++;
        $registros=  My_Comun::obtenerFiltroSQL("usuario", $filtro, "persona_id ASC");
            $columns_name = array
            (
                    "A$i" => array(
                            "name" => 'USUARIO',
                            "width" => 30
                            ),
                    "B$i" => array(
                            "name" => 'ENCUESTA',
                            "width" => 30
                            ),
                    "C$i" => array(
                            "name" => 'PREGUNTA',
                            "width" => 50
                            ),
                    "D$i" => array(
                            "name" => 'RESPUESTA',
                            "width" => 50
                            )
            );

        foreach ($registros as $registro) 
        {
            $encuestas = ContestaEncuesta::obtieneEncuestasUsuario($registro->id);
            $persona = My_Comun::obtenerSQL("persona","id",$registro->persona_id);
            $empresa = My_Comun::obtenerSQL("empresa","id",$persona->empresa_id);
            $i++;
            $data[] = array(                
                    "A$i" =>$persona->nombre
                    );
            $i++;
            $data[] = array(                
                    "A$i" =>$empresa->nombre
                    );
            $i--;
            //Datos tabla
            foreach($encuestas as $encuesta)
            {


                $preguntas = Preguntas::obtienePreguntasEncuesta($encuesta->id);

                $data[] = array(                
                        "B$i" =>$encuesta->nombre
                        );
            $i--;                
                foreach ($preguntas as $pregunta) {
                    $respuesta = Respuesta::obtieneRespuesta($registro->persona_id, $pregunta->id);
                    $i++;
                    if ($pregunta->tipo == 1 || $pregunta->tipo == 2 || $pregunta->tipo == 3) {
                        $data[] = array(                
                                "C$i" => $pregunta->descripcion,
                                "D$i" => $respuesta->descripcion
                                );
                    } else {
                        $respuestas_usuario = '';
                        $opciones = My_Comun::obtenerFiltroSQL('opciones_pregunta',' WHERE status=1 AND pregunta_id='.$pregunta->id,' opcion ASC ');
                        
                        foreach ($opciones as $opcion) {
                            $respuesta2 = Respuesta::obtieneRespuestaEspecialPersonalizado($opcion->opcion, $pregunta->id,$registro->persona_id);
                            if ($respuesta2->id!= ''){
                                $respuestas_usuario .= $opcion->opcion.', ';
                            }
                        }
                        $respuestas_usuario = substr($respuestas_usuario, 0, -2);
                        $data[] = array(                
                                "C$i" => $pregunta->descripcion,
                                "D$i" =>$respuestas_usuario
                                );
                    }
                    
                }
                $i++;
            }
            $i++;
        }
            $objPHPExcel->createExcel('Encuestas', $columns_name, $data, 10,array('rango'=>'A4:C4','texto'=>'Encuestas contestadas'));

    }
}//class
?>