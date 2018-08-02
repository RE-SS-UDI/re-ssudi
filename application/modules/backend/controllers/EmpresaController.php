<?php
class Backend_EmpresaController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/empresa.js?'.time());
       
    }//function
 
    public function indexAction(){
        $sess=new Zend_Session_Namespace('permisos');
        $this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_EMPRESA")!==false;
        $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1 ', ' nombre asc');
       // $this->view->estados = My_Comun::obtenerFiltroSQL('estados', ' WHERE status = 1 ', ' estado asc');
        $this->view->estados = Usuario::obtieneestadosZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);


        
    }//function

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        $filtro=" 1=1 ";

        $nombre=$this->_getParam('nombre');
        $razon_social=$this->_getParam('razon_social');
        $estado=$this->_getParam('estado');
        $calle=$this->_getParam('calle');
        $contacto=$this->_getParam('contacto');
        $zona=$this->_getParam('zona');
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
                    $filtro.=" AND (e.nombre LIKE '%".$nombre[$i]."%') ";
            }//for
        }//if
        if($razon_social!='')
        {
            $filtro.=" AND (e.razon_social LIKE '%".$razon_social."%') ";
        }//if
        if($calle!='')
        {
            $filtro.=" AND (e.calle LIKE '%".$calle."%') ";
        }//if
        if($contacto!='')
        {
            $filtro.=" AND (e.contacto LIKE '%".$contacto."%') ";
        }//if
        if($zona!='')
        {
            $filtro.=" AND (e.zona_id = '".$zona."') ";
        }else{
            $filtro.=" AND (e.zona_id = '0') ";
        }
        if($estado!='')
        {
            $filtro.=" AND (e.estado_id = '".$estado."') ";
        }else{
            $filtro.=" AND (e.estado_id = '0') ";
        }

        if(Zend_Auth::getInstance()->getIdentity()->tipo_usuario != 3){
            $zona = Usuario::obtieneZonaUsuario(Zend_Auth::getInstance()->getIdentity()->persona_id);
            $filtro .= " AND e.zona_id = ".$zona->id." ";
        }

        $consulta = "SELECT e.*
                      FROM empresa e
                      WHERE ".$filtro;
    
        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
        $i=0;

        $editar = My_Comun::tienePermiso("EDITAR_EMPRESA");
        $eliminar = My_Comun::tienePermiso("ELIMINAR_EMPRESA");
            
        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                $estado = My_Comun::obtenerSQL('estados','id_estado',$registros['registros'][$k]->estado_id);
                $zona = My_Comun::obtenerSQL('zona', 'id',$registros['registros'][$k]->zona_id);
                $grid[$i]['nombre'] =$registros['registros'][$k]->nombre;
//                $grid[$i]['razon_social'] =$registros['registros'][$k]->razon_social;
//                $grid[$i]['domicilio'] =$registros['registros'][$k]->calle.' Num. Exterior: '.$registros['registros'][$k]->numExterior.' Num. Interior: '.$registros['registros'][$k]->numInterior;
                $grid[$i]['contacto'] =$registros['registros'][$k]->contacto;
                $grid[$i]['telefono'] =$registros['registros'][$k]->telefono;
                $grid[$i]['zona'] =$zona->nombre;
//                $grid[$i]['rfc'] =$registros['registros'][$k]->rfc;
                $grid[$i]['estado'] =$estado->estado;
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
                    $grid[$i]['editar'] = '<span onclick="agregar(\'/backend/empresa/agregar\','.$registros['registros'][$k]->id.', \'frmEmpresa\',\'Edición de empresa\' );" title="Editar"><i class="boton fa fa-pencil fa-lg azul"></i></span>';
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
        
        $this->view->estados = My_Comun::obtenerFiltroSQL('estados', ' where status = 1 ', ' estado asc');
        //$this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' where status = 1 ', ' nombre asc');


        $idPer = Zend_Auth::getInstance()->getIdentity()->id;

        $this->view->zon = My_Comun::obtenerZonas($idPer);
        //$this->view->zonasFiltro = My_Comun::obtenerFiltroSQLZonas($this->view->zon[0]->id);

        $this->view->tipoUser = My_Comun::obtenertipoUSer($idPer);



        if($this->view->tipoUser[0]->tipo_usuario == 3){

            $this->view->zonasFiltro = My_Comun::obtenerFiltroSQLZonasAdmin();
        }else {

            $this->view->zonasFiltro = My_Comun::obtenerFiltroSQLZonas($this->view->zon[0]->id);
        }        



        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtenerSQL("empresa", "id", $_POST["id"]);
            $this->view->muni = My_Comun::obtenerSQL("municipios",'id_municipio',$this->view->registro->municipio_id);
//            print_r($this->view->muni); exit; 
        }
    }//function

    public function obtieneMunicipiosAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $municipios = My_Comun::obtenerFiltroSQL('municipios', ' where estado_id = '.$_POST['id'],' nombre_municipio asc');
        $opciones = '<option value="">Escoge un municipio</option>';
        
        foreach ($municipios as $municipio) {
            if ($municipio->nombre_municipio != '') {
                $opciones.= '<option value="'.$municipio->id_municipio.'">'.utf8_encode($municipio->nombre_municipio).'</option>';
            }
        }
            echo($opciones);
    }//guardar

    public function guardarAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
            $bitacora = array();
            $bitacora[0]["modelo"] = "Empresa";
            $bitacora[0]["campo"] = "razon_social";
            $bitacora[0]["id"] = $_POST["id"];
            $bitacora[0]["agregar"] = "Agrega empresa";
            $bitacora[0]["editar"] = "Editar empresa";

            $preId = My_Comun::guardarSQL("empresa", $_POST, $_POST["id"], $bitacora);

            echo($preId);
    }//guardar
    
    function eliminarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
            
        $bitacora = array();
        $bitacora[0]["modelo"] = "Empresa";
        $bitacora[0]["campo"] = "razon_social";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar empresa";
        $bitacora[0]["deshabilitar"] = "Deshabilitar empresa";
        $bitacora[0]["habilitar"] = "Habilitar empresa";
            
        echo My_Comun::eliminarSQL("empresa", $_POST["id"], $bitacora);
    }//function 

    public function empresaUsuarioAction(){

        $usuario = My_Comun::obtenerSQL('usuario', 'id', Zend_Auth::getInstance()->getIdentity()->id);
        $persona = My_Comun::obtenerSQL('persona', 'id', $usuario->persona_id);
//        print_r('Aqui'.$usuario);
        $this->view->registro = My_Comun::obtenerSQL('empresa', 'id', $persona->empresa_id);
          $this->view->estados = My_Comun::obtenerFiltroSQL('estados', ' where 1 = 1 ', ' estado asc');
            if ($this->view->registro->municipio_id != '') {
                $this->view->municipio = My_Comun::obtenerSQL('municipios', 'id_municipio', $this->view->registro->municipio_id);

            }
    }//function
    

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

}//class
?>