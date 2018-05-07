<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $titulo; ?></title>
    <link rel="icon" href="../assets/img/ett.ico">

    <!-- Bootstrap -->
    <link href="../assets/css/reset.css" rel="stylesheet" type="text/css" media="all">
    <link href="../assets/css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <link href="../assets/css/style.css" rel="stylesheet" type="text/css" media="all">
    <link href="../assets/css/font.css" rel="stylesheet" type="text/css" media="all">
    <!-- end Bootstrap -->
    
    <link rel="stylesheet" type="text/css" href="../assets/css/styleForm.css" media="all" />
    
    <?php if(isset($extraCSS) && !empty($extraCSS)):
            foreach ($extraCSS as $css):
    ?><link href="../assets/css/<?= $css ?>.css" rel="stylesheet" type="text/css" media="all">
    <?php   endforeach;
    endif?>
    
    <!--- CSS Para la version mobil --->
    <link href="../assets/css/mobile.css" rel="stylesheet" type="text/css" media="all">

    <link href='http://fonts.googleapis.com/css?family=Lato:400,400italic,700,700italic,900,900italic,300italic,300,100italic,100' rel='stylesheet' type='text/css'>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"> </script>
    <script type="text/javascript" src="../assets/js/jquery-1.3.2.min.js" ></script>
    <script type="text/javascript" src="../assets/js/jquery-ui.min.js" ></script>
    <script type="text/javascript" src="../assets/js/jquery.min.js" ></script>
    <script type="text/javascript" src="../assets/js/jquery.easing.min.js" ></script>

    <?php if(isset($extraJS) && !empty($extraJS)):  
            foreach ($extraJS as $js):
    ?><script type="text/javascript" src="../assets/js/<?= $js ?>.js"></script>
    <?php   endforeach;
    endif?>
	
	<?php include('../CategoriaPregunta/FuncionesCategoria.php'); ?>
	<?php include('../CategoriaPregunta/FuncionesPregunta.php'); ?>
</head>