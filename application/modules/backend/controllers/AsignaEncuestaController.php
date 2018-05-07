<?php
class Backend_AsignaEncuestaController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/asigna-encuesta.js');
       
    }//function
 
    public function indexAction(){

        $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1 ', ' nombre asc');
        $this->view->encuestas = My_Comun::obtenerFiltroSQL('encuesta', ' WHERE status = 1 ', ' nombre asc');
//        print_r($this->view->encuestas);
//        exit;
    }//function

    public function cambiaZonaAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $encuestas = My_Comun::obtenerFiltroSQL('encuesta', ' WHERE status = 1 AND zona_id = '.$_POST['id'], ' nombre asc');

        $lista = '';
        foreach ($encuestas as $encuesta) {
            $lista .= '
                        <div class="col-xs-12">
                            <div class="col-xs-1">
                                <label class="control-label"><input type="checkbox" name="encuesta_id" value="'.$encuesta->id.'"></label>
                            </div>
                            <div class="col-xs-11">
                                <label class="control-label">'.$encuesta->nombre.'</label>
                            </div>
                        </div>
                        ';
        }

        echo $lista;
    }

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        $filtro=" 1=1 ";

        $zona=$this->_getParam('zona_id');
        $encuestas=$this->_getParam('encuestas');

        $encuestas = substr($encuestas,0,-1);
        
        if($zona!='')
        {
            $filtro.=" AND (ze.zona_id = '".$zona."') ";
        }//if

        $consulta = "SELECT e.nombre as encuesta, z.nombre as zona
                      FROM encuesta e
                      INNER JOIN zona_encuesta ze
                      on e.id = ze.encuesta_id
                      INNER JOIN zona z
                      on z.id = ze.zona_id
                      WHERE ".$filtro;
    
        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
    	$i=0;

        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                
                $grid[$i]['encuesta'] =$registros['registros'][$k]->encuesta;
                $grid[$i]['zona'] =$registros['registros'][$k]->zona;
                $grid[$i]['status']=(($registros['registros'][$k]->status)?'Habilitado':'Inhabilitado');
               
            
    				
            $i++;
    	}//foreach
    	My_Comun::armarGrid($registros,$grid);
    }//function

    public function guardarAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		
        	$bitacora = array();
        	$bitacora[0]["modelo"] = "AsignaEncuesta";
        	$bitacora[0]["campo"] = "id";
        	$bitacora[0]["id"] = $_POST["id"];
        	$bitacora[0]["agregar"] = "Agrega categoría";
        	$bitacora[0]["editar"] = "Editar categoría";

            if ($_POST['zona_id'] != '') {
                Encuesta::eliminaEncuestasAsignadas($_POST['zona_id']);
            }

            $data = array();


            foreach ($_POST['encuesta'] as $key) {
                $data[] = $key;
//                $data['zona_id'] = $_POST['zona_id'];
            }
            foreach ($data as $key2) {
                $data2 = array();
                $data2['encuesta_id'] = $key2;
                $data2['zona_id'] = $_POST['zona_id'];
                //print_r($data2);
                $preId = My_Comun::guardarSQL("zona_encuesta", $data2, $data2["id"], $bitacora);
            }
 //               exit;
            echo($preId);
    }//guardar
	
    function eliminarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
			
        $bitacora = array();
        $bitacora[0]["modelo"] = "Categoría";
        $bitacora[0]["campo"] = "nombre";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar categoría";
        $bitacora[0]["deshabilitar"] = "Deshabilitar categoría";
        $bitacora[0]["habilitar"] = "Habilitar categoría";
			
        echo My_Comun::eliminarSQL("categoria", $_POST["id"], $bitacora);
    }//function

}//class
?>