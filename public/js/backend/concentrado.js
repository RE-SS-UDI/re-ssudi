$(document).ready(function() {
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

    cargaTabla();

});


function muestraEncuesta(persona, encuesta)
{
    $.ajax({
        type: "POST",
        url: '/backend/concentrado/encuesta',
        data: {
            persona_id: persona, encuesta_id: encuesta
        }
        ,success: function(html)
        {
            $("#_dialogo-1").html(html);        
            $("#_dialogo-1").dialog({
                width: "1000",
                height: "auto",
                title: "Encuesta contestada",
                resizable: false,
                draggable: false,
                modal: true
            })//dialog
            
        }//success
        ,error: function(respuesta){
            _mensaje("#_mensaje-1", "Ocurri&oacute; un error inesperado, int&eacute;ntelo de nuevo");
        } //error
    }) //$.ajax

}

function cargaTabla()
{
    console.log("default");

    $.ajax({
        url: '/backend/concentrado/tabla',
        success: function(res){
            $('#cuerpo_tabla').html(res);
        }
    });
    
}

function cargaTablaZona()
{
    // var zona_id = $("#Zid").val();
    // console.log(zona_id);

    var filtro2 = '/backend/concentrado/tabla';

	if ($('#Zid').val() != '') {
		var zona = $('#Zid').val();

          filtro2+="/Zid/"+zona;

          $.ajax({
            url: filtro2,
            success: function(res){
                $('#cuerpo_tabla').html(res);
            }
        });
    
}
}


// function cambiaZona(){
// 	var filtro2 = '/backend/concentrado/tabla';

// 	if ($('#encuestaCat').val() != '') {
// 		var zona = $('#encuestaCat').val();

//           filtro2+="/encuestaCat/"+zona;

//           $.ajax({
//             url: filtro2,
//             success: function(res){
//                 $('#cuerpo_tabla').html(res);
//             }
//         });

// 	//     $("#flexigrid").flexOptions({
// 	// 		url: filtro2,
// 	//         onSuccess: function(){
// 	//         }

// 	// 	}).flexReload();
// 	// }else{
// 	//     $("#flexigrid").flexOptions({
// 	// 		url: filtro2,
// 	//         onSuccess: function(){
// 	//         }

// 	// 	}).flexReload();

// 	}
// }

function updateByEstado(estado_id){
	var filtro2 = '/backend/concentrado/tabla';
    console.log(estado_id);
            filtro2+="/estado_id/"+estado_id;
            
    $.ajax({
        url: filtro2,
        success: function(res){
            $('#cuerpo_tabla').html(res);
        }
    });
}

function updateByEstadoZona(estado_id,zona_id){
	var filtro2 = '/backend/concentrado/tabla';
	console.log(estado_id);
	filtro2+="/estado_id/"+estado_id;
	filtro2+="/zona_id/"+zona_id;

    $.ajax({
        url: filtro2,
        success: function(res){
            $('#cuerpo_tabla').html(res);
        }
    });
}

// function updateByEstado(estado_id){
//     var filtro2 = '/backend/concentrado/grid';
//     console.log(estado_id);
//             filtro2+="/estado_id/"+estado_id;

//     $("#flexigrid").flexOptions({
//         url: filtro2,
//         onSuccess: function(){
//         }

//     }).flexReload();
// }
    

function filtrar()
{
    var filtro='';

    $("#frmFiltrosCategoria :input").each(function(){
        if(this.id!='' && $("#"+this.id).val()!='')
            filtro+="/"+this.name+"/"+$("#"+this.id).val();
    });

    $.ajax({
        url: '/backend/concentrado/tabla'+filtro,
        success: function(res){
            $('#cuerpo_tabla').html(res);
        }
    });
    
}

function limpiar()
{
    cargaTabla();
}

function cambiaEstado(estado_id) {
	console.log("estado seleccionado: "+estado_id.value);
	var estado = estado_id.value;
	
	updateByEstado(estado);

	if (estado != '') {
		$.ajax({
			url: '/backend/concentrado/on-change-estado',
			type: 'POST',
			data: {estado: estado},
			success: function(res){
				console.log('estado cambiado');
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
            var zonas = $('#zona_id');
            zonas.empty();
            var tipo = $('#tipo_id');
			tipo.empty();
			zonas.append('<option value="">-Selecciona una zona-</option>');
			tipo.append('<option value="">-Selecciona un tipo-</option>');
                for (var zona in objJSON) {
                    console.log(objJSON[zona]['nombre']);
                    $('#zona_id').append(
                        $('<option>', {
                        value: objJSON[zona]['id']
                        }).text(objJSON[zona]['nombre'])
                    );
                    // $('#zona_id').append('<option value=' + objJSON[zona]['id'] + '>' + objJSON[zona]['nombre'] + '</option>');
                }
            }
		});
	}
}

function cambiaZona(zona_id){

	var zona = zona_id.value;
	console.log("zona slelected: "+zona);
		var estado = $('#estado_id').val();
		console.log("estado pre-seleccionado: "+estado);
		 updateByEstadoZona(estado,zona);

if (zona != '') {
		$.ajax({
			url: '/backend/concentrado/on-change-zona',
			type: 'POST',
			data: {zona: zona},
			success: function(res){
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
            var tipo = $('#tipo_id');
			tipo.empty();
			$('#tipo_id').append('<option value="" >-Selecciona un tipo-</option>');
                for (var tipo in objJSON) {
                    console.log("de "+objJSON[tipo]['descripcion']);
                    // tipo.append(
                    //     $('<option>', {
                    //     value: objJSON[tipo]['id']
                    //     }).text(objJSON[tipo]['descripcion'])
                    // );
					$('#tipo_id').append('<option value=' + objJSON[tipo]['id'] + '>' + objJSON[tipo]['descripcion'] + '</option>');
                }
            }
		});
	}
}

function cambiaTipo(tipo_id) {
	console.log("tipo seleccionado: "+tipo_id.value);
	var tipo = tipo_id.value;
	var estado = $('#estado_id').val();
	var zona = $('#zona_id').val();
	// updateByEstadoZonaTipo(estado,zona,tipo);
}