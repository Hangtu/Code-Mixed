<?php
Route::get('administracion/usuarios', 'Administracion\UsuariosController@index');
Route::get('administracion/usuarios/editar', 'Administracion\UsuariosController@edit');
Route::get('administracion/usuarios/nuevo', 'Administracion\UsuariosController@nuevo');

Route::post('administracion/usuarios/search', 'Administracion\UsuariosController@search');
Route::post('administracion/usuarios/create', 'Administracion\UsuariosController@create');
Route::post('administracion/usuarios/update', 'Administracion\UsuariosController@update');

