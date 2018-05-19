<?php

class Backend_ProyectoController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        /* Initialize action controller here */
        $this->view->headScript()->appendFile('/js/backend/proyecto.js?'.time());
    }

    public function indexAction()
    {
        // action body
        $sess=new Zend_Session_Namespace('permisos');
        $this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_PROYECTO")!==false;
        
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
        $area=$this->_getParam('area');
        $status=$this->_getParam('status');
        
        
        if($this->_getParam('status')!="")
            $filtro.=" AND p.status=".$this->_getParam('status');
        
        if($descripcion!='')
        {
            $descripcion=explode(" ", trim($descripcion));
            for($i=0; $i<=$descripcion[$i]; $i++)
            {
                $descripcion[$i]=trim(str_replace(array("'","\"",),array("�","�"),$descripcion[$i]));
                if($descripcion[$i]!="")
                    $filtro.=" AND (p.descripcion LIKE '%".$descripcion[$i]."%') ";
            }//for
        }//if

        if($area!='')
        {
            $area=explode(" ", trim($area));
            for($i=0; $i<=$area[$i]; $i++)
            {
                $area[$i]=trim(str_replace(array("'","\"",),array("�","�"),$area[$i]));
                if($area[$i]!="")
                    $filtro.=" AND (ap.descripcion LIKE '%".$area[$i]."%') ";
            }//for
        }//if

        $consulta = "SELECT ap.descripcion as 'area', p.*
                      FROM proyecto p
                      join area_proyecto ap
                      on p.area_id = ap.id
                      WHERE ".$filtro;
    
        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
        $i=0;

        $editar = My_Comun::tienePermiso("EDITAR_PROYECTO");
        $eliminar = My_Comun::tienePermiso("ELIMINAR_PROYECTO");
            
        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                
                $grid[$i]['descripcion'] =$registros['registros'][$k]->descripcion;
                $grid[$i]['area'] =$registros['registros'][$k]->area;
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
                    $grid[$i]['editar'] = '<span onclick="agregar(\'/backend/proyecto/agregar\','.$registros['registros'][$k]->id.', \'frmProyecto\',\'Edición de Proyectos\' );" title="Editar"><i class="boton fa fa-pencil fa-lg azul"></i></span>';
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
    
    }

    public function agregarAction()
    {
        // action body
        $this->_helper->layout->disableLayout();
        $this->view->llave = My_Comun::aleatorio(20);
        
        

        $idPer = Zend_Auth::getInstance()->getIdentity()->id;
        $this->view->zonaUser = My_Comun::obtenerZonas($idPer);
        $this->view->tipoUser = My_Comun::obtenertipoUSer($idPer);

        if($this->view->tipoUser[0]->tipo_usuario == 3){

            $this->view->areas = My_Comun::obtenerFiltroSQL('area_proyecto', ' WHERE status = 1 ', ' descripcion asc ');
        }else {

            $this->view->areas = My_Comun::obtenerFiltroSQL('area_proyecto', ' WHERE status = 1 and zona_id = '.$this->view->zonaUser[0]->id, ' descripcion asc ');
        }  


        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtenerSQL("proyecto", "id", $_POST["id"]);
        }
    }

    public function guardarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
            $bitacora = array();
            $bitacora[0]["modelo"] = "Proyecto";
            $bitacora[0]["campo"] = $_POST["descripcion"];
            $bitacora[0]["id"] = $_POST["id"];
            $bitacora[0]["agregar"] = "Agrega proyecto";
            $bitacora[0]["editar"] = "Editar proyecto";

            $preId = My_Comun::guardarSQL("proyecto", $_POST, $_POST["id"], $bitacora);

            echo($preId);
    }

    public function eliminarAction()
    {   
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
            
        $bitacora = array();
        $bitacora[0]["modelo"] = "Proyecto";
        $bitacora[0]["campo"] = "proyecto";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar proyecto";
        $bitacora[0]["deshabilitar"] = "Deshabilitar proyecto";
        $bitacora[0]["habilitar"] = "Habilitar proyecto";
            
        echo My_Comun::eliminarSQL("proyecto", $_POST["id"], $bitacora);
    }
}

?>



