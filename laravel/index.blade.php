<!-- Creado por Hang Tu Wong Ley Franco -->
<!-- 08/08/2016 -->
@extends('master')
@section('content')

<style>
	[aria-describedby=dialog-message]  .ui-dialog-titlebar-close {
		visibility: hidden;
	}

	input[type="password"] {
		width: 125px;
	}

	input[type="email"] {
		width: 125px;
	}

	#cmbTipoUsuario {
		width: 125px;
	}
</style>




<!-- Se crean los componentes para el formulario -->
<?php
if(!isset($menuPrincipal)){ 
	$lblBuscar = Form::label('txtBuscar', 'Buscar Usuario :', null);
	$txtBuscar = Form::text('txtBuscar', '',array('oninvalid'=>'setCustomValidity(\'Completa este campo\')', 'oninput'=>'setCustomValidity(\'\')','autofocus',
		'title'=>"Escribe aqui para buscar."));

	$lblUsuario = Form::label('txtUsuario', 'Usuario :', null);
	$txtUsuario = Form::text('txtUsuario', '', array('oninvalid'=>'setCustomValidity(\'Completa este campo\')', 'oninput'=>'setCustomValidity(\'\')','required' => 'required'));


	if(isset($update)){
		$lblPassword = Form::label('txtPassword', 'Contraseña:', null);
		$txtPassword = Form::password('txtPassword', array('oninvalid'=>'setCustomValidity(\'Completa este campo\')', 'oninput'=>'setCustomValidity(\'\')'));

		$lblPassword2 = Form::label('txtPassword2', 'Re-Contraseña :', null);
		$txtPassword2 = Form::password('txtPassword2', array('oninvalid'=>'setCustomValidity(\'Completa este campo\')', 'oninput'=>'setCustomValidity(\'\')'));
	}else{
		$lblPassword = Form::label('txtPassword', 'Contraseña:', null);
		$txtPassword = Form::password('txtPassword', array('oninvalid'=>'setCustomValidity(\'Completa este campo\')', 'oninput'=>'setCustomValidity(\'\')','required' => 'required'));

		$lblPassword2 = Form::label('txtPassword2', 'Re-Contraseña :', null);
		$txtPassword2 = Form::password('txtPassword2', array('oninvalid'=>'setCustomValidity(\'Completa este campo\')', 'oninput'=>'setCustomValidity(\'\')','required' => 'required'));
	}

	$lblNombre = Form::label('txtNombre', 'Nombre :', null);
	$txtNombre = Form::text('txtNombre', '', array('oninvalid'=>'setCustomValidity(\'Completa este campo\')', 'oninput'=>'setCustomValidity(\'\')','required' => 'required','autocomplete'=>'off'));

	$lblPaterno = Form::label('txtPaterno', 'Apellido Paterno :', null);
	$txtPaterno = Form::text('txtPaterno', '', array('oninvalid'=>'setCustomValidity(\'Completa este campo\')', 'oninput'=>'setCustomValidity(\'\')','required' => 'required'));

	$lblMaterno = Form::label('txtMaterno', 'Apellido Materno :', null);
	$txtMaterno = Form::text('txtMaterno', '', array('oninvalid'=>'setCustomValidity(\'Completa este campo\')', 'oninput'=>'setCustomValidity(\'\')','required' => 'required'));


	//CORREOS ELECTRONICOS
	$lblCorreo = Form::label('txtCorreo', 'Correo  :', null);
	$txtCorreo = Form::email('txtCorreo', '', array('oninvalid'=>'setCustomValidity(\'Debe introducir un correo electronico\')', 'oninput'=>'setCustomValidity(\'\')'));
	$lblCorreoTest = Form::label('txtCorreoTest', 'Correo Test :', null);
	$txtCorreoTest = Form::email('txtCorreoTest', '', array('oninvalid'=>'setCustomValidity(\'Debe introducir un correo electronico\')', 'oninput'=>'setCustomValidity(\'\')'));
	$lblCorreota = Form::label('txtCorreota', 'Correo tapractico :', null);
	$txtCorreota = Form::email('txtCorreota', '', array('oninvalid'=>'setCustomValidity(\'Debe introducir un correo electronico\')', 'oninput'=>'setCustomValidity(\'\')'));
	//

	$lblTipoUsuario  = Form::label('cmbTipoUsuario', 'Tipo de usuario :', null);
	$cmbTipoUsuario  = Form::select('cmbTipoUsuario', $tipos , null,array('oninvalid'=>'setCustomValidity(\'Completa este campo\')', 'oninput'=>'setCustomValidity(\'\')','placeholder' => 'Seleccione una opción','required' => 'required'));

	$lblStatus = Form::label('lblTipoUsuario', 'Estatus :', null);
	$chkStatus = Form::checkbox('status', '1',null,['id' => 'status','title'=>"Estatus del usuario"]);
}
?>

{!!  Html::setOpenPanel('400px') !!} 
{!!  Html::setTitlePanel('Administración de usuarios') !!} 
{!!  Html::setOpenPanelBody('') !!} 


@if(isset($menuPrincipal))
<!-- Inicia menu principal -->
<div style="text-align: center; width: 100%; border-width: 1px 0 0 0;" >
	
	<a href="/lrvl/administracion/usuarios/nuevo" style="text-decoration: none">
		<button class="ui-button ui-widget ui-corner-all">
			Nuevo <span class="ui-icon ui-icon-plus"></span>
		</button>
	</a>
	
	<a href="/lrvl/administracion/usuarios/editar" style="text-decoration: none">
		<button class="ui-button ui-widget ui-corner-all">
			Editar <span class="ui-icon ui-icon-pencil"></span>
		</button>
	</a>

</div>
<!-- Fin del menu princiapal -->
@else

@if(isset($update))
{!! Form::open(array('url' => '#','onsubmit' => 'return editar()')) !!}
@else
{!! Form::open(array('url' => '#','onsubmit' => 'return guardar()')) !!}
@endif
<table id="form" class="form" border="0" align="center" > 
	<!-- Input para buscar -->
	@if(isset($update))
	<tr>  
		<td>{{$lblBuscar}}</td>
		<td>{{$txtBuscar}}</td>
		<td><button id="btnSearch" class="ui-button ui-widget ui-corner-all" type="button" onclick="search()">
			Buscar <span class="ui-icon ui-icon-search"></span>
		</button></td>
	</tr> 
	@endif
	<tr>
		<td>{!! $lblUsuario !!}</td>
		<td>{!! $txtUsuario !!}</td>
	</tr>
	<tr>
		<td>{{$lblPassword}}</td>
		<td>{{$txtPassword}}</td>
	</tr>
	<tr>
		<td>{{$lblPassword2}}</td>
		<td>{{$txtPassword2}}</td>
	</tr>
	<tr>
		<td>{!! $lblNombre !!}</td>
		<td>{!! $txtNombre !!}</td>
	</tr>
	<tr>
		<td>{!! $lblPaterno !!}</td>
		<td>{!! $txtPaterno !!}</td>
	</tr>
	<tr>
		<td>{!! $lblMaterno !!}</td>
		<td>{!! $txtMaterno !!}</td>
	</tr>
	<tr>
		<td>{!! $lblCorreo !!}</td>
		<td>{!! $txtCorreo !!}</td>
	</tr>

	<tr>
		<td>{!! $lblCorreoTest !!}</td>
		<td>{!! $txtCorreoTest !!}</td>
	</tr>

	<tr>
		<td>{!! $lblCorreota !!}</td>
		<td>{!! $txtCorreota !!}</td>
	</tr>


	<tr>
		<td>{!! $lblTipoUsuario !!}</td>
		<td>{!! $cmbTipoUsuario !!}</td>
	</tr>
	@if(isset($update))
	<tr>
		<td>{{$lblStatus}}</td>
		<td>{{$chkStatus}}</td>
	</tr>
	{{ Form::hidden('userID', null, array('id' => 'userID')) }}
	{{ Form::hidden('userName', null, array('id' => 'userName')) }}
	@endif
</table>

<!-- Seccion de botones para guardar y regresar -->
<div style="text-align: center; width: 100%; margin-top:10px; ">
	<!-- Editar -->
	@if(isset($update))
	<button id="btnUpdate" class="ui-button ui-widget ui-corner-all" type="submit" disabled>
		Guardar <span class="ui-icon ui-icon-check"></span>
	</button>
	<button class="ui-button ui-widget ui-corner-all" type="button" onclick="goBack()">
		Regresar <span class="ui-icon ui-icon-arrowreturnthick-1-w"></span>
	</button>

	<!-- Guardar -->
	@else
	<button class="ui-button ui-widget ui-corner-all" type="submit">
		Guardar <span class="ui-icon ui-icon-check"></span>
	</button>
	<button class="ui-button ui-widget ui-corner-all" type="button" onclick="goBack()">
		Regresar <span class="ui-icon ui-icon-arrowreturnthick-1-w"></span>
	</button>
	@endif
</div>
<!-- Fin de seccion de botones -->

{!!Form::close()!!}

<!-- MODAL PARA BUSCAR -->
<div id="dialog" title="Basic dialog" style="display: none">
	<table id="example" class="display" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>Usuario</th>
				<th>Nombre</th>
				<th>A. Paterno</th>
				<th>A. Materno</th>
				<th>Tipo</th>
				<th>Estatus</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<!-- MODAL PARA MENSAJES DE RESPUESTA -->
<div id="dialog-message" title="" style="display:none">
	<p>
		<span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
		<ul id="response"></ul>
	</p>
</div>

@endif <!--Fin de 'if' menu principal -->

{!!  Html::setClosePanelBody('') !!} 
{!!  Html::setClosePanel() !!} 			
@stop <!--content section-->

@section('code_javascript')
{!! Html::script('js/administracion/usuario.js') !!}
@stop


