urlEliminar='/backend/empresa/eliminar';

$(document).ready(function(){
	var filtro="/backend/empresa/grid";
	if($("#frazon_social").val()!="")       filtro+="/razon_social/"+$("#frazon_social").val();
	if($("#fcalle").val()!="")      filtro+="/calle/"+$("#fcalle").val();
	if($("#festado").val()!="")      filtro+="/estado/"+$("#festado").val();
	if($("#fmunicipio").val()!="")      filtro+="/municipio/"+$("#fmunicipio").val();
	if($("#fstatus").val()!="")      filtro+="/status/"+$("#fstatus").val();
	if($("#fcontacto").val()!="")      filtro+="/contacto/"+$("#fcontacto").val();
	
        $("#flexigrid").flexigrid({
		url: filtro,
		dataType: "xml",
		colModel: [
			{display: "Nombre",           name:"e.nombre",       width: 200, sortable: true, align: "center"},
			{display: "Raz&oacute;n social",           name:"e.razon_social",       width: 200, sortable: true, align: "center"},
			{display: "Domicilio",name:"e.calle",       width: 220, sortable: true, align: "center"},			
			{display: 'Contacto',         name:"e.contacto",     width: 200, sortable : false, align: 'center'},
			{display: 'Tel&eacute;fono',         name:"e.telefono",     width: 100, sortable : false, align: 'center'},
			{display: 'E-mail',         name:"e.email",     width: 150, sortable : false, align: 'center'},
			{display: 'RFC',         name:"e.rfc",     width: 120, sortable : false, align: 'center'},
			{display: 'Estado',         name:"e.estado",     width: 180, sortable : false, align: 'center'},
			{display: 'Municipio',         name:"e.municipio",     width: 180, sortable : false, align: 'center'},
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
