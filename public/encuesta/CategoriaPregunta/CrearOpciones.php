
<?php
	$opcion = $_REQUEST['opcion'];
	for($i=1; $i<=$opcion; $i++){
		$data = "$data <label>Opci&oacute;n $i:</label> <input type='text' id='opc$i' name='opc$i'/>";
	}
	
	echo $data;

?>