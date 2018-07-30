urlEliminar='/backend/pre-registro/eliminar';

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
	var filtro="/backend/pre-registro/grid";
	if($("#fnombre").val()!="")       filtro+="/nombre/"+$("#fnombre").val();
	if($("#fpaterno").val()!="")       filtro+="/paterno/"+$("#fpaterno").val();
	if($("#fmaterno").val()!="")       filtro+="/materno/"+$("#fmaterno").val();
	
        $("#flexigrid").flexigrid({
		url: filtro,
		dataType: "xml",
		colModel: [
			{display: "Nombre",           name:"pr.nombre",       width: 370, sortable: true, align: "center"},
			{display: 'Correo',         name:"pr.correo",     width: 250, sortable : false, align: 'center'},
			{display: 'Tel&eacute;fono',         name:"pr.telefono",     width: 150, sortable : false, align: 'center'},
			{display: 'Estado',         name:"es.estado",     width: 110, sortable : false, align: 'center'},
			{display: 'Estatus',           name:"pr.status",       width: 100, sortable : false, align: 'center'},
			{display: 'Visualizar',           name:"editar",       width: 100, sortable : false, align: 'center'},
			{display: 'Eliminar',         name:"eliminar",     width: 100, sortable : false, align: 'center'}
		],
		sortname: "pr.nombre"
		,sortorder: "asc"
		,usepager: true
        ,useRp: false
        ,singleSelect: true
        ,resizable: false
        ,showToggleBtn: false
        ,rp: 10
        ,width: 1200
        ,height: 400
	});

    $('#fecha_nacimiento').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        maxDate: "+0d",
        dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        dayNamesShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
        monthNames: 
            ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
            "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
        monthNamesShort: 
            ["Ene", "Feb", "Mar", "Abr", "May", "Jun",
            "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]
    });        
});

function guardarPreregistro(formulario, frmFiltro, filtroinicial, urlImprimir, urlExportar)
{
	if (confirm('¿Desea aceptar el registro y volverlo un usuario de RESSUDI?')) {
	    $("#"+formulario).validate({
	        submitHandler: function(frm)
	        {
	            
	            $(frm).ajaxSubmit({
	                success: function(respuesta){
	                    if(!isNaN(respuesta))
	                    {   
							
	                        $("#_dialogo-1").dialog("close");
	                        recargar();
	                        _mensaje('#_mensaje-1',  'Se guardó de forma correcta');                       
	                    }else{    
							$("#_dialogo-1").dialog("close");
							recargar();                  
							// _mensaje("#_mensaje-1",  respuesta ); 
							_mensaje("#_mensaje-1",  'Se guardó de forma correcta' );
							
	                    }                        
	                        
	                } //success
	                ,error: function(respuesta){
	                    _mensaje("#_mensaje-1", "Ocurri&oacute; un error inesperado, int&eacute;ntelo de nuevo");
	                } //error
	            }) //ajaxSubmit*/
	        } //submitHandler
	    }) //validate
	    
	    $("#"+formulario).submit();     
	}
}

function updateByZona(zona_id){
	var filtro2 = '/backend/pre-registro/grid';
console.log(zona_id);
  		filtro2+="/zona_id/"+zona_id;

	    $("#flexigrid").flexOptions({
			url: filtro2,
	        onSuccess: function(){
	        }

		}).flexReload();
	}

function updateByEstado(estado_id){
	var filtro2 = '/backend/pre-registro/grid';
console.log(estado_id);
			filtro2+="/estado_id/"+estado_id;

		$("#flexigrid").flexOptions({
			url: filtro2,
			onSuccess: function(){
			}

		}).flexReload();
	}

	
function updateByEstadoZona(estado_id,zona_id){
	var filtro2 = '/backend/pre-registro/grid';
	console.log(estado_id);
	filtro2+="/estado_id/"+estado_id;
	filtro2+="/zona_id/"+zona_id;

	$("#flexigrid").flexOptions({
		url: filtro2,
		onSuccess: function(){
		}

	}).flexReload();
}

function updateByEstadoZonaTipo(estado_id,zona_id,tipo_id){
	var filtro2 = '/backend/pre-registro/grid';
	console.log(estado_id);
	filtro2+="/estado_id/"+estado_id;
	filtro2+="/zona_id/"+zona_id;
	filtro2+="/tipo_id/"+tipo_id;

	$("#flexigrid").flexOptions({
		url: filtro2,
		onSuccess: function(){
		}

	}).flexReload();
}

function updateByEstadoZonaTipoNone(){
	var estado = $('#estado_idS').val();
	var zona = $('#zona_idS').val();
	var tipo = $('#tipo_idS').val();
	var filtro2 = '/backend/pre-registro/grid';
	filtro2+="/estado_id/"+estado;
	filtro2+="/zona_id/"+zona;
	filtro2+="/tipo_id/"+tipo;

	$("#flexigrid").flexOptions({
		url: filtro2,
		onSuccess: function(){
		}

	}).flexReload();
}


function cambiaZona(zona_id){

	var zona = zona_id.value;
	console.log("zona slelected: "+zona);
		var estado = $('#estado_idS').val();
		console.log("estado pre-seleccionado: "+estado);
		//  updateByEstadoZona(estado,zona);

if (zona != '') {
		$.ajax({
			url: '/backend/pre-registro/on-change-zona',
			type: 'POST',
			data: {zona: zona},
			success: function(res){
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
            var tipo = $('#tipo_idS');
			tipo.empty();
			$('#tipo_idS').append('<option value="">Selecciona un tipo</option>');
                for (var tipo in objJSON) {
                    console.log("de "+objJSON[tipo]['descripcion']);
                    // tipo.append(
                    //     $('<option>', {
                    //     value: objJSON[tipo]['id']
                    //     }).text(objJSON[tipo]['descripcion'])
                    // );
                     $('#tipo_idS').append('<option value=' + objJSON[tipo]['id'] + '>' + objJSON[tipo]['descripcion'] + '</option>');
                }
            }
		});
	}
}

function cambiaEstado(estado_id) {
	console.log("estado seleccionado: "+estado_id.value);
	var estado = estado_id.value;
	
	// updateByEstado(estado);

	if (estado != '') {
		$.ajax({
			url: '/backend/pre-registro/on-change-estado',
			type: 'POST',
			data: {estado: estado},
			success: function(res){
				console.log('estado cambiado');
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
            var zonas = $('#zona_idS');
            zonas.empty();
            var tipo = $('#tipo_idS');
			tipo.empty();
			$('#zona_idS').append('<option value="">Selecciona una zona</option>');
			$('#tipo_idS').append('<option value="">Selecciona un tipo</option>');
                for (var zona in objJSON) {
                    console.log(objJSON[zona]['nombre']);
                    zonas.append(
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

function cambiaTipo(tipo_id) {
	var tipo = tipo_id.value;
	var estado = $('#estado_idS').val();
	var zona = $('#zona_idS').val();
	console.log("tipo seleccionado: "+tipo_id.value + " Estado: "+ estado + " Zona:" + zona);
	
	// updateByEstadoZonaTipo(estado,zona,tipo);
	
	// if (tipo != '') {
	// 	$.ajax({
	// 		url: '/backend/pre-registro/on-change-tipo',
	// 		type: 'POST',
	// 		data: {tipo: tipo},
	// 		success: function(res){
	// 			console.log('tipo cambiado');
    //             var objJSON = eval("(function(){return " + res + ";})()");
    //             // var response = $.parseJSON(res);
    //             // console.log("sucess " + objJSON[0].nombre);
    //         var tipo = $('#tipo_idS');
    //         tipo.empty();
    //             for (var zona in objJSON) {
    //                 console.log(objJSON[zona]['nombre']);
    //                 zonas.append(
    //                     $('<option>', {
    //                     value: objJSON[zona]['id']
    //                     }).text(objJSON[zona]['nombre'])
    //                 );
    //                 // $('#zona_id').append('<option value=' + objJSON[zona]['id'] + '>' + objJSON[zona]['nombre'] + '</option>');
    //             }
    //         }
	// 	});
	// }
}

function shakeEmpys(){
    // var cat = $('#categoria_id').val();
    var tipoF = document.getElementById("tipo_idS");
    var zonaf = document.getElementById("zona_idS");
    var estadoF = document.getElementById("estado_idS");


    if(tipoF.value === "") {
        // console.log("falta " +catF.value);
        tipoF.classList.add("apply-shake");
    }
    if(zonaf.value === "") {
        // console.log("falta " +catF.value);
        zonaf.classList.add("apply-shake");
    }
    if(estadoF.value === "") {
        // console.log("falta " +catF.value);
        estadoF.classList.add("apply-shake");
    }

    tipoF.addEventListener("animationend", (e) => {
        tipoF.classList.remove("apply-shake");
    });
    zonaf.addEventListener("animationend", (e) => {
        zonaf.classList.remove("apply-shake");
    });
    estadoF.addEventListener("animationend", (e) => {
        estadoF.classList.remove("apply-shake");
    });
}