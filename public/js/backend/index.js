

$(document).ready(function(){
	var value = __obtenerTiempo ();
	

	$('#mainContent').idle({
       onIdle: function(){
       	$.ajax({url: "ingreso/salir", success: function(result){
                        __mensajeSinBoton('#_mensaje-1',  'Cerrando aplicacion por falta de actividad...');
                        __delayRefreshPage(600);
                        
	    }});
		  },
		  idle: value  //10 segundos
		})


});


