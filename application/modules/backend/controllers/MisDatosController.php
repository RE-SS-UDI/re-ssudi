<?php
class Backend_MisDatosController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/usuario.js?'.time());
       
    }//function
 
    public function misDatosAction(){
//        $this->_helper->layout->disableLayout();


        $this->view->registro = My_Comun::obtenerSQL("usuario", "id", Zend_Auth::getInstance()->getIdentity()->id);
        $this->view->persona = My_Comun::obtenerSQL("persona", "id", $this->view->registro->persona_id);
    }

    public function guardarUsuarioAction(){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $bitacora = array();
        $bitacora[0]["modelo"] = "Usuario";
        $bitacora[0]["campo"] = "nombre";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["agregar"] = "Agregar usuario";
        $bitacora[0]["editar"] = "Editar usuario";
        
        if($_POST["confirmar"]==$_POST["contrasena"]){

            $data = array();
            $data2 = array();

            $data['nombre'] = $_POST['nombre'];
            $data['apellido_pat'] = $_POST['apellido_pat'];
            $data['apellido_mat'] = $_POST['apellido_mat'];
            $data['genero'] = $_POST['genero'];
            $data['fecha_nacimiento'] = $_POST['fecha_nacimiento'];
            $data['curp'] = $_POST['curp'];
            $data['rfc'] = $_POST['rfc'];
            $data['telefono'] = $_POST['telefono'];
            $data['celular'] = $_POST['celular'];
            $data['id'] = $_POST['persona_id'];

            $data2['usuario'] = $_POST['usuario'];
            $data2['contrasena'] = $_POST['contrasena'];
            $data2['id'] = $_POST['id'];

           echo My_Comun::guardarSQL("persona", $data, $data["id"], $bitacora);

           echo My_Comun::guardarSQL("usuario", $data2, $data2["id"], $bitacora);
        }
        
        else{
            echo "Las contraseñas no corresponden";
        }
    }
}//class
?>