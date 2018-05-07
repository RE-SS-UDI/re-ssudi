<ul id="menu">
    <li class="active">
        <a href="../miPerfil/miPerfil.php">
            <i class="icon-arrow"></i>
            <span>INICIO</span>
            <div class="bg-nav"></div>
        </a>
    </li>
	<?php if( $_SESSION["tipoUsuario"] != "Encuestador" ){ ?>
    <li>
        <a href="#" class="sub" tabindex="1">
        <i class="icon-arrow"></i>
        <span>ENCUESTAS</span>
        <div class="bg-nav"></div>
        </a><img src="" alt="" />
        <ul>
			<?php if( $_SESSION["tipoUsuario"] == "Administrador" ){ ?>
				<li><a href="../encuesta/CrearEncuesta.php">Crear nueva</a></li>
			<?php } ?>
			<?php if( $_SESSION["tipoUsuario"] == "Administrador"  || $_SESSION["tipoUsuario"] == "Coordinador" ){?>
				<li><a href="../encuesta/MostrarEncuestas.php">Buscar</a></li>
			<?php } ?>
			<?php if( $_SESSION["tipoUsuario"] == "Administrador" ){ ?>
				<li><a href="../encuesta/ModificarEncuesta.php">Modificar</a></li>
				<li><a href="../encuesta/EliminarEncuesta.php">Eliminar</a></li>
				<li><a href="../encuesta/AbrirEncuesta.php">Abrir</a></li>
				<li><a href="../encuesta/CerrarEncuesta.php">Cerrar</a></li>
			<?php } ?>
			<?php if( $_SESSION["tipoUsuario"] == "Empresa" ){ ?>
				<li><a href="../encuesta/ContestarEncuesta.php">Contestar</a></li>
			<?php } ?>
        </ul>
    </li>
	<?php } ?>
	<?php if( $_SESSION["tipoUsuario"] == "Administrador"  || $_SESSION["tipoUsuario"] == "Coordinador" ){?>
    <li>
        <a href="#" class="sub" tabindex="1">
        <i class="icon-arrow"></i>
        <span>USUARIOS Y PERFILES</span>
        <div class="bg-nav"></div>
        </a><img src="" alt="" />
        <ul>
            <li><a href="../usuario/CrearUsuario.php">Nuevo usuario</a></li>
            <li><a href="../usuario/MostrarUsuarios.php">Buscar usuario</a></li>
            <li><a href="../usuario/ModificarUsuario.php">Modificar usuario</a></li>
            <li><a href="../usuario/EliminarUsuario.php">Eliminar usuario</a></li>
			<?php if( $_SESSION["tipoUsuario"] == "Administrador" ){?>
				<li><a href="#">Crear perfil de usuario</a></li>
				<li><a href="#">Mostrar perfiles</a></li>
				<li><a href="#">Modificar perfil</a></li>
				<li><a href="#">Eliminar perfil</a></li>
			<?php } ?>
        </ul>
    </li>
	<?php } ?>
	<?php if( $_SESSION["tipoUsuario"] == "Administrador" ){?>
    <li>
        <a href="#" class="sub" tabindex="1">
        <i class="icon-arrow"></i>
        <span>PREGUNTAS Y CATEGOR&Iacute;AS</span>
        <div class="bg-nav"></div>
        </a><img src="" alt="" />
        <ul>
            <li><a href="../CategoriaPregunta/CrearCategoria.php">Crear categor&iacute;a</a></li>
            <li><a href="../CategoriaPregunta/MostrarCategoria.php">Buscar categor&iacute;a</a></li>
            <li><a href="../CategoriaPregunta/ModificarCategoria.php">Modificar categor&iacute;a</a></li>
            <li><a href="../CategoriaPregunta/EliminarCategoria.php">Eliminar categor&iacute;a</a></li>
            <li><a href="../CategoriaPregunta/CrearPregunta.php">Crear pregunta</a></li>
            <li><a href="../CategoriaPregunta/MostrarPregunta.php">Buscar pregunta</a></li>
            <li><a href="../CategoriaPregunta/ModificarPregunta.php">Modificar pregunta</a></li>
            <li><a href="../CategoriaPregunta/EliminarPregunta.php">Eliminar pregunta</a></li>
        </ul>
    </li>
	<?php } ?>
	<?php if( $_SESSION["tipoUsuario"] == "Administrador" || $_SESSION["tipoUsuario"] == "Coordinador" ){?>
    <li>
        <a href="#" class="sub" tabindex="1">
        <i class="icon-arrow"></i>
        <span>EMPRESAS</span>
        <div class="bg-nav"></div>
        </a><img src="" alt="" />
        <ul>
            <li><a href="../empresa/CrearEmpresa.php">Crear nueva</a></li>
            <li><a href="../empresa/MostrarEmpresas.php">Buscar</a></li>
            <li><a href="../empresa/ModificarEmpresa.php">Modificar</a></li>
            <li><a href="../empresa/EliminarEmpresa.php">Eliminar</a></li>
            <li><a href="../administracion/MostrarEncuestaEmpresa.php">Encuestas asociadas</a></li>
        </ul>
    </li>
	<?php } ?>	
	<?php if( $_SESSION["tipoUsuario"] == "Administrador" ){?>
    <li>
        <a href="#" class="sub" tabindex="1">
        <i class="icon-arrow"></i>
        <span>ZONAS</span>
        <div class="bg-nav"></div>
        </a><img src="" alt="" />
        <ul>
            <li><a href="../zona/CrearZona.php">Crear nueva</a></li>
            <li><a href="../zona/MostrarZonas.php">Buscar</a></li>
            <li><a href="../zona/ModificarZona.php">Modificar</a></li>
            <li><a href="../zona/EliminarZona.php">Eliminar</a></li>
        </ul>
    </li>
	<?php } ?>
	<?php if( $_SESSION["tipoUsuario"] == "Administrador" || $_SESSION["tipoUsuario"] == "Coordinador" ){?>
    <li>
        <a href="#" class="sub" tabindex="1">
        <i class="icon-arrow"></i>
        <span>ADMINISTRACI&Oacute;N</span>
        <div class="bg-nav"></div>
        </a><img src="" alt="" />
        <ul>
            <li><a href="../administracion/AsignarEncuestaEmpresa.php">Asignar encuestas</a></li>
            <li><a href="../administracion/MostrarEncuestaEmpresa.php">ver encuestas asignadas</a></li>
            <li><a href="../administracion/EliminarEncuestaEmpresa.php">Eliminar encuestas asignadas</a></li>
        </ul>
    </li>
	<?php } ?>
	<?php if( $_SESSION["tipoUsuario"] == "Administrador" ){?>
    <li>
        <a href="#" class="sub" tabindex="1">
        <i class="icon-arrow"></i>
        <span>UTILER&Iacute;AS</span>
        <div class="bg-nav"></div>
        </a><img src="" alt="" />
        <ul>
            <li><a href="#">Descargar formatos</a></li>
            <li><a href="#">Realizar respaldo</a></li>
            <li><a href="#">Recuperar respaldo</a></li>
        </ul>
    </li>
	<?php } ?>
</ul>