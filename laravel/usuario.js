// Creado por Hang Tu Wong Ley Franco
// 08/08/2016
require(
	['datatables','multiselect', 'backbone'],
	
	function(datatables,multiselect, backbone){


		jQuery(document).ready(function($) {
			

			$("#txtBuscar").keyup(function(event){
				if(event.keyCode == 13){
					$("#btnSearch").click();
				}
			});

			$('html').bind('keypress', function(e)
			{
				if(e.keyCode == 13)
				{
					return false;
				}
			});
		});

		search = function () {

			var search = $('#txtBuscar').val();

			if (search == ''){
				alertDialog(0,'Escriba algo en el campo para buscar');
				return;
			}
			
			params = {
				search: search
			};

			//CREA LA MODAL PARA BUSCAR USUARIOS
			$( "#dialog" ).dialog({
				title: "Buscar Usuario",
				height: 400,
				width: 700,
				modal: true,
				show: {
					effect: "blind",
					duration: 300
				},
				hide: {
					effect: "explode",
					duration: 300
				},
				close: function(){
					$('#example').empty();
					$('#example').DataTable().destroy();
				}
			});

			//CONFIGURACION Y CARGA DE LOS DATOS CON LA LIBRERIA DATATABLE
			var table = $('#example').DataTable({
				"scrollY":        "300px",
				"destroy": true,
				"scrollCollapse":  true,    
				"bInfo" : false,
				"bPaginate":          false,
				"bFilter"      : false,
				"order": [[ 0, "asc" ]],
				ajax:{
					type: 'POST',
					url: '/lrvl/administracion/usuarios/search',
					dataSrc: '',
					data:params
				},
				"columns": [
				{ "data": "usuario" },
				{ "data": "nombre" },
				{ "data": "paterno" },
				{ "data": "materno" },
				{ "data": "descripcion" },
				{ "data": "estatus" }]
			});

			//EVENTO PARA MANEJAR CADA RENGLON DE LA TABLA DE RESULTADOS
			$('#example tbody').on('click', 'tr', function(){
				$('#example').empty();
				var data = table.row( this ).data();
				$('#userID').val(data.id_usuario);
				$('#txtUsuario').val(data.usuario);
				$('#userName').val(data.usuario)
				$('#txtNombre').val(data.nombre);
				$('#txtPaterno').val(data.paterno);
				$('#txtMaterno').val(data.materno);
				$('#txtCorreo').val(data.correo_electronico);
				$('#txtCorreo').val(data.correo_electronico);
				$('#txtCorreoQuiro').val(data.correo_electronico);
				$('#cmbTipoUsuario').val(data.id_tipo);
				$('#status').prop('checked', data.estatus);
				$("#dialog").dialog( "close" );
				$("#btnUpdate").attr({
					disabled: false
				});
			});
			clean();
		  } //finaliza funcion serch()

		  guardar = function (){
		  	$.post('create', $('form').serialize(), function(data) {
		  		alertDialog(1,data);
		  	}).fail(function(data) {
		  		alertDialog(0,data.responseText);
		  	})
		  	return false; // Submit sin refresh
		  }

		  editar = function (){
		  	$.post('update', $('form').serialize(), function(data) {
		  		alertDialog(1,data);
		  	}).fail(function(data) {
		  		alertDialog(0,data.responseText);
		  	})
		  	return false; 
		  }

		  clean = function () {
		  	$('#txtBuscar').val('');
		  	$('#txtPassword').val('');
		  	$('#txtPassword2').val('');
		  }

		  alertDialog = function (title, str){
		  	var x = title;
		  	if(title == 1) {title = 'Operación Exitosa'}
		  		else if(title == 0){ title = 'Error!';}
		  	else{title = title};
		  	$( "#response" ).append( "<div id='test'><b>"+str+"</b></div>" );
		  	$( "#dialog-message" ).dialog({
		  		modal: true,
		  		title: title,
		  		buttons: {
		  			Cerrar: function() {
		  				$( "#test" ).remove();
		  				$( this ).dialog( "close" );
		  				if(x == 1){
		  					goBack();
		  				}
		  			}
		  		}
		  	});
		  }

		  goBack = function () {
		  	window.history.back();
		  }

});//finaliza require
