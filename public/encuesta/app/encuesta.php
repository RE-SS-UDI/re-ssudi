<!DOCTYPE html>
<html>
<?php include("head.php"); ?>
<?php include("generarEncuesta.php"); ?>


<body>
    
    <br>
    <div class="container">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <a class="navbar-brand"><?php echo $nombreEncuesta ?></a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <p class="navbar-text">Pregunta <?php echo $numeroPregunta ?></p>
                <ul class="nav navbar-nav navbar-right">
                    <!--<li><a href="#">Limpiar</a></li>-->
                    <p class="navbar-text"><?php echo $categorias[$categoria] ?></p>
                    <li class="dropdown">
                        <a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Ir a Categoria<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <?php
                            $i = 1;
                            foreach($categorias as $key=>$value) {
                                $href = "?categoria=".$i++;
                                echo '<li><a href="'.$href.'">'.$value.'</a></li>';
                            }
                            ?>
                        </ul>
                    </li>
                  </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
        
        <ul class="breadcrumb">
            <?php
            $i = 1;
            foreach($catpre as $key=>$value) {
                if($key == $pregunta){
                    echo '<li class="active">['.$i.']</li>';
                } else {
                    $href = "?pregunta=".$i;
                    echo '<li><a href="'.$href.'">'.$i.'</a></li>';
                }
                $i++;
            }
            ?>
            <!--<li class="active">4</li>-->
        </ul>
        
        <br>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>">
            <div style="min-height: 200px;">
                <?php dibujarPregunta($pregunta);?>
            </div>
            <hr>
            <?php dibujarNavegacion();?>
            <?php dibujarGuardarTermina();?>
        </form>
        
        <br>
        <?php $porcentaje = getPorcentaje();?>
        <div class="progress">
            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $porcentaje;?>"
            aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $porcentaje;?>%">
            <?php echo $porcentaje;?>%
            </div>
        </div>
        <?php
        if(!empty($mensaje)){
        ?>
        <div class="alert alert-danger" role="alert">
          <strong>Error:</strong> <?php echo $mensaje; ?>
        </div>
        <?php
        }
        ?>
    </div>
    
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js" ></script>
</body>
</html>