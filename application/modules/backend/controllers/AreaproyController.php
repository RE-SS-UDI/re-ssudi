<?php
class Backend_AreaproyController extends Zend_Controller_Action
{    public function init()
    {
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/areaproy.js?'.time());
    
    }

    public function indexAction()
    {
    	$sess=new Zend_Session_Namespace('permisos');
    	$this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_AREAPROY")!==false;
    }

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        $filtro=" 1=1 ";



         //Verificamos el tipo d usurio
        if(Zend_Auth::getInstance()->getIdentity()->tipo_usuario != 3){
            $zona = Usuario::obtieneZonaUsuario(Zend_Auth::getInstance()->getIdentity()->id);
            $filtro .= " AND ap.zona_id = ".$zona->id." ";
        }

        $descripcion=$this->_getParam('descripcion');
        $status=$this->_getParam('status');
        
        
        if($this->_getParam('status')!="")
            $filtro.=" AND status=".$this->_getParam('status');
        
        if($descripcion!='')
        {
            $descripcion=explode(" ", trim($descripcion));
            for($i=0; $i<=$descripcion[$i]; $i++)
            {
                $descripcion[$i]=trim(str_replace(array("'","\"",),array("�","�"),$descripcion[$i]));
                if($descripcion[$i]!="")
                    $filtro.=" AND (ap.descripcion LIKE '%".$descripcion[$i]."%') ";
            }//for
        }//if

        $consulta = "
                    SELECT z.nombre, ap.* FROM area_proyecto ap 
                    join zona z 
                    on ap.zona_id = z.id
                    WHERE ".$filtro;
    
        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
        $i=0;

        $editar = My_Comun::tienePermiso("EDITAR_AREAPROY");
        $eliminar = My_Comun::tienePermiso("ELIMINAR_AREAPROY");
            
        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                

                $grid[$i]['descripcion'] =$registros['registros'][$k]->descripcion;
                $grid[$i]['nombre'] =$registros['registros'][$k]->nombre;
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
                    $grid[$i]['editar'] = '<span onclick="agregar(\'/backend/areaproy/agregar\','.$registros['registros'][$k]->id.', \'frmAreaproy\',\'Edición de Area de Proyectos\' );" title="Editar"><i class="boton fa fa-pencil fa-lg azul"></i></span>';
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



    public function agregarAction()
    {   

        $this->_helper->layout->disableLayout();
        $this->view->llave = My_Comun::aleatorio(20);
        
        $idPer = Zend_Auth::getInstance()->getIdentity()->id;
        $this->view->zonaUser = My_Comun::obtenerZonas($idPer);
        $this->view->tipoUser = My_Comun::obtenertipoUSer($idPer);

        if($this->view->tipoUser[0]->tipo_usuario == 3){

            $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1 ', ' nombre asc ');
        }else {

            $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1 and id = '.$this->view->zonaUser[0]->id, ' nombre asc ');
        }  

        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtenerSQL("area_proyecto", "id", $_POST["id"]);
        }
    }

    public function guardarAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
            $bitacora = array();
            $bitacora[0]["modelo"] = "Area Proyecto"; //modulo
            $bitacora[0]["campo"] = $_POST["descripcion"];      //referencia 
            $bitacora[0]["id"] = $_POST["id"];
            $bitacora[0]["agregar"] = "Agrega area de proyecto"; //accion
            $bitacora[0]["editar"] = "Editar area de proyecto";  //accion

            $preId = My_Comun::guardarSQL("area_proyecto", $_POST, $_POST["id"], $bitacora);

            echo($preId);
    }//guardar

    public function eliminarAction(){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
            
        $bitacora = array();
        $bitacora[0]["modelo"] = "Area Proyecto";
        $bitacora[0]["campo"] = "area de proyecto";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar area de proyecto";
        $bitacora[0]["deshabilitar"] = "Deshabilitar area de proyecto";
        $bitacora[0]["habilitar"] = "Habilitar area de proyecto";
            
        echo My_Comun::eliminarSQL("area_proyecto", $_POST["id"], $bitacora);
    }//function


}











