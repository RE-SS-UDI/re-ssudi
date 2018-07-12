$(document).ready(function() {
    $('#fecha_nacimiento').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: "-87:-15",//rango del calendario seleccionable
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

var modal = document.getElementById('id01');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function inicioSesion()
{
  
    $("#frmLogin").validate({
        submitHandler: function(form){
            $(form).ajaxSubmit({
                success: function(respuesta){

                        if(respuesta=="0"){
                            _mensajePersonalizado2("#_mensaje-3", "El usuario y/o contraseña son incorrectos.");
                            //bandera = false;
                        }else{
                            //bandera = true;
                             window.location.href ="/backend/index/";
                        }
                } //success
                ,error: function(respuesta){
                    _mensaje("#_mensaje-3", respuesta);
                } //error
            }) //ajaxSubmit
        } //submitHandler
    }) //validate
     $("#frmLogin").submit(); 

}

function _mensajePersonalizado2(id, texto){

    $(id).html(texto); 
                        
    $(id).dialog({              
        width: "auto",
        height: "auto",
        title: "¡Atención!",
        resizable: false,
        draggable:false,
        modal: true,
        buttons: [              
            {
                id: "aceptar-999",
                text : "Aceptar",
                class: "btn btn-rojo-1",
                click: function(){
                    //location.href="/index";
                    $(id).dialog("close");
                }
            }
        ] //buttons
    }); //dialog
}

function guardarPreRegistro(formulario)
{
    $("#"+formulario).validate({
        submitHandler: function(frm)
        {
            
            $(frm).ajaxSubmit({
                success: function(respuesta){
                    if(!isNaN(respuesta))
                    {   
//                        recargar();
                        _mensaje('#_mensaje-3',  'Se ha guardadó de forma correcta el pre-registro, se le enviará un correo cuando sea aceptado por los coordinadores mencionando nuevas indicaciones.');
                        $('#'+formulario)[0].reset();
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


function onChangeEstado(estado_id)
{
	console.log("estado seleccionado: "+estado_id.value);
    var estado = estado_id.value;

	if (estado != '') {
		$.ajax({
			url: '/index/pre-register-onchange-estado',
			type: 'POST',
			data: {estado: estado},
			success: function(res){
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
            var zonas = $('#zona_id');
            zonas.empty();
            var tipo = $('#tipo_id');
            tipo.empty();
            $('#zona_id').append('<option value="">-Selecciona una zona-</option>');
            $('#tipo_id').append('<option value="">-Selecciona una zona-</option>');
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

function onChangeZona(zona_id)
{
	console.log("zona seleccionada: "+zona_id.value);
    var zona = zona_id.value;

	if (zona != '') {
		$.ajax({
			url: '/index/pre-register-onchange-zona',
			type: 'POST',
			data: {zona: zona},
			success: function(res){
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
            var tipo = $('#tipo_id');
            tipo.empty();
            $('#tipo_id').append('<option value="">-Selecciona una zona-</option>');
                for (var tipo in objJSON) {
                    console.log(objJSON[tipo]['descripcion']);
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
