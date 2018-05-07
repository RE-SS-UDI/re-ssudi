
<script>
	function VerificarCategoria() {
		var nombre = $('#nombre').val();
		var descripcion = $('#descripcion').val();
		if (nombre=="" || descripcion=="")
			alert ("No pueden quedar campos vac\u00edos");
		else{
			var formData = new FormData(document.getElementById("CrearCategoria"));
			$.ajax({
			url: "CrearCategoriaBD.php",
			type: "post",
			dataType: "html",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success		:function(){
			window.location.href='CrearCategoria.php';
			},error	:function(){}
			});
		}
	}
</script>

<script>
	function MostrarCategoria() {
		var nombre = $('#nombre').val();
		if (nombre=="")
			alert ('No haz ingresado un nombre');
		else{
			$.ajax( {
				url		    :'MostrarCategoriaBD.php?nombre='+nombre,
				type	  	:'post',
				dataType	:'text',
				success		:function(data){
				if (data == "")
					alert ('No existe categoria');
				else
					$('#datos').html(data);
				},error	:function(){}
			});
		}	
	}
</script>

<script>
	function ModificarCategoria() {
		var codigo = $('#codigo').val();
		if (codigo=="")
			alert ('No haz ingresado un c\u00f3digo');
		else{
			$.ajax( {
				url		    :'BuscarCategoriaBD.php?codigo='+codigo,
				type	  	:'post',
				dataType	:'text',
				success		:function(bandera){
				if (bandera == "")
					alert ('No existe categor\u00eda');
				else
					window.location.href='ModificarCategoria.php?codigo='+codigo;
				},error	:function(){}
			});
		}	
	}
</script>

<script>
	function ModificarDatosCategoria() {
		var nombre = $('#nombre').val();
		var descripcion = $('#descripcion').val();
		if (nombre=="" || descripcion=="")
			alert ("No pueden quedar campos vac\u00edos");
		else{
			var formData = new FormData(document.getElementById("forma2"));
			$.ajax({
			url: "ActualizarCategoriaBD.php",
			type: "post",
			dataType: "html",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success		:function(){
				window.location.href='MostrarCategoria.php';
			},error	:function(){}
			});
		}
	}
</script>

<script>
	function ImgModificarCategoria(codigo)
	{
		window.location.href='ModificarCategoria.php?codigo='+codigo;
	}
</script>


<script>
	function ImgEliminarCategoria(codigo){
		var confirmar=confirm('¿Esta seguro de eliminar esta categor\u00eda?');			
		if(confirmar){
			$.ajax({
				url		    :'EliminarCategoriaBD.php?codigo='+codigo,
				type	  	:'post',
				dataType	:'text',
				success		: function()
				{				
					window.location.href='MostrarCategoria.php';
				},error	:function(){
			}	
			}
			);
		}
		}

</script>

<script>
	function EliminarCategoria() {
		var codigo = $('#codigo').val();
		if (codigo=="")
			alert ('No haz ingresado un c\u00f3digo');
		else{
			$.ajax( {
				url		    :'EliminarCategoriaBD.php?codigo='+codigo,
				type	  	:'post',
				dataType	:'text',
				success		:function(){
					window.location.href='MostrarCategoria.php';
				},error	:function(){}
			});
		}	
	}
</script>

