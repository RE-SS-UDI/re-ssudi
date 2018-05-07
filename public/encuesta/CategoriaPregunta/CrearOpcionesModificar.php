
<?php
	$opcion = $_REQUEST['opcion'];
	$lim = $_REQUEST['lim'];
	$opcion = $opcion+$lim-1;
	for($i=$lim; $i<=$opcion; $i++){
		$data = "$data <label>Opci&oacute;n $i:</label> <input type='text' id='opc$i' name='opc$i'/>";
	}
	
	echo $data;

?>