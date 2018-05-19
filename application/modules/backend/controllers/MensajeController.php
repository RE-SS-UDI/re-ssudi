<?php

class Backend_MensajeController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        /* Initialize action controller here */
        $this->view->headScript()->appendFile('/js/backend/mensaje.js?'.time());
        //$zonaID = $this->view->zona[0]->id;
    }   

    public function indexAction()
    {
        // action body
        $idPer = Zend_Auth::getInstance()->getIdentity()->id;
        $this->view->zona = My_Comun::obtenerZonas($idPer);
        
        $sess=new Zend_Session_Namespace('permisos');
        print_r($sess->permisos);


        $this->view->mensajes=My_Comun::obtenerFiltroSQL("mensaje"," where persona_origen_id=".$idPer."","asunto ASC");
        
        $this->view->tipos_usuarios = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE 1 = 1 ', ' descripcion asc');
        
        $this->view->registro = My_Comun::obtenerSQL("usuario", "id", Zend_Auth::getInstance()->getIdentity()->id);
        
        

    }

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

    public function guardarMensajeAction()
    {
        

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
            $data['status'] = 1;
            $data['asunto'] = $_POST['asunto'];
            $data['duedate_at'] =  $_POST['fecha_limite'];
            
            echo My_Comun::guardarSQL("mensaje", $data, $data["id"], $bitacora);

    }
        

    }

?>