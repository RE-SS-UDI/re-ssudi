<?php
class Backend_PreguntasController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/preguntas.js?'.time());
       
    }//function
 
    public function indexAction(){
    	$sess=new Zend_Session_Namespace('permisos');
    	$this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_PREGUNTAS")!==false;
        $this->view->categorias = My_Comun::obtenerFiltroSQL('categoria', ' WHERE status = 1 ', ' nombre asc');
        $this->view->encuestas = My_Comun::obtenerFiltroSQL('encuesta', ' WHERE status = 1 ', ' nombre asc');
    }//function

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        $filtro=" 1=1 ";

        $nombre=$this->_getParam('nombre');
        $status=$this->_getParam('status');
        $tipo=$this->_getParam('tipo');
        $categoria=$this->_getParam('categoria');
        $nombre_encuesta=$this->_getParam('nombre_encuesta');
        
        
        if($this->_getParam('status')!="")
            $filtro.=" AND p.status=".$this->_getParam('status');

        if($tipo!="")
            $filtro.=" AND p.tipo=".$tipo;

        if($categoria!="")
            $filtro.=" AND e.categoria_id=".$categoria;

        if($nombre_encuesta!="")
            $filtro.=" AND (e.id = '".$nombre_encuesta."') ";

        
        if($nombre!='')
        {
            $nombre=explode(" ", trim($nombre));
            for($i=0; $i<=$nombre[$i]; $i++)
            {
                $nombre[$i]=trim(str_replace(array("'","\"",),array("�","�"),$nombre[$i]));
        		if($nombre[$i]!="")
                    $filtro.=" AND (p.descripcion LIKE '%".$nombre[$i]."%') ";
            }//for
        }//if

        $consulta = "SELECT p.id,p.descripcion,p.tipo,p.status, e.nombre as encuesta, c.nombre as categoria
                      FROM pregunta p
                      INNER JOIN encuesta e
                      on e.id = p.encuesta_id
                      INNER JOIN categoria c
                      on c.id = e.categoria_id
                      WHERE ".$filtro;
    

        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
    	$i=0;

        $editar = My_Comun::tienePermiso("EDITAR_PREGUNTAS");
    	$eliminar = My_Comun::tienePermiso("ELIMINAR_PREGUNTAS");
            
        for ($k=0; $k < count($registros['registros']); $k++) 
        {
            $tipo = '';

            switch ($registros['registros'][$k]->tipo) {
            //Abierta corta
                case '1':
                    $tipo = 'Abierta corta';
                    break;
            //Abierta larga
                case '2':
                    $tipo = 'Abierta larga';
                    break;
            //Radio boton
                case '3':
                    $tipo = 'Opción múltiple';
                    break;
            //Opción múltiple
                case '4':
                    $tipo = 'Múltiples respuestas';
                    break;
            }

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
                    $grid[$i]['editar'] = '<span onclick="agregarPregunta(\'/backend/preguntas/agregar\','.$registros['registros'][$k]->id.', \'frmPregunta\',\'Edición de Pregunta\' );" title="Editar"><i class="boton fa fa-pencil fa-lg azul"></i></span>';
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
                
                $grid[$i]['descripcion'] =$registros['registros'][$k]->descripcion;
                $grid[$i]['tipo'] =$tipo;
                $grid[$i]['encuesta'] =$registros['registros'][$k]->encuesta;
                $grid[$i]['categoria'] =$registros['registros'][$k]->categoria;
                $grid[$i]['status']=(($registros['registros'][$k]->status)?'Habilitado':'Inhabilitado');
               
    				
            $i++;
    	}//foreach
    	My_Comun::armarGrid($registros,$grid);
    }//function
    
    public function agregarAction(){
        $this->_helper->layout->disableLayout();
        $this->view->llave = My_Comun::aleatorio(20);
		
        $this->view->encuestas = My_Comun::obtenerFiltroSQL('encuesta', ' WHERE status = 1', 'nombre asc');

        if($_POST["id"]!="0"){
            $this->view->registro = My_Comun::obtenerSQL("pregunta", "id", $_POST["id"]);
            $this->view->opciones = My_Comun::obtenerFiltroSQL('opciones_pregunta',' WHERE pregunta_id = '.$_POST["id"],' opcion asc');
        }
    }//function

    public function guardarAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		
        	$bitacora = array();
        	$bitacora[0]["modelo"] = "Pregunta";
        	$bitacora[0]["campo"] = "descripcion";
        	$bitacora[0]["id"] = $_POST["id"];
        	$bitacora[0]["agregar"] = "Agrega pregunta";
        	$bitacora[0]["editar"] = "Editar pregunta";

            unset($_POST['cantidad']);
            $opciones = array();
            $data = array();

            $opciones = $_POST['opciones'];
            unset($_POST['opciones']);

//            print_r($opciones);
//            exit;

            $preId = My_Comun::guardarSQL("pregunta", $_POST, $_POST["id"], $bitacora);

            OpcionPregunta::eliminarOpciones($_POST["id"]);

            foreach ($opciones as $key => $value) {
                $data['opcion'] = $value;
                $data['id'] = '';
                $data['pregunta_id'] = $preId;
                $opc_preg = My_Comun::guardarSQL("opciones_pregunta", $data, $data["id"], "");
                //echo $opc_preg;
            }

            

            echo($preId);
    }//guardar
	
    function eliminarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
			
        $bitacora = array();
        $bitacora[0]["modelo"] = "Pregunta";
        $bitacora[0]["campo"] = "nombre";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar pregunta";
        $bitacora[0]["deshabilitar"] = "Deshabilitar pregunta";
        $bitacora[0]["habilitar"] = "Habilitar pregunta";
			
        echo My_Comun::eliminarSQL("pregunta", $_POST["id"], $bitacora);
    }//function

    function agregaOpcionesAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $tipo = $_POST['tipo'];
        $opciones = '';

        for ($i=0; $i < $_POST['cantidad']; $i++) { 
            $time = time();
            $opciones .= '<div id="opcion_'.$time.'" class="col-xs-12 form-group">
                            <label class="col-xs-2 control-label">Descripción:</label>
                        <div class="col-xs-6">
                            <input type="text" name="opciones[]" id="opcion_'.$time.'" class="form-control input-sm required" maxlength="100">
                        </div>
                        <div class="col-xs-2">
                            <a class="btn btn-danger" title="Eliminar" onclick="eliminaOpcion(\''.$time.'\')"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;Eliminar</a>
                        </div>
                      </div>
                    ';
        }
   
        echo $opciones;
    }

    function eliminarOpcionesAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
            
        $bitacora = array();
        $bitacora[0]["modelo"] = "Opción";
        $bitacora[0]["campo"] = "opcion";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar opción";
        $bitacora[0]["deshabilitar"] = "Deshabilitar opción";
        $bitacora[0]["habilitar"] = "Habilitar opción";
            
        echo My_Comun::eliminarSQL("opciones_pregunta", $_POST["id"], $bitacora);
    }//function

    function obtieneEncuestasAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $opciones = '<option value="" selected>Todas las empresas</option>';
        $encuestas = My_Comun::obtenerFiltroSQL('encuesta', ' WHERE status = 1 AND categoria_id = '.$_POST['id'], ' nombre asc');

        foreach ($encuestas as $empresa) {
            $opciones .= '<option value="'.$empresa->id.'">'.$empresa->nombre.'</option>';
        }

        echo $opciones;
    }
}//class
?>