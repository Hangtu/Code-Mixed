<?php
#Desarrollo: Hang Tu Wong Ley Franco
#Fecha: 13-10-2016
include ("../../../sesion/validaAjax.php");
include ("../../../class/class.json.php");
include ("../../../class/class.valida.php");
$db = new db ('postgres' , 'Test');

$accion = "";
$data = "";

if (isset($_POST['data'])) {
	$data = json_decode($_POST['data']);
}

if (isset($data->accion)) {
	$accion = $data->accion; 
}

if(isset($_POST['accion'])){
	$accion = $_POST['accion'];
}

if (isset($_POST['search'])){ 
	$accion = 'search';
	$evento = $_POST['search'];
}


switch ($accion){

	case 'search':
	$qry = "SELECT l.id_licitacion,l.numero_evento,c.razon_social,l.cliente_nuevo,e.estado,l.fecha_licitacion, 
	concat(u.nombre,' ',u.paterno,' ',u.materno),e.id_estado, l.fecha_captura
	FROM vta_licitacion l 
	left  join cat_cliente c on l.id_cliente=c.id_cliente
	left  join cat_estado e on e.id_estado=l.id_estado
	inner join adm_usuario u on u.id_usuario=l.id_usuario_captura
	where UPPER(l.numero_evento) = UPPER('$evento')
	order by l.id_licitacion";

	$rst = $db->query ($qry);
	if ($db->num_rows($rst)){
		echo "Folio a buscar: <b>$evento</b>";
		?>
		<div class="widget_tableDiv">    
			<table id="localidad">
				<thead>
					<tr>
						<td>No. Evento</td>
						<td>Cliente</td>
						<td>Fecha Licitacion</td>
						<td>Captura</td>
					</tr>
				</thead>
				<tbody class="scrollingContent">
					<?php
					while ($row=$db->fetch_array($rst)){
						$cliente = $row['razon_social'];
						if ($cliente == '') {
							$cliente = $row['cliente_nuevo'];
						}

						echo "<tr onclick=\"consultar(
						'$row[id_licitacion]','$row[numero_evento]');\">

						<td>$row[numero_evento]</td>
						<td>$cliente</td>
						<td>$row[fecha_licitacion]</td>
						<td>$row[fecha_captura]</td>
					</tr>";
				}
				?>
			</tbody>
		</table>
	</div>
	<script type="text/javascript">
		window.initTableWidget('localidad',580,250,Array('S','S','S'));
	</script>
	<?php
	echo "<div class=\"note\">
	<table>
		<tr>
			<td>*Nota: </td>
			<td>La busqueda esta limitada a 50 registros.</td>
		</tr>
		<tr>
			<td></td>
			<td>Sino aparece el registro deseado, especifique m&aacute;s su b&uacute;squeda.</td>
		</tr>
	</div>";
}
else
	echo "No se encontraron resultados con la busqueda:";
break;



case 'cargar_licitacion':
$querySQL="SELECT l.id_licitacion,l.numero_evento,c.razon_social,l.cliente_nuevo,e.estado,l.fecha_licitacion, 
concat(u.nombre,' ',u.paterno,' ',u.materno),e.id_estado
FROM vta_licitacion l 
left  join cat_cliente c on l.id_cliente=c.id_cliente
left  join cat_estado e on e.id_estado=l.id_estado
inner join adm_usuario u on u.id_usuario=l.id_usuario_captura
where UPPER(l.numero_evento) = UPPER('$data->evento')
and l.id_licitacion='$data->id_licitacion'
order by l.id_licitacion";
$res  = $db->query($querySQL);

if ($db->num_rows($res) == 0) {
	echo 'No se encontraron resultados';
	http_response_code(400); exit();
}

	$arr = array();// array los datos
	while($r = $db->fetch_assoc($res)){
		$arr = $r;
	}

	//LIMPIA LOS DATOS
	foreach ($arr as $key => $value) {
		$arr[$key] = utf8_encode($value);
	}

	$querySQL = "SELECT a.*, b.nombre as nom_proveedor, c.laboratorio as nom_laboratorio FROM vta_licitacion_detalle a 
	LEFT JOIN cat_proveedor b ON a.proveedor = b.id_proveedor 
	LEFT JOIN cat_laboratorio c ON a.laboratorio = c.id_laboratorio
	WHERE a.id_licitacion=".$arr['id_licitacion']." ORDER BY a.partida";
	$res  = $db->query($querySQL);

	$arrRows = array();
	while($r = $db->fetch_assoc($res)){
		$arrRows [] = $r;
	}

	for ($i=0; $i < sizeof($arrRows); $i++) { 
		foreach ($arrRows[$i] as $key => $value) {
			if ($value == ''){
				$arrRows[$i][$key] = '';
			}
		}
	}	

	$arr['rows'] = $arrRows; // SE AGREGAN LOS DATOS 
	echo json_encode($arr);
	break;


	case 'update':
	$id_licitacion = $data->id_licitacion;
	$rows = json_decode($data->rows,true);

	foreach ($rows as $masterKey => $row) {
		foreach ($row as $key => $value) {
			$rows[$masterKey][$key] = trim(preg_replace('/[^A-Za-z0-9 \s\s+.\-,&]/', '', $value));
		}
	}

    ////INICIAN VALIDACIONES
	foreach($rows AS $Akey => $registro){ 
	// if there is proveedor then we've  to validate the extras values : clave_sistema','costo','precio','laboratorio', 'codigo_barras','empresa'
		if ($rows[$Akey]['proveedor'] == ''){ 
			if(isset($registro['clave_sistema']) && $registro['clave_sistema'] != ''){
				echo "Se necesita un proveedor para agregar la columna Clave Sistema\n";
				http_response_code(401); exit();
			}else{
				unset($rows[$Akey]['clave_sistema']);
				unset($registro['clave_sistema']);
			}

			if(isset($registro['costo']) && $registro['costo'] != ''){
				echo "Se necesita un proveedor para agregar la columna Costo\n";
				http_response_code(401); exit();
			}else{
				unset($rows[$Akey]['costo']);
				unset($registro['costo']);
			}

			if(isset($registro['precio']) && $registro['precio'] != ''){
				echo "Se necesita un proveedor para agregar la columna Precio\n";
				http_response_code(401); exit();
			}else{
				unset($rows[$Akey]['precio']);
				unset($registro['precio']);
			}

			if(isset($registro['laboratorio']) && $registro['laboratorio'] != ''){
				echo "Se necesita un proveedor para agregar la columna Laboratorio\n";
				http_response_code(401); exit();
			}else{
				unset($rows[$Akey]['laboratorio']);
				unset($registro['laboratorio']);
			}

			if(isset($registro['codigo_barras']) && $registro['codigo_barras'] != ''){
				echo "Se necesita un proveedor para agregar la columna Codigo Barras\n";
				http_response_code(401); exit();
			}else{
				unset($rows[$Akey]['codigo_barras']);
				unset($registro['codigo_barras']);
			}

			if(isset($registro['empresa']) && $registro['empresa'] != ''){
				echo "Se necesita un proveedor para agregar la columna Empresa\n";
				http_response_code(401); exit();
			}else{
				unset($rows[$Akey]['empresa']);
				unset($registro['empresa']);
			}
			unset($rows[$Akey]['proveedor']);
			unset($registro['proveedor']);
		}

		foreach ($registro as $key => $value) {

			if ($key == 'clave_cbn' && ($value == '')){
				echo "La columna Clave se encuentra vacia \n";
				http_response_code(401); exit();
			}

			if($key == 'partida' && (($value == '') || !is_numeric($value)  ||  $value < 1)){
				echo "La columna de Partida debe ser un valor númerico mayor a cero o se encuentra vacia\n";
				http_response_code(401); exit();
			}


			if ($key == 'descripcion' && ($value == '') ){
				echo "La columna de Descripción se encuentra vacia \n";
				http_response_code(401); exit();
			}

			if( $key == 'cantidad_minima' && (($value == '') || !is_numeric($value) || ($value) < 1)){
				echo "La columna de Cantidad Minima debe ser mayor a cero, debe ser un valor númerico o se encuentra vacia\n";
				http_response_code(401); exit();
			}

			if( $key == 'cantidad_maxima' &&  $value != ''){
				if ((!is_numeric($value))) {
					echo "La columna de Cantidad Maxima debe ser un valor númerico\n";
					http_response_code(401); exit();
				}
			}

			if( $key == 'cantidad_maxima'){
				if(is_numeric($value) && ($value) < 1){
					echo "La columna de Cantidad Maxima debe ser un valor mayor a cero\n";
					http_response_code(401); exit();
				}
			}


			if ($key == 'proveedor' && ($value == '')){
				echo "La columna Proveedor se encuentra vacia \n";
				http_response_code(401); exit();
			}

			if($key == 'proveedor' && $value != ''){

				$proveedorSQL = "SELECT id_proveedor, nombre FROM cat_proveedor WHERE  upper(nombre) LIKE upper('%$value%') AND estatus = 1";
				$res = $db->query($proveedorSQL);
				$no_resp = $db->num_rows($res);

				if ($no_resp > 1) {
					echo "Ll nombre proveedor debe ser mas especifico, se encontraron dos o mas resultados similares \n";
					http_response_code(401); exit();
				}

				if ($no_resp == 0){
					echo "Ll nombre de proveedor no existe \n";
					http_response_code(401); exit();
				}

				$row = $db->fetch_assoc($res);
				$rows[$Akey]['proveedor'] = $row['id_proveedor'];
			}


			if ($key == 'clave_sistema' && ($value == '') ){
				echo "La columna Clave Corporativo se encuentra vacia \n";
				http_response_code(401); exit();
			}

			if($key == 'clave_sistema' && $value != ''){

				$claveSQL ="SELECT clave FROM catalogo_producto WHERE estatus = 1 AND clave ='$value'";
				$res = $db->query($claveSQL);
				$no_resp = $db->num_rows($res);


				if ($no_resp > 1) {
					echo "La clave esta duplicada \n";
					http_response_code(401); exit();
				}

				if ($no_resp == 0){
					echo "La clave no existe \n";
					http_response_code(401); exit();
				}

			}

			if( $key == 'costo' && (($value == '') || !is_numeric($value) || $value < 1 )){
				echo "La columna de Costo, debe ser un valor númerico mayor a cero o se encuentra vacia\n";
				http_response_code(401); exit();
			}

			if( $key == 'precio' && (($value == '') || !is_numeric($value)|| $value < 1 )){
				echo "La columna de Precio, debe ser un valor númerico mayor a cero o se encuentra vacia\n";
				http_response_code(401); exit();
			}



			if($key == 'laboratorio' && $value != ''){

				$value = str_replace('.', '', $value);

				$laboratorioSQL = "SELECT id_laboratorio, laboratorio FROM cat_laboratorio WHERE  REPLACE(upper(laboratorio),'.','') LIKE upper('%$value%') AND estatus = 1";
				$res = $db->query($laboratorioSQL);
				$no_resp = $db->num_rows($res);

				if ($no_resp > 1) {
					echo "Ll laboratorio debe ser mas especifico, se encontraron dos o mas resultados similares \n";
					http_response_code(401); exit();
				}

				if ($no_resp == 0){
					echo "Ll nombre de laboratorio no existe \n";
					http_response_code(401); exit();
				}

				$row = $db->fetch_assoc($res);
				$rows[$Akey]['laboratorio'] = $row['id_laboratorio'];

			}




			if ($key == 'empresa' && $value != ''){
				if (strtoupper($value) != 'Test' && strtoupper($value) != 'QUIROPRACTICO'){
					echo "La columna Empresa debe ser Test o QUIROPRACTICO : Se encontro $value \n";
					http_response_code(401); exit();
				}
			}



			if ($key == 'empresa' && (strtolower($value) == 'Test') ){
				echo "La columna Empresa debe ser diferente a la que licita \n";
				http_response_code(401); exit();
			}
		}
	}
	

	$claves = array_count_values(array_column($rows, 'clave_cbn')); // CUENTA SI HAY CLAVES REPETIDAS
	$partidas = array_count_values(array_column($rows, 'partida')); // CUNETA SI HAY PARTIDAS REPETIDAS
	foreach ($claves as $key => $clave) {
		if ($clave > 1) {
			echo 'La clave '.$key.' esta duplicada';
			http_response_code(401); exit();
		}
	}
	foreach ($partidas as $key => $partida) {
		if ($partida > 1) {
			echo 'La partida '.$key.' esta duplicada';
			http_response_code(401); exit();
		}
	}

	// //TERMINAN  VALIDACIONES
	$db->query('START TRANSACTION');

	$sqlDelete = "DELETE FROM vta_licitacion_detalle WHERE id_licitacion = $id_licitacion";
	$db->query($sqlDelete);

	foreach($rows AS $masterkey => $row){ // SE RECORRE EL ARREGLO DE TODOS LOS REGISTROS
		$columnNamesSQL = "";
		$createData ="";
		foreach ($row as $key => $value) { // SE RECORREN LOS VALORES DENTRO DE ESE REGISTRO PARA LIMPIARLOS Y CONCATENARLOS
			$value = str_replace(array("\r\n", "\r", "\n"), " ", $value);
			$createData .= !empty($value) ? "'$value'" : "NULL";
			$createData .= ',';
			$columnNamesSQL .= $key.',';
		}
		$columnNamesSQL .='id_licitacion';
		$createData = rtrim($createData,",");
		$createData .= ','.$id_licitacion; // ID QUE VA AL FINAL DEL ARREGLO
		$sql_new_licitacion_detalle = "INSERT INTO vta_licitacion_detalle (".$columnNamesSQL.") VALUES(".strtoupper($createData).")";
		$db->query($sql_new_licitacion_detalle);
	}
	echo 'Se ha actualizado la información';
	$db->query("COMMIT");

	break;





	case 'delete':
	$db->query('START TRANSACTION');
	$SQL = "DELETE FROM vta_licitacion_detalle WHERE id_licitacion = $data->id_licitacion AND partida = '$data->partida' AND clave_cbn = '$data->clave'";
	$db->query($SQL);
	$db->query("COMMIT");
	break;







	case 'upload_excel_file':
	$id_licitacion = $_POST['id_licitacion'];
	$type = $_FILES['licitacion']['type'];
	$tmp_name= $_FILES['licitacion']['tmp_name'];
	$mimes = array('application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

	if($type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
		require_once("../../../librerias/excel2007/PHPExcel.php");
		require_once("../../../librerias/excel2007/PHPExcel/Reader/Excel2007.php");
		$objLeer 		= new PHPExcel_Reader_Excel2007();
		$objPHPExcel	= $objLeer->load($tmp_name);
	} 

	if($type == "application/vnd.ms-excel"){
		require_once("../../../librerias/excel2007/PHPExcel.php");
		require_once("../../../librerias/excel2007/PHPExcel/Reader/Excel5.php");
		$objLeer 		= new PHPExcel_Reader_Excel5();
		$objPHPExcel	= $objLeer->load($tmp_name);
	}

						//SOLO SE TIENEN QUE INSERTAR LOS NOMBRES DE LOS CAMPOS DE LA TABLA '$tableColumns' A LA MISMA POSICION DE '$allTitles' 
	$tableColumns = ['partida','clave_cbn','descripcion','nombre_comercial','presentacion','cantidad_minima','cantidad_maxima'];
	$allTitles = ['PARTIDA','CLAVE','DESCRIPCION','NOMBRE COMERCIAL','PRESENTACION','CANTIDAD MINIMA','CANTIDAD MAXIMA'];
	$requiredTitles = ['PARTIDA','CLAVE','DESCRIPCION','CANTIDAD MINIMA'];
	$titulos=0;
	$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
	$columnNames = array();
	$allData = array();

	//SE INSERTAN OTROS VALORES EN CASO DE EXISTIR EL PROVEEDOR
	foreach ($objWorksheet->getRowIterator() as $row) { // DATOS DINAMICOS DE LOS REQUERIDOS Y NO REQUERIDOS
		$cellIterator = $row->getCellIterator(); 
		$cellIterator->setIterateOnlyExistingCells(false);
		foreach ($cellIterator as $cell) {  //OBTIENE LOS VALORES DE ESE RENGLON Y LOS ALMACENA EN EL ARREGLO
			 $extras[]=$cell->getValue(); //DATOS DE ESE RENGLON
			}
		foreach ($extras as $key => $value){ // SI SE INSERTO LA CABECERA DE PROVEEDOR
			if($value == 'PROVEEDOR'){
				array_push($requiredTitles, 'PROVEEDOR','CLAVE SISTEMA','COSTO','PRECIO');
				array_push($allTitles, 'PROVEEDOR','CLAVE SISTEMA','COSTO','PRECIO', 'LABORATORIO', 'CODIGO BARRAS','EMPRESA');
				array_push($tableColumns, 'proveedor','clave_sistema','costo','precio','laboratorio', 'codigo_barras','empresa');
				break;
			}
		}
		break;
	}




	                   // SE HACE VALIDACION DE LAS CABECERAS  Y SE SACAN LOS DATOS
	foreach ($objWorksheet->getRowIterator() as $row) {
		$cellIterator = $row->getCellIterator(); 
							$cellIterator->setIterateOnlyExistingCells(false); // This loops all cells,
							
							foreach ($cellIterator as $cell) {  //OBTIENE LOS VALORES DE ESE RENGLON Y LOS ALMACENA EN EL ARREGLO
								 $datos[] = trim($cell->getValue()); //DATOS DE ESE RENGLON
								}	


								if($titulos == 0){ // VALIDACION DE LAS CABECERAS

									
									//BORRA TODAS LAS COLUMNAS VACIAS DE TITULOS
									for ($j = (sizeof($datos)-1); $j >= 0; $j--){ 
										if (trim($datos[$j]) == ""){
											unset($datos[$j]);	
										}
									}
									///////////////////////////////////////////

									$titulos = sizeof($datos);
									$requiredTitlesCount = 0;
									$allTitlesCount = 0;


									//SE VERIFICAN SI EXISTEN LAS CABECERAS REQUERIDAS
									$required ="";
									for ($i=0; $i < sizeof($datos); $i++){ 
										for ($j=0; $j < sizeof($requiredTitles); $j++){ 
											if ($datos[$i] == $requiredTitles[$j]){
												$requiredTitlesCount++;			
											}		
										}
									}
									$required = array_diff($requiredTitles,$datos); // BUSCA LAS CABECERAS REQUERIDAS QUE NO EXISTEN
									$required = implode(",",$required);
									//////////////////////////////////////////////////


									// SE VERIFICA LAS CABECERAS REQUERIDAS Y OPCIONALES DEL DOCUMENTO COINCIDAN
									$optional = "";
									for ($i=0; $i < sizeof($datos); $i++){ 
										for ($j=0; $j < sizeof($allTitles); $j++){ 
											if ($datos[$i] == $allTitles[$j]){
												$titles[] = $j; //IDS DE LAS POCIONES DE LOS TITULOS EN EL DOCUMENTO
												$allTitlesCount++;
												$rem [] = $datos[$i];			
											}

										}
									}
									
									$optional = array_diff($datos,$rem);
									$optional = implode(",",$optional);
									/////////////////////////////////////////////////////////////////////////

									for ($i=0; $i < sizeof($titles); $i++){  // SE SACAN LOS NOMBRES DE LOS CAMPOS QUE SE VAN A INSERTAR
										$columnNames [] = $tableColumns[$titles[$i]];
									}



									if ($requiredTitlesCount != sizeof($requiredTitles)){ // SI NO ESTAN TODOS LOS CAMPOS REQUERIDOS
										echo 'FALTAN CABECERAS REQUERIDAS: '.$required;
										exit();
									}


									if ($allTitlesCount != sizeof($datos)){ // SI NO COINCIDE ALGUN NOMBRE DE LOS CAMPOS
										echo 'LAS CABECERAS DEL DOCUMENTO NO COINCIDEN: '.$optional;
										exit();
									}

									if (sizeof($datos) > sizeof($allTitles)){ //  SI HAY MAS CAMPOS EN EL DOCUMENTO DE LOS EXISTEN
										echo 'EXISTEN MAS CAMPOS EN LA CABECERA DE LOS REQUERIDOS';
										exit();
									}

								}else{

									for ($i=0; $i < sizeof($columnNames) ; $i++){ //OBTIENE LOS VALORES DE CADA COLUMNA
										$data[$columnNames[$i]] = trim(preg_replace('/[^A-Za-z0-9 \s\s+.\-,&]/', '', $datos[$i])); //LIMPIA LOS DATOS ANTES DE INSERTARLOS AL ARRAY 
										
									}

									$emptyRows = 0;	 // VERIFICA QUE NO EXISTAN RENGLONES VACIOS								
									foreach ($data as $key => $value) {
										if (trim($value) == ''){
											$emptyRows++;
										}
									}

									if (sizeof($data) != $emptyRows) { // SI EXISTE UN RENGLON VACIO NO LO AGREGA
										$allData[] = $data; // ALMACENA LOS DATOS DEL RENGLON EN UN ARREGLO
									}

								}
							    unset($datos); // LIMPIA LA VARIABLE DATOS QUE CONTIENE LOS DATOS DE LOS CAMPOS DEL DOCUMENTO
							}

							

							$i = 1;
							//START VALIDATION AREA
							foreach($allData AS $Akey => $registro){ 
								// if there is proveedor then we've  to validate the extras values : clave_sistema','costo','precio','laboratorio', 'codigo_barras','empresa'
								if ($allData[$Akey]['proveedor'] == ''){ 
									if(isset($registro['clave_sistema']) && $registro['clave_sistema'] != ''){
										echo "En la fila No. ".($i+1)." se necesita un proveedor para agregar la columna Clave Sistema\n";
										http_response_code(400); exit();
									}else{
										unset($allData[$Akey]['clave_sistema']);
										unset($registro['clave_sistema']);
									}

									if(isset($registro['costo']) && $registro['costo'] != ''){
										echo "En la fila No. ".($i+1)." se necesita un proveedor para agregar la columna Costo\n";
										http_response_code(400); exit();
									}else{
										unset($allData[$Akey]['costo']);
										unset($registro['costo']);
									}

									if(isset($registro['precio']) && $registro['precio'] != ''){
										echo "En la fila No. ".($i+1)." se necesita un proveedor para agregar la columna Precio\n";
										http_response_code(400); exit();
									}else{
										unset($allData[$Akey]['precio']);
										unset($registro['precio']);
									}

									if(isset($registro['laboratorio']) && $registro['laboratorio'] != ''){
										echo "En la fila No. ".($i+1)." se necesita un proveedor para agregar la columna Laboratorio\n";
										http_response_code(400); exit();
									}else{
										unset($allData[$Akey]['laboratorio']);
										unset($registro['laboratorio']);
									}

									if(isset($registro['codigo_barras']) && $registro['codigo_barras'] != ''){
										echo "En la fila No. ".($i+1)." se necesita un proveedor para agregar la columna Codigo Barras\n";
										http_response_code(400); exit();
									}else{
										unset($allData[$Akey]['codigo_barras']);
										unset($registro['codigo_barras']);
									}

									if(isset($registro['empresa']) && $registro['empresa'] != ''){
										echo "En la fila No. ".($i+1)." se necesita un proveedor para agregar la columna Empresa\n";
										http_response_code(400); exit();
									}else{
										unset($allData[$Akey]['empresa']);
										unset($registro['empresa']);
									}
									unset($allData[$Akey]['proveedor']);
									unset($registro['proveedor']);
								}

								foreach ($registro as $key => $value) {

									if ($key == 'clave_cbn' && ($value == '')){
										echo "En la fila No. ".($i+1)." la columna Clave se encuentra vacia \n";
										http_response_code(400); exit();
									}

									if($key == 'partida' && (($value == '') || !is_numeric($value)  ||  $value < 1)){
										echo "En la fila No. ".($i+1)." la columna de Partida debe ser un valor númerico mayor a cero o se encuentra vacia\n";
										http_response_code(400); exit();
									}


									if ($key == 'descripcion' && ($value == '') ){
										echo "En la fila No. ".($i+1)." la columna de Descripción se encuentra vacia \n";
										http_response_code(400); exit();
									}

									if( $key == 'cantidad_minima' && (($value == '') || !is_numeric($value) || ($value) < 1)){
										echo "En la fila No. ".($i+1)." la columna de Cantidad Minima debe ser mayor a cero, debe ser un valor númerico o se encuentra vacia\n";
										http_response_code(400); exit();
									}

									if( $key == 'cantidad_maxima' &&  $value != ''){
										if ((!is_numeric($value))) {
											echo "En la fila No. ".($i+1)." la columna de Cantidad Maxima debe ser un valor númerico\n";
											http_response_code(400); exit();
										}
									}

									if( $key == 'cantidad_maxima'){
										if(is_numeric($value) && ($value) < 1){
											echo "En la fila No. ".($i+1)." la columna de Cantidad Maxima debe ser un valor mayor a cero\n";
											http_response_code(400); exit();
										}
									}


									if ($key == 'proveedor' && ($value == '')){
										echo "En la fila No. ".($i+1)." la columna Proveedor se encuentra vacia \n";
										http_response_code(400); exit();
									}

									if($key == 'proveedor' && $value != ''){

										$proveedorSQL = "SELECT id_proveedor, nombre FROM cat_proveedor WHERE  upper(nombre) LIKE upper('%$value%') AND estatus = 1";
										$res = $db->query($proveedorSQL);
										$no_resp = $db->num_rows($res);

										if ($no_resp > 1) {
											echo "En la fila No. ".($i+1)." el nombre proveedor debe ser mas especifico, se encontraron dos o mas resultados similares \n";
											http_response_code(400); exit();
										}

										if ($no_resp == 0){
											echo "En la fila No. ".($i+1)." el nombre de proveedor no existe \n";
											http_response_code(400); exit();
										}

										$row = $db->fetch_assoc($res);
										$allData[$Akey]['proveedor'] = $row['id_proveedor'];

									}


									if ($key == 'clave_sistema' && ($value == '') ){
										echo "En la fila No. ".($i+1)." la columna Clave Corporativo se encuentra vacia \n";
										http_response_code(400); exit();
									}

									if($key == 'clave_sistema' && $value != ''){

										$claveSQL ="SELECT clave FROM catalogo_producto WHERE estatus = 1 AND clave ='$value'";
										$res = $db->query($claveSQL);
										$no_resp = $db->num_rows($res);


										if ($no_resp > 1) {
											echo "En la fila No. ".($i+1)." la clave esta duplicada \n";
											http_response_code(400); exit();
										}

										if ($no_resp == 0){
											echo "En la fila No. ".($i+1)." la clave no existe \n";
											http_response_code(400); exit();
										}

									}

									if( $key == 'costo' && (($value == '') || !is_numeric($value) || $value < 1 )){
										echo "En la fila No. ".($i+1)." la columna de Costo, debe ser un valor númerico mayor a cero o se encuentra vacia\n";
										http_response_code(400); exit();
									}

									if( $key == 'precio' && (($value == '') || !is_numeric($value)|| $value < 1 )){
										echo "En la fila No. ".($i+1)." la columna de Precio, debe ser un valor númerico mayor a cero o se encuentra vacia\n";
										http_response_code(400); exit();
									}


									if($key == 'laboratorio' && $value != ''){

										$value = str_replace('.', '', $value);

										$laboratorioSQL = "SELECT id_laboratorio, laboratorio FROM cat_laboratorio WHERE  REPLACE(upper(laboratorio),'.','') LIKE upper('%$value%') AND estatus = 1";
										$res = $db->query($laboratorioSQL);
										$no_resp = $db->num_rows($res);

										if ($no_resp > 1) {
											echo "En la fila No. ".($i+1)." el laboratorio debe ser mas especifico, se encontraron dos o mas resultados similares \n";
											http_response_code(400); exit();
										}

										if ($no_resp == 0){
											echo "En la fila No. ".($i+1)." el nombre de laboratorio no existe \n";
											http_response_code(400); exit();
										}

										$row = $db->fetch_assoc($res);
										$allData[$Akey]['laboratorio'] = $row['id_laboratorio'];

									}




									if ($key == 'empresa' && $value != ''){
										if (strtoupper($value) != 'Test' && strtoupper($value) != 'QUIROPRACTICO'){
											echo "En la fila No. ".($i+1)." la columna Empresa debe ser Test o QUIROPRACTICO : Se encontro $value \n";
											http_response_code(400); exit();
										}
									}



									if ($key == 'empresa' && (strtolower($value) == 'Test') ){
										echo "En la fila No. ".($i+1)." la columna Empresa debe ser diferente a la que licita \n";
										http_response_code(400); exit();
									}
								}
								$i++;
							}

							$claves = array_count_values(array_column($allData, 'clave_cbn')); // CUENTA SI HAY CLAVES REPETIDAS
							$partidas = array_count_values(array_column($allData, 'partida')); // CUNETA SI HAY PARTIDAS REPETIDAS

							foreach ($claves as $key => $clave) {
								if ($clave > 1) {
									echo 'La clave '.$key.' esta duplicada';
									http_response_code(400); exit();
								}
							}

							foreach ($partidas as $key => $partida) {
								if ($partida > 1) {
									echo 'La partida '.$key.' esta duplicada';
									http_response_code(400); exit();
								}
							}
						    //END VALIDATIONS AREA

							$db->query('START TRANSACTION');
							$sqlDelete = "DELETE FROM vta_licitacion_detalle WHERE id_licitacion = $id_licitacion";
							$db->query($sqlDelete);
							foreach($allData AS $masterkey => $row){ 
								$columnNamesSQL = "";
								$createData ="";
								foreach ($row as $key => $value) { 
									$value = str_replace(array("\r\n", "\r", "\n"), " ", $value);
									$createData .= !empty($value) ? "'$value'" : "NULL";
									$createData .= ',';
									$columnNamesSQL .= $key.',';
								}
								$columnNamesSQL .='id_licitacion';
								$createData = rtrim($createData,",");
								$createData .= ','.$id_licitacion; 
								$sql_new_licitacion_detalle = "INSERT INTO vta_licitacion_detalle (".$columnNamesSQL.") VALUES(".strtoupper($createData).")";
								$db->query($sql_new_licitacion_detalle);
							}
							$db->query("COMMIT");
							echo 'Se ha actualizado la información';
							break;
}// end switch
?>              