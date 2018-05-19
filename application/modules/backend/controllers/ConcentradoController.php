<?php
class Backend_ConcentradoController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/concentrado.js?'.time());
       
    }//function
 
    public function indexAction(){
    	$sess=new Zend_Session_Namespace('permisos');
    	//$this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_CATEGORIA")!==false;

        $idPer = Zend_Auth::getInstance()->getIdentity()->id;
        $this->view->zonaUser = My_Comun::obtenerZonas($idPer);
        $this->view->tipoUser = My_Comun::obtenertipoUSer($idPer);

        //Verificamos el tipo d usurio
        if(Zend_Auth::getInstance()->getIdentity()->tipo_usuario == 3){
            $this->view->categorias = My_Comun::obtenerFiltroSQL('categoria', 'where status = 1', 'nombre asc');
        }else {

            $this->view->categorias = My_Comun::obtenerFiltroSQLCategorias($this->view->zonaUser[0]->id);
        }

    }//function

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        $filtro=" 1=1 ";

        $nombre=$this->_getParam('nombre');
        $status=$this->_getParam('status');
        
        
        if($this->_getParam('status')!="")
            $filtro.=" AND status=".$this->_getParam('status');
        
        if($nombre!='')
        {
            $nombre=explode(" ", trim($nombre));
            for($i=0; $i<=$nombre[$i]; $i++)
            {
                $nombre[$i]=trim(str_replace(array("'","\"",),array("�","�"),$nombre[$i]));
        		if($nombre[$i]!="")
                    $filtro.=" AND (c.nombre LIKE '%".$nombre[$i]."%') ";
            }//for
        }//if



        $consulta = "SELECT c.*
                      FROM categoria c
                      WHERE ".$filtro;
    
        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
    	$i=0;

        $editar = My_Comun::tienePermiso("EDITAR_CATEGORIA");
    	$eliminar = My_Comun::tienePermiso("ELIMINAR_CATEGORIA");
            
        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                
                $grid[$i]['nombre'] =$registros['registros'][$k]->nombre;
                $grid[$i]['descripcion'] =$registros['registros'][$k]->descripcion;
                $grid[$i]['status']=(($registros['registros'][$k]->status)?'Habilitado':'Inhabilitado');
               
            if($registros['registros'][$k]->status == 0)
            {
                $grid[$i]['editar'] = '<i class="boton fa fa-pencil fa-lg text-danger"></i>';
                
                if($eliminar)
                    $grid[$i]['eliminar'] = '<span onclick="eliminar('.$registros['registros'][$k]->id.','.$registros['registros'][$k]->status.');" title="Eliminar"><i class="boton fa fa-times-circle fa-lg azul"></i></span>';
                else
                    $grid[$i]['eliminar'] = '<i class="boton fa fa-times-circle text-danger fa-lg "></i>';
            }
            else
            {
                    
                if($editar){
                    $grid[$i]['editar'] = '<span onclick="agregar(\'/backend/categoria/agregar\','.$registros['registros'][$k]->id.', \'frmCategoria\',\'Edición de Categoría\' );" title="Editar"><i class="boton fa fa-pencil fa-lg azul"></i></span>';
                }
                else{
                    $grid[$i]['editar'] = '<i class="boton fa fa-pencil fa-lg text-danger"></i>';
                }

                if($eliminar){
                    $grid[$i]['eliminar'] = '<span onclick="eliminar('.$registros['registros'][$k]->id.','.$registros['registros'][$k]->status.');" title="Deshabilitar / Habilitar"><i class="boton fa fa-times-circle fa-lg azul"></i></i></span>';
                }
                else{
                    $grid[$i]['eliminar'] = '<i class="boton fa fa-times-circle fa-lg text-danger"></i>';						
                }
            }
    				
            $i++;
    	}//foreach
    	My_Comun::armarGrid($registros,$grid);
    }//function
    
    public function encuestaAction(){
        $this->_helper->layout->disableLayout();
        $this->view->llave = My_Comun::aleatorio(20);
		
            $this->view->encuesta=Encuesta::obtieneEncuesta($_POST["persona_id"], $_POST["encuesta_id"]);
            $this->view->persona_seleccionada = $_POST["persona_id"];
            //print_r($this->view->encuesta);
    }//function

    public function tablaAction()
    {
        $this->_helper->layout->disableLayout();

        $nombre=$this->_getParam('nombre');
        $categoria=$this->_getParam('categoria');

        $filtro_nombre = '';
        $filtro_categoria = ' ';
        $filtro_zona = '';

        if ($nombre != '') {
            $filtro_nombre = " AND concat(p.nombre, ' ', p.apellido_pat, ' ', p.apellido_mat) LIKE '%".$nombre."%' ";
        }

        if ($categoria != '') {
            $filtro_categoria = ' AND e.categoria_id = '.$categoria;
        }

        $idPer = Zend_Auth::getInstance()->getIdentity()->id;
        $this->view->zonaUser = My_Comun::obtenerZonas($idPer);


        //Verificamos el tipo d usurio
        if(Zend_Auth::getInstance()->getIdentity()->tipo_usuario != 3){
            $zona = Usuario::obtieneZonaUsuario(Zend_Auth::getInstance()->getIdentity()->id);
            $filtro_zona .= " and zona_id = ".$zona->id." ";
        }
        $this->view->encuestas = My_Comun::obtenerFiltroSQLConcentradoEncuestas($filtro_zona,$filtro_categoria);
        $this->view->personas = My_Comun::obtenerFiltroSQLConcentrado($filtro_zona,$filtro_nombre);
        //print_r($this->view->encuestas);
    }

    public function exportarAction(){
        ### Deshabilitamos el layout y la vista
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
          
        $persona_id=$this->_getParam('persona');
       
        $filtro=" 1=1 ";
        $i=6;
        $data = array();
        
       
        $i++;

        $encuestas = Encuesta::obtieneEncuestasPersona($persona_id);
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
                $respuesta = Respuesta::obtieneRespuesta($persona_id, $pregunta->id);
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
                        $respuesta2 = Respuesta::obtieneRespuestaEspecial($opcion->opcion, $pregunta->id);
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
        $objPHPExcel->createExcel('Encuesta', $columns_name, $data, 10,array('rango'=>'A4:C4','texto'=>'Encuesta contestada'));
    }

}//class
?>