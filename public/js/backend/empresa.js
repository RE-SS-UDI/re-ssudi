urlEliminar='/backend/empresa/eliminar';

	//var path = "ingreso/salir";
	//var basePath = request.getScheme()+"://"+request.getServerName()+":"+request.getServerPort()+path+"/";
	
$(document).ready(function(){

	var value = __obtenerTiempo ();

	var server = window.location.hostname;
	var link = "http://"+server+"/backend/ingreso/salir";

    $('#mainContent').idle({
       onIdle: function(){
        $.ajax({url: link, success: function(result){
                        __mensajeSinBoton('#_mensaje-1',  'Cerrando aplicacion por falta de actividad...');
                        __delayRefreshPage(600);
        }});
          },
          idle: value //10 segundos
        })

	var filtro="/backend/empresa/grid";
	if($("#fnombre").val()!="")       filtro+="/nombre/"+$("#fnombre").val();
	// if($("#frazon_social").val()!="")       filtro+="/razon_social/"+$("#frazon_social").val();
	// if($("#fcalle").val()!="")      filtro+="/calle/"+$("#fcalle").val();
	if($("#festado").val()!="")      filtro+="/estado/"+$("#festado").val();
	if($("#fstatus").val()!="")      filtro+="/status/"+$("#fstatus").val();
	if($("#fcontacto").val()!="")      filtro+="/contacto/"+$("#fcontacto").val();
	if($("#fzona").val()!="")      filtro+="/zona/"+$("#fzona").val();
	
        $("#flexigrid").flexigrid({
		url: filtro,
		dataType: "xml",
		colModel: [
			{display: "Nombre",           name:"e.nombre",       width: 210, sortable: true, align: "center"},
//			{display: "Raz&oacute;n social",           name:"e.razon_social",       width: 200, sortable: true, align: "center"},
//			{display: "Domicilio",name:"e.calle",       width: 220, sortable: true, align: "center"},			
			{display: 'Contacto',         name:"e.contacto",     width: 200, sortable : false, align: 'center'},
			{display: 'Tel&eacute;fono',         name:"e.telefono",     width: 100, sortable : false, align: 'center'},
			{display: 'Zona',         name:"e.zona_id",     width: 150, sortable : false, align: 'center'},
//			{display: 'RFC',         name:"e.rfc",     width: 120, sortable : false, align: 'center'},
			{display: 'Estado',         name:"e.estado",     width: 180, sortable : false, align: 'center'},
			{display: 'Estatus',           name:"e.status",       width: 100, sortable : false, align: 'center'},
			{display: 'Editar',           name:"editar",       width: 100, sortable : false, align: 'center'},
			{display: 'Habilitar/deshabilitar',         name:"eliminar",     width: 150, sortable : false, align: 'center'}
		],
		sortname: "e.razon_social"
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


        validaRegistro();
        cambiaEstado($('#estado_id').val());
});


function validaRegistro()
{
	if ($("#id").val() != '') {
		$("#frmEmpresa").attr('readonly','readonly');
	}
}

function cambiaEstado(id)
{
	if (id != '') {
		$.ajax({
			url: '/backend/empresa/obtiene-municipios',
			type: 'POST',
			data: {id: id},
			success: function(res){
				$('#municipio_id').html(res);

				if ($('#muni').val() != '') {
			        $('#municipio_id').val($('#muni').val());
				}		
			}
		});
	}
}

function guardarEmpresa(formulario)
{
    $("#"+formulario).validate({
        submitHandler: function(frm)
        {
            
            $(frm).ajaxSubmit({
                success: function(respuesta){
                    if(!isNaN(respuesta))
                    {   
//                        recargar();
                        _mensaje('#_mensaje-3',  'Se ha guardado de forma correcta.');
                    }else{                       
                        _mensaje('#_mensaje-3',  respuesta );
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

function updateByEstado(estado_id){
	var filtro2 = '/backend/empresa/grid';
console.log(estado_id);
			filtro2+="/estado/"+estado_id;

		$("#flexigrid").flexOptions({
			url: filtro2,
			onSuccess: function(){
			}

		}).flexReload();
	}

function updateByEstadoZona(estado_id,zona_id){
	var filtro2 = '/backend/empresa/grid';
	console.log(estado_id);
	filtro2+="/estado/"+estado_id;
	filtro2+="/zona/"+zona_id;

	$("#flexigrid").flexOptions({
		url: filtro2,
		onSuccess: function(){
		}

	}).flexReload();
}

function cambiaEstado(estado_id) {
	console.log("estado seleccionado: "+estado_id.value);
	var estado = estado_id.value;

	// updateByEstado(estado);

	if (estado != '') {
		$.ajax({
			url: '/backend/empresa/on-change-estado',
			type: 'POST',
			data: {estado: estado},
			success: function(res){
				console.log('estado cambiado');
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
			var zonas = $('#zona');
			var zonas2 = $('#zona_id');
			zonas.empty();
			zonas2.empty();
			zonas.append('<option value="">Selecciona una zona</option>');
			zonas2.append('<option value="">Selecciona una zona</option>');
			// tipo.append('<option value="">Selecciona un tipo</option>');
                for (var zona in objJSON) {
                    console.log(objJSON[zona]['nombre']);
                    zonas.append(
                        $('<option>', {
                        value: objJSON[zona]['id']
                        }).text(objJSON[zona]['nombre'])
					);
					zonas2.append(
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
		var estado = $('#estado').val();
		console.log("estado pre-seleccionado: "+estado);
		//  updateByEstadoZona(estado,zona);

// if (zona != '') {
// 		$.ajax({
// 			url: '/backend/empresa/on-change-zona',
// 			type: 'POST',
// 			data: {zona: zona},
// 			success: function(res){
//                 var objJSON = eval("(function(){return " + res + ";})()");
//                 // var response = $.parseJSON(res);
//                 // console.log("sucess " + objJSON[0].nombre);
//             var tipo = $('#tipo_id');
// 			tipo.empty();
// 			$('#tipo_id').append('<option value="" >Selecciona un tipo</option>');
//                 for (var tipo in objJSON) {
//                     console.log("de "+objJSON[tipo]['descripcion']);
//                     // tipo.append(
//                     //     $('<option>', {
//                     //     value: objJSON[tipo]['id']
//                     //     }).text(objJSON[tipo]['descripcion'])
//                     // );
// 					$('#tipo_id').append('<option value=' + objJSON[tipo]['id'] + '>' + objJSON[tipo]['descripcion'] + '</option>');
//                 }
//             }
// 		});
// 	}
}

function shakeEmpys(){
    // var cat = $('#categoria_id').val();

    var zonaf = document.getElementById("zona");
    var estadoF = document.getElementById("estado");

    if(zonaf.value === "") {
        // console.log("falta " +catF.value);
        zonaf.classList.add("apply-shake");
    }
    if(estadoF.value === "") {
        // console.log("falta " +catF.value);
        estadoF.classList.add("apply-shake");
    }

    zonaf.addEventListener("animationend", (e) => {
        zonaf.classList.remove("apply-shake");
    });
    estadoF.addEventListener("animationend", (e) => {
        estadoF.classList.remove("apply-shake");
    });
}