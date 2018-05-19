urlEliminar='/backend/mensaje/eliminar';

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
    
    $('#fecha_limite').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        minDate: "+0d"
    });

});



function cambiaTipoUsuario(id,idZona)
{
	if (id != '') {
		$.ajax({
			url: '/backend/mensaje/obtener-usuario',
			type: 'POST',
			data: {

				id: id,
				idZona: idZona

			},
			success: function(res){
				$('#usuario_destino').html(res);

				if ($('#usu').val() != '') {
			        $('#usuario_destino').val($('#usu').val());
				}		
			}
		});
	}
}


function permisos(id){
	$.ajax({
		type: "POST"
		,url: "/backend/mensaje/permisos"
		,data: {
			id: id
		}
		,success: function(html){
			$("#_dialogo-1").html(html);
			$("#frm2").validate({
				submitHandler: function(form){
					$("#es-1").removeClass('hide');
					$("#frm2").addClass('hide');
					$("#aceptar-1").addClass('hide');
					$("#cancelar-1").addClass('hide');
					$(form).ajaxSubmit({
						success: function(respuesta){
							$("#_dialogo-1").dialog("close");
						}
					})
				}
			})
			$("#_dialogo-1").dialog({
				width: "900"
				,height: "auto"
				,title: "Usuario"
				,resizable: false
				,draggable: false
				,modal: true
				,buttons: [
				]//buttons
			})//dialog
		}
	});
}//function




function guardarPermisos(){
    $("#frm-2").validate({
        submitHandler: function(form){
            $(form).ajaxSubmit({
                success: function(respuesta){
                    if(!isNaN(respuesta)){   
                        $("#_dialogo-1").dialog("close");
			 			window.location="/backend/mensaje";                   
                    }else{                       
                    	_mensaje("#_mensaje-1",  respuesta );
                    }                        
                        
                } //success
                ,error: function(respuesta){
                    _mensaje("#_mensaje-1", "Ocurri&oacute; un error inesperado, int&eacute;ntelo de nuevo");
                } //error
            }) //ajaxSubmit
        } //submitHandler
    }) //validate
    $("#frm-2").submit();    
}