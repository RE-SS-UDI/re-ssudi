<header>
    <div class="container container-content">
    <div class="row">

        <!-- logo -->
        <div class="col-md-4 col-xs-4">
            <img id="logo" src="../assets/img/principal.png" border="0" height=60 />
        </div>
        <!-- logo -->
        
        <div class="col-md-4 col-md-offset-4 col-xs-4 col-xs-offset-4">
            <div class="row">
                <div class="col-md-9 col-xs-9">
                    <p class="text-right"><a href="../miPerfil/miPerfil.php">Bienvenido <?php echo $_SESSION["Usuario"];?></a></p>
                    <p class="text-right"><a href="../miPerfil/cerrarSesion.php">Cerrar Sesion </a></p>
                </div>
                <div class="col-md-3 col-xs-3">
                    <img src="../files/profile/foto.png" width="50" height="50" >
                </div>
            </div>
        </div>

    </div>
    </div>
</header>