<?php
class Backend_ConcentradoController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/concentrado.js?'.time());
       
    }//function
 
    public function indexAction(){
    	$sess=new Zend_Session_Namespace('permisos');
    	//$this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_CATEGORIA")!==false;

        $idPer = Zend_Auth::getInstance()->getIdentity()->persona_id;
        $this->view->zonaUser = My_Comun::obtenerZonas($idPer);
        $this->view->tipoUser = My_Comun::obtenertipoUSer(Zend_Auth::getInstance()->getIdentity()->id);

        $this->view->encu = My_Comun::obtenerFiltroSQL('encuesta', 'where status = 1', 'nombre asc');
        $this->view->estados = Usuario::obtieneestadosZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);

        // $this->view->cate = My_Comun::obtenerFiltroSQL('categoria', 'where status = 1', 'nombre asc');


        $this->view->zonasName = ContestaEncuesta::obtieneZona_UsuarioZona(Zend_Auth::getInstance()->getIdentity()->persona_id);


        //Verificamos el tipo d usurio
        // if(Zend_Auth::getInstance()->getIdentity()->tipo_usuario == 3){
        //     $this->view->categorias = My_Comun::obtenerFiltroSQL('categoria', 'where status = 1', 'nombre asc');
        // }else {

        //     $this->view->categorias = My_Comun::obtenerFiltroSQLCategorias($this->view->zonaUser[0]->id);
        //     // $this->view->categorias = My_Comun::obtenerFiltroSQL('categoria', ' WHERE status = 1 ', ' nombre asc');

        // }

    }//function

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        $filtro=" 1=1 ";

        $nombre=$this->_getParam('nombre');
        $status=$this->_getParam('status');
        $cateEn=$this->_getParam('encuestaCat');
        
        
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

        if($cateEn!='')
        {
            $filtro.=" AND (c.id = '".$cateEn."') ";
        }



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

        $persona_id=$this->_getParam('persona_id');
        $encuesta_id=$this->_getParam('encuesta_id');
        $zona_id=$this->_getParam('zona_id');
        $tipo_id=$this->_getParam('tipo_id');
        
        echo("<script>console.log('PHP: default - obtiene encuesta: persona_id: ".$persona_id." encuesta_id: ".$encuesta_id." zona_id: ".$zona_id." tipo_id: ".$tipo_id."');</script>");


        $this->view->encuesta=Encuesta::obtieneEncuesta( $persona_id, $encuesta_id,$zona_id, $tipo_id);
        $this->view->zona_seleccionada = $zona_id;
        $this->view->tipo_seleccionado =$tipo_id;
        $this->view->persona_seleccionada = $persona_id;
            // $this->view->encuesta=Encuesta::obtieneEncuesta($_POST["persona_id"], $_POST["encuesta_id"]);
            // $this->view->persona_seleccionada = $_POST["persona_id"];
            //print_r($this->view->encuesta);
    }//function

    public function tablaAction()
    {
        $this->_helper->layout->disableLayout();
        
        $nombre=$this->_getParam('nombre');
        
        $estado = $this->_getParam('estado_id');
        $zona = $this->_getParam('zona_id');
        $tipo = $this->_getParam('tipo_id');
        $categoria=$this->_getParam('categoria_id');
        
        $filtro_nombre = '';
        $join_zona = '';

        $filtro_estado = '';
        $filtro_zona = '';
        $filtro_tipo = '';
        $filtro_tipo_encuesa = '';
        $filtro_categoria = ' ';
        $filtro_categoria_per = ' ';
       

        if ($nombre != '') {
            $filtro_nombre = " AND concat(p.nombre, ' ', p.apellido_pat, ' ', p.apellido_mat) LIKE '%".$nombre."%' ";
        }

        if($estado!='')
        {
            $filtro_estado.=' AND zo.estado_id = '.$estado;
        }

        if($tipo!='')
        {
            $filtro_tipo.=' AND uz.tipo_persona_id = '.$tipo;
            $filtro_tipo_encuesta.=' AND ze.tipo_id = '.$tipo;
        }
        else{
            $filtro_tipo_encuesta.=' AND ze.tipo_id = 0';
        }

        if ($categoria != '') {
            $filtro_categoria = ' AND e.categoria_id = '.$categoria; 
        }

        // $idPer = Zend_Auth::getInstance()->getIdentity()->id_persona;
        // $this->view->zonaUser = Usuario::obtieneZonasXususario($idPer); 


        //Verificamos el tipo d usurio
        // if(Zend_Auth::getInstance()->getIdentity()->tipo_usuario != 3){
            
            if ($zona!=''){
                $join_zona .=" join zona_encuesta ze on e.id = ze.encuesta_id "; //agrega join para zonas en encuestas
                $filtro_zona .= " AND ze.zona_id = ".$zona; //obtiene zona de zona
                $filtro_usuario_zona .= " AND uz.zona_id = ".$zona;
            }else{
                // $zona = Usuario::obtieneZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);
                
                // foreach($zona as $zonas){
                     $filtro_usuario_zona .= " or p.id = ".$zonas->id." "; //obtiene zid persona de persona_zona
                    //  $filtro_zona .= " or ze.zona_id = ".$zonas->zona_id; //obtiene zona de perzona_zona
                     $filtro_zona .= "";
                // }
            }
            echo("<script>console.log('PHP: default - filtro personas: ".$filtro_usuario_zona.$filtro_categoria.$filtro_tipo."');</script>");
            echo("<script>console.log('PHP: default - filtro encuestas: ".$filtro_zona.$filtro_categoria.$filtro_tipo_encuesta."');</script>");
            
        // }//fin if

        $this->view->encuestas = My_Comun::obtenerFiltroSQLConcentradoEncuestas($filtro_zona, $filtro_categoria, $join_zona, $filtro_tipo_encuesta);
        $this->view->personas = My_Comun::obtenerFiltroSQLConcentrado($filtro_usuario_zona,$filtro_tipo, $filtro_categoria);
        //print_r($this->view->encuestas);
    }

    public function exportarAction(){
        ### Deshabilitamos el layout y la vista
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
          
        $persona_id=$this->_getParam('persona');
        $zona=$this->_getParam('zona');
        $encuesta_id=$this->_getParam('encuesta');
        $tipo_id=$this->_getParam('tipo');
       
        $filtro=" 1=1 ";
        $i=6;
        $data = array();
        
       
        $i++;

        // $encuestas = Encuesta::obtieneEncuestasPersona($persona_id);
        // $encuestas = Encuesta::obtieneEncuesta( $persona_id, $encuesta_id,$zona_id, $tipo_id);
        $encuestas = ContestaEncuesta::obtieneEncuestas_UsuarioZonaByIdconcentrado($persona_id, $zona,$tipo_id);

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
                $respuesta = Respuesta::obtieneRespuesta($persona_id, $pregunta->id, $zona, $tipo_id);
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
                        $respuesta2 = Respuesta::obtieneRespuestaEspecial($opcion->opcion, $pregunta->id, $zona, $tipo_id);
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

    public function onChangeEstadoAction(){
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

    public function onChangeZonaAction(){
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
        $zonas = My_Comun::obtenerFiltroSQL('tipo_persona', $filtro, ' descripcion asc');
        echo json_encode($zonas);
    }

    public function onChangeTipoAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        // $estado = $_POST["estado"];
  
        $tipo=$this->_getParam('tipo');
        $zona=$this->_getParam('zona');
        $filtro = "WHERE status = 1";
          
        // $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', $filtro, ' nombre asc');
        $categorias = My_Comun::obtenerCategoriasXzonaXtipo($zona,$tipo);
        echo json_encode($categorias);
    }

}//class
?>