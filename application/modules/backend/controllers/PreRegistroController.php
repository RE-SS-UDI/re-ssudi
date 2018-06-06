<?php
class Backend_PreRegistroController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/pre-registro.js?'.time());
       
    }//function
 
    public function indexAction(){
    	$sess=new Zend_Session_Namespace('permisos');
    	///$this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_PRE_REGISTRO")!==false;

    }//function

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        $filtro=" 1=1 ";


        //Verificamos el tipo d usurio
        if(Zend_Auth::getInstance()->getIdentity()->tipo_usuario != 3){
            $zona = Usuario::obtieneZonaUsuario(Zend_Auth::getInstance()->getIdentity()->id);
            $filtro .= " AND pr.zona_id = ".$zona->id." ";
        }


        $nombre=$this->_getParam('nombre');
        $materno=$this->_getParam('materno');
        $paterno=$this->_getParam('paterno');
        $status=$this->_getParam('status');
        
        
        if($this->_getParam('status')!="")
            $filtro.=" AND status=".$this->_getParam('status');
        
        if($nombre!='')
        {
            $filtro.=" AND (pr.nombre LIKE '%".$nombre."%') ";
        }
        if($paterno!='')
        {
            $filtro.=" AND (pr.apellido_pat LIKE '%".$paterno."%') ";
        }
        if($materno!='')
        {
            $filtro.=" AND (pr.apellido_mat LIKE '%".$materno."%') ";
        }

        $consulta = "SELECT pr.id, pr.nombre, pr.apellido_pat, pr.apellido_mat, pr.correo, pr.telefono, pr.status
                      FROM pre_registro pr
                      JOIN zona z
                      ON z.id = pr.zona_id
                      WHERE ".$filtro;

        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
    	$i=0;

        $editar = My_Comun::tienePermiso("EDITAR_PRE_REGISTRO");
    	$eliminar = My_Comun::tienePermiso("ELIMINAR_PRE_REGISTRO");
            
        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                $grid[$i]['nombre'] =$registros['registros'][$k]->nombre.' '.$registros['registros'][$k]->apellido_pat.' '.$registros['registros'][$k]->apellido_mat;
                $grid[$i]['correo'] =$registros['registros'][$k]->correo;
                $grid[$i]['telefono'] =$registros['registros'][$k]->telefono;
                $grid[$i]['status']="En espera";
               
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
                    $grid[$i]['editar'] = '<span onclick="agregar(\'/backend/pre-registro/agregar\','.$registros['registros'][$k]->id.', \'frmPreRegistro\',\'Ficha de Pre-registro\' );" title="Visualizar"><i class="boton fa fa-eye fa-lg azul"></i></span>';
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

//        $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1', ' nombre asc');
        //$this->view->empresas = My_Comun::obtenerFiltroSQL('empresa', ' WHERE status = 1', ' nombre asc');

        // $zId = $this->_getParam('zona_id');

        $idPer = Zend_Auth::getInstance()->getIdentity()->id;
        $this->view->zonaUser = My_Comun::obtenerZonas($idPer);
        $this->view->tipoUser = My_Comun::obtenertipoUSer($idPer);

        if($this->view->tipoUser[0]->tipo_usuario == 3){

            $this->view->empresas = My_Comun::obtenerFiltroSQLEmpresaRoot();
        }else {

            $this->view->empresas = My_Comun::obtenerFiltroSQLEmpresa($this->view->zonaUser[0]->id);
        }  



 
        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtenerSQL("pre_registro", "id", $_POST["id"]);
        }
    }//function

    public function guardarAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		
        	$bitacora = array();
        	$bitacora[0]["modelo"] = "Pre-registro";
        	$bitacora[0]["campo"] = "nombre";
        	$bitacora[0]["id"] = $_POST["id"];
        	$bitacora[0]["agregar"] = "Modifica pre-registro";
        	$bitacora[0]["editar"] = "Editar pre-registro";

            $pre_registro = My_Comun::obtenerSQL('pre_registro','id',$_POST['id']);            

            $data = array();
            $data['nombre'] = $_POST['nombre'];
            $data['apellido_pat'] = $_POST['apellido_pat'];
            $data['apellido_mat'] = $_POST['apellido_mat'];
            $data['rfc'] = $_POST['rfc'];
            $data['curp'] = $_POST['curp'];
            $data['fecha_nacimiento'] = $_POST['fecha_nacimiento'];
            $data['genero'] = $_POST['genero'];
            $data['correo'] = $_POST['correo'];
            $data['telefono'] = $_POST['telefono'];
            $data['celular'] = $_POST['celular'];
            $data['empresa_id'] = $_POST['empresa_id'];

            $preId = My_Comun::guardarSQL("persona", $data, $data["id"], $bitacora);

            //Se define una cadena de caractares. Te recomiendo que uses esta.
            $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
            $pass = $this->generaUsuarioContrasena($cadena, 8);
            $armando = trim($_POST['nombre'].$_POST['apellido_pat'].$_POST['apellido_mat']," ");
            $usu = $this->generaUsuarioContrasena($armando,6);
            $data2 = array();
            $data2['persona_id'] = $preId;
            $data2['cambiado'] = 0;
            $data2['usuario'] = $usu;
            $data2['contrasena'] = $pass;
            //Nuevo
            $data2['tipo_usuario'] = 6;
            $data2['permisos'] = '';

            $usuId = My_Comun::guardarSQL("usuario", $data2, $data2["id"], $bitacora);
            
            $pre = My_Comun::eliminarSQL("pre_registro", $_POST["id"], $bitacora);

            $titulo = 'Bienvenido a Sinergia';
            $cuerpo = 'Ha sido aceptado en el sistema de "Sinergia", podrá aceder al sistema con la siguiente información:<br>';
            $cuerpo .= 'Usuario: '.$usu;
            $cuerpo .= '<br>Password: '.$pass;

            $respuesta = My_Comun::envioCorreo($titulo, $cuerpo, 'ressudi.utj@gmail.com', 'Sinergia', $_POST['correo'], $_POST['nombre'].' '.$_POST['apellido_pat'].' '.$_POST['apellido_mat']);
            print_r($respuesta);
            
            echo($preId);
    }//guardar
	
    function eliminarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
			
        $bitacora = array();
        $bitacora[0]["modelo"] = "Pre-registro";
        $bitacora[0]["campo"] = "nombre";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar pre-registro";
        $bitacora[0]["deshabilitar"] = "Deshabilitar pre-registro";
        $bitacora[0]["habilitar"] = "Habilitar pre-registro";
			
        echo My_Comun::eliminarSQL("pre_registro", $_POST["id"], $bitacora);
    }//function 

    public function generaUsuarioContrasena($cadena,$longitud)
    {
        //Obtenemos la longitud de la cadena de caracteres
        $longitudCadena=strlen($cadena);
         
        //Se define la variable que va a contener la contraseña
        $pass = "";
        //Se define la longitud de la contraseña, en mi caso 10, pero puedes poner la longitud que quieras
        $longitudPass=$longitud;
         
        //Creamos la contraseña
        for($i=1 ; $i<=$longitudPass ; $i++){
            //Definimos numero aleatorio entre 0 y la longitud de la cadena de caracteres-1
            $pos=rand(0,$longitudCadena-1);
         
            //Vamos formando la contraseña en cada iteraccion del bucle, añadiendo a la cadena $pass la letra correspondiente a la posicion $pos en la cadena de caracteres definida.
            $pass .= substr($cadena,$pos,1);
        }
        return $pass;

    }
}//class
?>