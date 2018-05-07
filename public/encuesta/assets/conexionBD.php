<?php
	$servername = "XHIROX\SQLEXPRESS";
    $username = "admin";
    $password = "admin123";
    $dbname = "ca02_db";
	$connectionInfo = array( "Database"=>"ca02_db", "UID"=>"admin", "PWD"=>"admin123");
	$conn = sqlsrv_connect( $servername, $connectionInfo);
	
	/*$servername = "webmay.nubecinema.com";
    $username = "nubecine_benja";
    $password = "castillo8";
    $dbname = "nubecine_enterprisesurvey_2";*/
	
	if (!$conn){
		echo "Error conectando al Servidor";
		exit();
	}


    // Create connection
//    $conn = new mysqli($servername, $username, $password, $dbname);

  
?>