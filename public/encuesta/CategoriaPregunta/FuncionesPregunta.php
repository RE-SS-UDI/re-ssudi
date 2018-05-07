
<script>
	function modificar_pregunta(){
		var opcion=document.formulario.respuesta.value;
		var opc;
		if (opcion==2)
		{
			opc = '<label>Opci&oacute;n 1:</label> <input type="text" value=""/>';
			opc+= '<label>Opci&oacute;n 2:</label> <input type="text" value=""/>';
			opc+= '<label>Opci&oacute;n 3:</label> <input type="text" value=""/>';				
		}
		else 
			opc='';
		$('#opciones').html(opc);
	}
</script>
<script>
	function crear_pregunta(){
		var opcion = $('#respuesta').val();
		var opc;
		if (opcion==2){
			opc= '<label>Selecciona n\u00famero de opciones</label>';
			opc+= '<select name="opc" id="opc" onchange="crear_opciones();">';
			opc+= '<option value="0"> Selecciona </option>';
			opc+= '<option value="2"> 2 </option>';
			opc+= '<option value="3"> 3 </option>';
			opc+= '<option value="4"> 4 </option>';
			opc+= '<option value="5"> 5 </option>';
			opc+= '</select>';			
		}
		else{
			opc='';
			$('#opciones').html(opc);
		} 
		$('#numOpciones').html(opc);
	}
	
	function crear_opciones(){
		var opcion = $('#opc').val();
		$.ajax( {
			url		    :'CrearOpciones.php?opcion='+opcion,
			type	  	:'post',
			dataType	:'text',
			success		:function(data){
			$('#opciones').html(data);
			},error	:function(){}
		});
	}
</script>

<script>
	function validar_pregunta(){
		var categoria = $('#categoria').val();
		var pregunta = $('#pregunta').val();
		var opcion = $('#respuesta').val();
		var bandera = "1";
		if (opcion==2){
			var numOpc = $('#opc').val();
			if (categoria=="0" || pregunta=="" || numOpc=="0")	{	
				alert ("No pueden quedar campos vac\u00edos");
				bandera = "0";
			}
			var opc1 = $('#opc1').val();
			var opc2 = $('#opc2').val();
			if (numOpc=="2"){
				if (opc1=="" || opc2==""){
					alert ("No haz terminado de definir las opciones");
					bandera = "0";}
			}
			if (numOpc=="3"){
				var opc3 = $('#opc3').val();
				if (opc1=="" || opc2=="" || opc3==""){
					alert ("No haz terminado de definir las opciones");
					bandera = "0";}
			}
			if (numOpc=="4"){
				var opc3 = $('#opc3').val();
				var opc4 = $('#opc4').val();
				if (opc1=="" || opc2=="" || opc3=="" || opc4==""){
					alert ("No haz terminado de definir las opciones");
					bandera = "0";}
			}
			if (numOpc=="5"){
				var opc3 = $('#opc3').val();
				var opc4 = $('#opc4').val();
				var opc5 = $('#opc5').val();
				if (opc1=="" || opc2=="" || opc3=="" || opc4=="" || opc5==""){
					alert ("No haz terminado de definir las opciones");
					bandera = "0";}
			}
		}
		else if (categoria=="0" || pregunta=="" || opcion=="0")	{	
				alert ("No pueden quedar campos vac\u00edos");	
				bandera = "0";
		}
		if (bandera=="1"){
			var formData = new FormData(document.getElementById("formulario"));	
			$.ajax({
			url: "CrearPreguntaBD.php",
			type: "post",
			dataType: "html",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success		:function(codigo){
				window.location.href='CrearPregunta.php';
			},error	:function(){}
			});
		}	
	}

</script>


<script>
	function MostrarPregunta() {
		var codigo = $('#codigo').val();
		if (codigo=="")
			alert ('No haz ingresado un c\u00f3digo');
		else{
			$.ajax( {
				url		    :'MostrarPreguntaBD.php?codigo='+codigo,
				type	  	:'post',
				dataType	:'text',
				success		:function(data){
				if (data == "")
					alert ('No existe pregunta');
				else
					$('#datos').html(data);
				},error	:function(){}
			});
		}	
	}
</script>

<script>
	function ModificarPregunta() {
		var codigo = $('#codigo').val();
		if (codigo=="")
			alert ('No haz ingresado un c\u00f3digo');
		else{
			$.ajax( {
				url		    :'BuscarPreguntaBD.php?codigo='+codigo,
				type	  	:'post',
				dataType	:'text',
				success		:function(bandera){
				if (bandera == "")
					alert ('No existe pregunta');
				else
					window.location.href='ModificarPregunta.php?codigo='+codigo;
				},error	:function(){}
			});
		}	
	}
	
	
</script>

<script>
	function ImgModificarPregunta(codigo)
	{
		window.location.href='ModificarPregunta.php?codigo='+codigo;
	}
</script>

<script>
	function crear_preguntaMod(){
		var opcion = $('#respuesta').val();
		var opc;
		if (opcion==2){
			opc= '<label>Selecciona n\u00famero de opciones</label>';
			opc+= '<select name="opc" id="opc" onchange="crear_opciones();">';
			opc+= '<option value="0"> Selecciona </option>';
			opc+= '<option value="2"> 2 </option>';
			opc+= '<option value="3"> 3 </option>';
			opc+= '<option value="4"> 4 </option>';
			opc+= '<option value="5"> 5 </option>';
			opc+= '</select>';			
		}
		else{
			opc='';
			$('#opciones').html(opc);
			$('#opcionesMod').html(opc);
			$('#numOpcionesMod').html(opc);
		} 
		$('#numOpciones').html(opc);
	}
	
	function crear_opcionesModificar(numOpc){
		var opcion = $('#opc').val();
		if (opcion>=numOpc){
			opcion=opcion-numOpc;
			var lim =  parseInt(numOpc)+1;
		
			$.ajax( {
				url		    :'CrearOpcionesModificar.php?opcion='+opcion+'&lim='+lim,
				type	  	:'post',
				dataType	:'text',
				success		:function(data){
				$('#opciones').html(data);
				},error	:function(){}
			});
		}
		else{
			$.ajax( {
				url		    :'CrearOpciones.php?opcion='+opcion,
				type	  	:'post',
				dataType	:'text',
				success		:function(data){
					var opc='';
					$('#opcionesMod').html(opc);
					$('#opciones').html(data);
				},error	:function(){}
			});
		}
	}
</script>


<script>
	function validar_preguntaMod(){
		var categoria = $('#categoria').val();
		var pregunta = $('#pregunta').val();
		var opcion = $('#respuesta').val();
		var bandera = "1";
		if (opcion==2){
			var numOpc = $('#opc').val();
			if (categoria=="0" || pregunta=="" || numOpc=="0")	{	
				alert ("No pueden quedar campos vac\u00edos");
				bandera = "0";
			}
			var opc1 = $('#opc1').val();
			var opc2 = $('#opc2').val();
			if (numOpc=="2"){
				if (opc1=="" || opc2==""){
					alert ("No haz terminado de definir las opciones");
					bandera = "0";}
			}
			if (numOpc=="3"){
				var opc3 = $('#opc3').val();
				if (opc1=="" || opc2=="" || opc3==""){
					alert ("No haz terminado de definir las opciones");
					bandera = "0";}
			}
			if (numOpc=="4"){
				var opc3 = $('#opc3').val();
				var opc4 = $('#opc4').val();
				if (opc1=="" || opc2=="" || opc3=="" || opc4==""){
					alert ("No haz terminado de definir las opciones");
					bandera = "0";}
			}
			if (numOpc=="5"){
				var opc3 = $('#opc3').val();
				var opc4 = $('#opc4').val();
				var opc5 = $('#opc5').val();
				if (opc1=="" || opc2=="" || opc3=="" || opc4=="" || opc5==""){
					alert ("No haz terminado de definir las opciones");
					bandera = "0";}
			}
		}
		else if (categoria=="0" || pregunta=="" || opcion=="0")	{	
				alert ("No pueden quedar campos vac\u00edos");	
				bandera = "0";
		}
		if (bandera=="1"){
			var formData = new FormData(document.getElementById("formulario"));	
			$.ajax({
			url: "ActualizarPreguntaBD.php",
			type: "post",
			dataType: "html",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success		:function(codigo){
				window.location.href='MostrarPregunta.php';
			},error	:function(){}
			});
		}	
	}

</script>


<script>
	function EliminarPregunta() {
		var codigo = $('#codigo').val();
		if (codigo=="")
			alert ('No haz ingresado un c\u00f3digo');
		else{
			$.ajax( {
				url		    :'EliminarPreguntaBD.php?codigo='+codigo,
				type	  	:'post',
				dataType	:'text',
				success		:function(){
					window.location.href='MostrarPregunta.php';
				},error	:function(){}
			});
		}	
	}
</script>

<script>
	function ImgEliminarPregunta(codigo){
		var confirmar=confirm('¿Esta seguro de eliminar esta pregunta?');			
		if(confirmar){
			$.ajax({
				url		    :'EliminarPreguntaBD.php?codigo='+codigo,
				type	  	:'post',
				dataType	:'text',
				success		: function()
				{				
					window.location.href='MostrarPregunta.php';
				},error	:function(){
			}	
			}
			);
		}
	}
</script>
