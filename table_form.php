<?php
/*
		Nombre: Hang Tu Wong Ley Franco
		Fecha:2016-10-19
*/
		include_once("class/forms.php");
		include_once("sesion/validaAjax.php");
		include_once("class/tabla.php");
		$db= new DB("postgres", "");
		$form = new Form();
		?>

		<style>
			.cell > input {
				width: 97%; 
			}
		</style>
		
		<input type="hidden" id="licitacionID">
		<input type="hidden" id="noEventoID">
		
		<center>
			<?php 	
			$form->setTitle('Edici&oacute;n de Licitaciones', '430px');
			$form->bgTop("430px");?>
			<table>
				<tr>
					<td><?php $form->label("","Buscar Evento","");?></td>
					<td><?php $form->text("evento","{validar:alfanumerico};{maxlength:20};");?></td>
					<td>
						<?php 
						$form->button("consultar", "{img:iconos/search.png}; {texto: Consultar}; {funcion:onclick=buscar()};");
						?>
					</td>
				</tr>
			</table>
			<?php $form->bgBottom();?>
			<br/>
		</center>


		<?php 	
		 $form->bgTop("1450px",1);?>
		<table>
			<tr>
				<td><?php $form->label("","N&uacute;mero Evento","");?></td>
				<td><?php $form->text("txt_evento","{validar:alfanumerico};{maxlength:20};{name:evento};{readonly:readonly};{width:100px}","");?></td>
				<td><?php $form->label("","Cliente",'');?></td>
				<td><?php $form->text("txt_cliente","{name:cliente};{readonly:readonly};{width:495px};","");?></td>
			</tr>
			<tr>
				<td><?php $form->label("", "Fecha Licitaci&oacute;n","")?></td>
				<td><?php $form->text("txt_fecha_licitacion","{validar:numeros};{maxlength:20};{name:f_lic};{readonly:readonly};{width:100px}");?></td>
				<td><?php $form->label("","Estado ","");?></td>
				<td><?php $form->text("txt_estado","{name:estado};{readonly:readonly};{width:200px};","");?></td>
			</tr>
		</table>
		<br/>


		<div style="width: 1420px; height: 200px; overflow: auto">			
			<table cellspacing="0" cellpadding="0" style="width: 1400px;">
				<thead>
					<tr>
						<td style="width: 10px" class="table_head"></td>
						<td style="width: 50px" class="table_head">Partida</td>
						<td style="width: 150px" class="table_head">Clave CBN</td>
						<td style="width: 330px" class="table_head">Descripci&oacute;n</td>
						<td style="width: 200px" class="table_head">Nombre Comercial</td>
						<td style="width: 80px" class="table_head">Presentacion</td>
						<td style="width: 80px" class="table_head">Cantidad Minima</td>
						<td style="width: 80px" class="table_head">Cantidad Maxima</td>
						<td style="width: 330px" class="table_head">Proveedor</td>
						<td style="width: 150px" class="table_head">Clave Sistema</td>
						<td style="width: 80px" class="table_head">Costo</td>
						<td style="width: 80px" class="table_head">Precio</td>
						<td style="width: 150px" class="table_head">Laboratorio</td>
						<td style="width: 200px" class="table_head">Codigo Barras</td>
						<td style="width: 80px" class="table_head">Empresa</td>
						<td style="width: 40px" class="table_head"><img src="iconos/add.png" onclick="add_row()" style="cursor:pointer;"></td>
						<td style="width: 10px" class="table_head"></td>
					</tr>
				</thead>
				<tbody id="tbl_licitaciones"></tbody>
			</table>
		</div>

		<div class="line" style="height: 30px;"></div>
		
		<center>
			<table>
				<tr>
					<td>
						<form action="" method="post" enctype="multipart/form-data" id="frm_licitacion" style="display:inline-block;">
							<?php $form->label("", "Importar Archivo",true)?>
							<input type="file" name="licitacion" id='licitacion' accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/>
						</form>
						<?php 
						$form->button("", "{img:iconos/arrow_up.png}; {texto: Cargar Excel}; {funcion:onclick=cargar_excel()}");
						?>
						<a href="http://servertest.com/ventas/licitaciones/eventos/ejemplo.xlsx" download>
						  <img src="iconos/help.png"  border="0" onclick="alert('Se ha descargado un archivo ejemplo')" />
						</a>
					</td>
				</tr>
				<tr>
					<td align="center"><?php $form->button("btn_guardar", "{img:iconos/layout_edit.png}; {texto: Guardar};{funcion:onclick=modificar()};");?></td>
				</tr>
			</table>
		</center>
		<?php
		$form->bgBottom();
		?>
