<?php

class Backend_EditarMensajeController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/editar-mensaje.js?'.time());
    }

    public function indexAction()
    {
        // action body
        $idPer = Zend_Auth::getInstance()->getIdentity()->id;
        $sess=new Zend_Session_Namespace('permisos');
        $this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_MENSAJE")!==false;

    }

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        $filtro=" 1=1 ";

        $persona_destino_id=$this->_getParam('usuario');
        $status=$this->_getParam('status');
        
        
        if($this->_getParam('status')!="")
            $filtro.=" AND status=".$this->_getParam('status');
        
        if($persona_destino_id!='')
        {
            $persona_destino_id=explode(" ", trim($persona_destino_id));
            for($i=0; $i<=$persona_destino_id[$i]; $i++)
            {
                $persona_destino_id[$i]=trim(str_replace(array("'","\"",),array("�","�"),$persona_destino_id[$i]));
                if($persona_destino_id[$i]!="")
                    $filtro.=" AND (usuario LIKE '%".$persona_destino_id[$i]."%') ";
            }//for
        }//if

        $consulta = "SELECT * from mensajesGenerales
                    WHERE ".$filtro;

        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
        $i=0;

        $editar = My_Comun::tienePermiso("EDITAR_MENSAJE");
        $eliminar = My_Comun::tienePermiso("ELIMINAR_EDITARMENSAJE");
            
        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                
                $grid[$i]['persona_destino_id'] =$registros['registros'][$k]->usuario;
                $grid[$i]['descripcion'] =$registros['registros'][$k]->descripcion;
                $grid[$i]['asunto'] =$registros['registros'][$k]->asunto;
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
                    $grid[$i]['editar'] = '<span onclick="agregar(\'/backend/editarMensaje/agregar\','.$registros['registros'][$k]->id.', \'frmEditarmsje\',\'Edición de Mensajes\' );" title="Editar"><i class="boton fa fa-pencil fa-lg azul"></i></span>';
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
        // action body
        $this->_helper->layout->disableLayout();
        $this->view->llave = My_Comun::aleatorio(20);

        $this->view->tipos_usuarios = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE 1 = 1 ', ' descripcion asc');
        $this->view->usuarios = My_Comun::obtenerFiltroSQL('usuario', ' WHERE 1 = 1 ', ' usuario asc');
        $idPer = Zend_Auth::getInstance()->getIdentity()->id;
        $this->view->zona = My_Comun::obtenerZonas($idPer);

        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtenerTipoMsje($_POST["id"]);
        };
    }

    public function guardarAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
            $bitacora = array();
            $bitacora[0]["modelo"] = "Mensajes";
            $bitacora[0]["campo"] = $_POST["asunto"];
            $bitacora[0]["id"] = $_POST["id"];
            $bitacora[0]["agregar"] = "Enviar mensaje";
            $bitacora[0]["editar"] = "Editar mensaje";

            $data = array();

            $data['id'] = $_POST['mensaje_id'];
            $data['persona_origen_id'] = Zend_Auth::getInstance()->getIdentity()->id;
            $data['persona_destino_id'] = $_POST['usuario_destino'];
            $data['mensaje'] = $_POST['mensaje'];
            $data['status'] = $_POST['status'];
            $data['asunto'] = $_POST['asunto'];
            $data['duedate_at'] =  $_POST['fecha_limite'];
            
            echo My_Comun::guardarSQL("mensaje", $data, $data["id"], $bitacora);

            echo($preId);
    }//guardar

    public function eliminarAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
            
        $bitacora = array();
        $bitacora[0]["modelo"] = "Mensajes";
        $bitacora[0]["campo"] = "eliminar mensaje";
        $bitacora[0]["id"] = 1;
        $bitacora[0]["eliminar"] = "Eliminar mensaje";
        $bitacora[0]["deshabilitar"] = "Deshabilitar mensaje";
        $bitacora[0]["habilitar"] = "Habilitar mensaje";


            
        echo My_Comun::eliminarSQL("mensaje", $_POST["id"], $bitacora);
    }//function


    public function obtenerUsuarioAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $usuarios = My_Comun::obtenerFiltroSqlUsuariosMensajes($_POST['id'],$_POST['idZona']);
        $admins = My_Comun::obtenerFiltroSqlUsuariosMensajesAdmins($_POST['id']);
        $idPer = Zend_Auth::getInstance()->getIdentity()->id;
        $this->view->tipoUser = My_Comun::obtenertipoUSer($idPer);
        $opciones = '<option value="">-- Seleccione usuario --</option>';
        if($this->view->tipoUser[0]->tipo_usuario == 3){
            foreach ($admins as $admin) {
                if ($admin->RetVal == 1) {
                $opciones.= '<option value="'.$admin->id.'">'.utf8_encode($admin->persona).'</option>';
                }else if ($admin->RetVal == 0) {

                }else {
                    $opciones .= '<option value="0" selected>Todos</option>';
                }                                
            }
        }else {
            foreach ($usuarios as $usuario) { 
                if ($usuario->RetVal == 1) {
                    $opciones.= '<option value="'.$usuario->id.'">'.utf8_encode($usuario->persona).'</option>';
                }else if ($usuario->RetVal == 0) {

                }else {
                    $opciones .= '<option value="0" selected>Todos</option>';
                }                                
            }
        }  
            echo($opciones);
    }
}



