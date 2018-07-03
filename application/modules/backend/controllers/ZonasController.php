<?php
class Backend_ZonasController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/zonas.js?'.time());
       
    }//function
 
    public function indexAction(){
    	$sess=new Zend_Session_Namespace('permisos');
    	$this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_ZONAS")!==false;

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
                    $filtro.=" AND (z.nombre LIKE '%".$nombre[$i]."%') ";
            }//for
        }//if

        $consulta = "SELECT z.*, es.estado as estado
                      FROM zona z
                      INNER JOIN estados es
                      ON es.id_estado = z.estado_id
                      WHERE ".$filtro;
    
        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
    	$i=0;

        $editar = My_Comun::tienePermiso("EDITAR_ZONAS");
    	$eliminar = My_Comun::tienePermiso("ELIMINAR_ZONAS");
            
        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                
                $grid[$i]['nombre'] =$registros['registros'][$k]->nombre;
                $grid[$i]['estado'] =$registros['registros'][$k]->estado;
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
                    $grid[$i]['editar'] = '<span onclick="agregar(\'/backend/zonas/agregar\','.$registros['registros'][$k]->id.', \'frmZona\',\'Edición de Zona\' );" title="Editar"><i class="boton fa fa-pencil fa-lg azul"></i></span>';
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
    
    public function agregarAction(){
        $this->_helper->layout->disableLayout();
        $this->view->llave = My_Comun::aleatorio(20);
		
        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtenerSQL("zona", "id", $_POST["id"]);
        }
        $this->view->estados = Usuario::obtieneestadosZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);

    }//function

    public function guardarAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		
        	$bitacora = array();
        	$bitacora[0]["modelo"] = "Zona";
        	$bitacora[0]["campo"] = "nombre";
        	$bitacora[0]["id"] = $_POST["id"];
        	$bitacora[0]["agregar"] = "Agrega zona";
        	$bitacora[0]["editar"] = "Editar zona";

            $preId = My_Comun::guardarSQL("zona", $_POST, $_POST["id"], $bitacora);

            echo($preId);
    }//guardar
	
    function eliminarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
			
        $bitacora = array();
        $bitacora[0]["modelo"] = "zona";
        $bitacora[0]["campo"] = "nombre";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar zona";
        $bitacora[0]["deshabilitar"] = "Deshabilitar zona";
        $bitacora[0]["habilitar"] = "Habilitar zona";
			
        echo My_Comun::eliminarSQL("zona", $_POST["id"], $bitacora);
    }//function

}//class
?>