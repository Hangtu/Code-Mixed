<?php
#Desarrollo: Hang Tu Wong Ley Franco
#Fecha: 13-10-2016
include ("../../../sesion/validaAjax.php");
include ("../../../class/class.json.php");
include ("../../../class/class.valida.php");
$db = new db ('postgres' , '');

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

switch ($accion){

	case 'search':
	$evento = $_POST['evento'];
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

						echo "<tr onclick=\"search_selected(
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

	$querySQL = "SELECT fd.*, ofe.oferente,ld.descripcion,ld.nombre_comercial,ld.presentacion,ld.laboratorio
	FROM vta_licitacion_fallo_detalle fd
	INNER JOIN cat_proveedor_oferente ofe on ofe.id_oferente=fd.id_oferente
	INNER JOIN vta_licitacion_detalle ld on ld.id_licitacion=fd.id_licitacion and fd.partida=ld.partida and fd.clave_cbn=ld.clave_cbn
	WHERE fd.id_licitacion='$data->id_licitacion'";

	$res  = $db->query($querySQL);

	$arrRows = array();
	while($r = $db->fetch_assoc($res)){
		$arrRows [] = $r;
	}

	for ($i=0; $i < sizeof($arrRows); $i++) { 
		foreach ($arrRows[$i] as $key => $value) {
			if ($value == ''){
				$arrRows[$i][$key] = '';
			}else{
				$arrRows[$i][$key] = utf8_encode($value);
			}
		}
	}

	$querySQL = "SELECT id_oferente, oferente FROM cat_proveedor_oferente";
	$res = $db->query($querySQL);

	$arrProvedores = array();
	while($r = $db->fetch_assoc($res)){
		$arrProvedores [] = $r;
	}

     for ($i=0; $i < sizeof($arrProvedores); $i++){ //LIMPIA LOS DATOS A UTF_8
     	foreach ($arrProvedores[$i] as $key => $value) {
     		$arrProvedores[$i][$key] = utf8_encode($value);
     	}
     }

     $editable = '1';
     $querySQL = "SELECT id_licitacion FROM vta_licitacion_fallo WHERE id_licitacion = '$data->id_licitacion'";
     $res = $db->query($querySQL);

     if ($db->num_rows($res) != 0) {
     	$editable = 0;
     }


     $arr['edit'] = $editable;
     $arr['provedores'] = $arrProvedores;
     $arr['rows'] = $arrRows; 
     echo json_encode($arr);
     break;


     case 'delete':
     $db->query('START TRANSACTION');
     $SQL = "DELETE FROM vta_licitacion_fallo_detalle WHERE id_licitacion = $data->id_licitacion AND partida = '$data->partida' AND clave_cbn = '$data->clave'
     AND id_oferente = '$data->id_oferente'";
     $db->query($SQL);
     $db->query("COMMIT");
     break;


     case 'add_provedor':

     $response = array();

     $name = utf8_decode(trim(preg_replace('/[^A-Za-z0-9 \s\s+.\-,&]ÁÉÍÓÚáéíóú\s/', '', $data->name)));
     $name = strtoupper(str_replace("'","",$name));

     if($name == ''){
     	echo 'Ingrese un nombre valido';
     	http_response_code(401); exit();
     }

     $fecha = date('Y-m-d H:i:s');
     $id_usuario = $_SESSION['idUser'];
     $sql = "SELECT id_oferente, oferente FROM cat_proveedor_oferente WHERE oferente = '$name'";
     $res = $db->query($sql);
     if($db->num_rows($res) == 0){
     	$sql = "INSERT INTO cat_proveedor_oferente (oferente, id_usuario_captura, fecha_captura) VALUES ('$name','$id_usuario','$fecha') RETURNING id_oferente";
     	$resp = $db->query($sql);
     	$row  = $db->fetch_assoc($resp);
     	$response['id_oferente'] = $row['id_oferente'];
     	$response['name'] = utf8_encode($name);
     	echo json_encode($response);
     }else{
     	echo 'Ese proveedor ya se encuentra registrado';
     	http_response_code(401);
     }
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
     $tableColumns = ['partida','clave_cbn','cantidad','costo','id_oferente','marca','cantidad_maxima'];
     $allTitles = ['PARTIDA','CLAVE CLIENTE','CANTIDAD ADJUDICADA','PRECIO ADJUDICADO','PROVEEDOR ADJUDICADO','MARCA','CANTIDAD MAXIMA'];
     $requiredTitles = ['PARTIDA','CLAVE CLIENTE','CANTIDAD ADJUDICADA','PRECIO ADJUDICADO','PROVEEDOR ADJUDICADO'];
	$titulos=0; // if 0 is reading headers; if 1 is reading data 
	$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
	$columnNames = array(); //$column names of sql ordered by 
	$allData = array(); // array with all data when foreach end of get them 

	// SE HACE VALIDACION DE LAS CABECERAS  Y SE SACAN LOS DATOS
	foreach ($objWorksheet->getRowIterator() as $row) {
		$cellIterator = $row->getCellIterator(); 
							$cellIterator->setIterateOnlyExistingCells(false); // This loops all cells,
							
							foreach ($cellIterator as $cell) {  //OBTIENE LOS VALORES DE ESE RENGLON Y LOS ALMACENA EN EL ARREGLO
								  $datos[] = strtoupper(trim($cell->getValue())); //DATOS DE ESE RENGLON
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

							//print_r($allData); exit();
							

							$i = 1;
							//START VALIDATION AREA
							foreach($allData AS $Akey => $registro){ 

								$SQL = "SELECT id_licitacion, partida, clave_cbn FROM  vta_licitacion_detalle WHERE partida ='".$allData[$Akey]['partida']."' AND clave_cbn = '".$allData[$Akey]['clave_cbn']."' AND id_licitacion = '$id_licitacion'";
								

								$res = $db->query($SQL);

								if($db->num_rows($res) == 0){
									echo "En la fila No. ".($i+1)." no se encontro la partida ".$allData[$Akey]['partida']." ligada con la clave ".$allData[$Akey]['clave_cbn']."\n";
									http_response_code(400); exit();
								}



								if ($allData[$Akey]['cantidad'] > $allData[$Akey]['cantidad_maxima']) {
									echo "En la fila No. ".($i+1)." la cantidad adjudicada no puede ser mayor a la cantidad maxima";
									http_response_code(400); exit();
								}

								foreach ($registro as $key => $value) {

									if($key == 'partida' && (($value == '') || !is_numeric($value)  ||  $value < 1)){
										echo "En la fila No. ".($i+1)." la columna de Partida debe ser un valor númerico mayor a cero o se encuentra vacia\n";
										http_response_code(400); exit();
									}

									if($key == 'partida' && $value != '' ){
										$SQL =  "SELECT id_licitacion, partida FROM vta_licitacion_detalle WHERE id_licitacion = '$id_licitacion' AND partida = '$value'";
										$res =  $db->query($SQL);
										$no_rows = $db->num_rows($res);

										if ($no_rows == 0) {
											echo "En la fila No. ".($i+1)." la Partida no existe en la licitacion\n";
											http_response_code(400); exit();
										}
									}


									if ($key == 'clave_cbn' && ($value == '')){
										echo "En la fila No. ".($i+1)." la columna Clave Cliente se encuentra vacia \n";
										http_response_code(400); exit();
									}

									if($key == 'clave_cbn' && $value != '' ){
										$SQL =  "SELECT id_licitacion, clave_cbn FROM vta_licitacion_detalle WHERE id_licitacion = '$id_licitacion' AND clave_cbn = '$value'";
										$res =  $db->query($SQL);
										$no_rows = $db->num_rows($res);

										if ($no_rows == 0) {
											echo "En la fila No. ".($i+1)." la Clave no existe en la licitacion\n";
											http_response_code(400); exit();
										}
									}


									if( $key == 'cantidad' && (($value == '') || !is_numeric($value) || floor($value) <= 0)){
										echo "En la fila No. ".($i+1)." la columna de Cantidad Adjudica debe ser mayor a cero, debe ser un valor númerico o se encuentra vacia\n";
										http_response_code(400); exit();
									}

									if( $key == 'costo' && (($value == '') || !is_numeric($value) || $value <=0)){
										echo "En la fila No. ".($i+1)." la columna de Precio Adjudicado debe ser mayor a cero, debe ser un valor númerico o se encuentra vacia\n";
										http_response_code(400); exit();
									}

									if( $key == 'cantidad_maxima' && $value != ''){
										if (!is_numeric($value) || floor($value) <= 0){
											echo "En la fila No. ".($i+1)." la columna de Cantidad Maxima debe ser mayor a cero, debe ser un valor númerico o se encuentra vacia\n";
											http_response_code(400); exit();
										}
									}

									if ($key == 'id_oferente' && ($value == '')){
										echo "En la fila No. ".($i+1)." la columna Proveedor Adjudicado se encuentra vacia \n";
										http_response_code(400); exit();
									}

									if($key == 'id_oferente' && $value != ''){

										$value = str_replace(',','', str_replace('.', '', $value));
										$proveedorSQL = "SELECT id_oferente, oferente FROM cat_proveedor_oferente WHERE  REPLACE(REPLACE(upper(oferente),'.',''),',','') LIKE upper('%$value%')";
										$res = $db->query($proveedorSQL);
										$no_resp = $db->num_rows($res);

										if ($no_resp > 1) {
											echo "En la fila No. ".($i+1)." el nombre del Proveedor Adjudicado debe ser mas especifico, se encontraron dos o mas resultados similares \n";
											http_response_code(400); exit();
										}

										if ($no_resp == 0){
											echo "En la fila No. ".($i+1)." el nombre del Proveedor Adjudicado no existe \n";
											http_response_code(400); exit();
										}

										$row = $db->fetch_assoc($res);
										$allData[$Akey]['id_oferente'] = $row['id_oferente'];

									}
								}
								$i++;
							}

							$db->query('START TRANSACTION');
							$sqlDelete = "DELETE FROM vta_licitacion_fallo_detalle WHERE id_licitacion = $id_licitacion";
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
								$sql_new_licitacion_detalle = "INSERT INTO vta_licitacion_fallo_detalle (".$columnNamesSQL.") VALUES(".strtoupper($createData).")";
								$db->query($sql_new_licitacion_detalle);
							}
							$db->query("COMMIT");
							echo 'Se ha actualizado la información';
							break;














							case 'update':
							$id_licitacion = $data->id_licitacion;
							$rows = json_decode($data->rows,true);
							foreach ($rows as $masterKey => $row) {
								foreach ($row as $key => $value) {
									$rows[$masterKey][$key] =  str_replace("'",'',utf8_decode(trim(preg_replace('/[^A-Za-z0-9 \s\s+.\-,&]ÁÉÍÓÚáéíóú\s/', '', $value)))); 
								}
							}
							//VALIDACIONES 
							foreach($rows AS $masterKey => $row){ 
								foreach ($row as $key => $value){
									
									// if ($key == 'clave_sistema' && ($value == '') ){
									// 	echo "La columna Clave Corporativo se encuentra vacia \n";
									// 	http_response_code(401); exit();
									// }

								}
							}
							//TERMINAN VALIDACIONES 
							//print_r($rows); exit();
							foreach($rows AS $masterkey => $row){ 
								foreach ($row as $key => $value) { 
									$value = str_replace(array("\r\n", "\r", "\n"), " ", $value);
									$value = (!empty($value) || $value == '0' )  ? "'$value'" : "NULL";
									$sqlUpdate = "UPDATE vta_licitacion_fallo_detalle SET  $key = $value WHERE id_licitacion  = $id_licitacion AND partida ='".$rows[$masterkey]['partida']."'"; 
									$db->query($sqlUpdate);	
								}
							}
							echo ' Se ha actualizado la información';
							break;




							case 'finalizar':
							
							$db->query('START TRANSACTION');
							
							$id_licitacion = $data->id_licitacion; 
							$userID = $_SESSION['idUser'];
							$fecha = date("Y-m-d H:i:s");
							$fecha_inicio = $data->fecha_inicio;
							$fecha_fin = $data->fecha_fin;
							$contrato = ($data->contrato == '') ? "NULL" : "'".$data->contrato."'";
							$fecha_contrato = $data->fecha_contrato;
							$monto_adjudicado = ($data->monto_adjudicado == '') ? "NULL" : $data->monto_adjudicado;
							$rows = json_decode($data->rows,true);

							$id_evento = "";
							$id_cliente = "";
							$id_tipo_cuadro = "";
							$no_evento = "";
							$id_clasificacion_evento = "";


							$sql = "SELECT numero_evento, id_cliente, id_estado , id_evento FROM vta_licitacion WHERE id_licitacion = '$id_licitacion'";
							$res = $db->query($sql);
							$row = $db->fetch_assoc($res);
							$id_cliente = $row['id_cliente'];
							$id_evento = $row['id_evento'];
							$no_evento = $row['numero_evento'];

							$sql = "SELECT a.id_cliente, a.tipo_licitacion, b.id_clasificacion_evento, b.id_tipo_cuadro FROM vta_evento a 
							INNER JOIN cat_cuadro_clasificacion b ON  a.tipo_licitacion = b.id_clasificacion_evento 
							WHERE id_evento = '$id_evento'";
							$res = $db->query($sql);
							$row = $db->fetch_assoc($res);
							$id_tipo_cuadro = $row['id_tipo_cuadro'];
							$id_clasificacion_evento = $row['id_clasificacion_evento'];


							$sql_cat_cuadro = "INSERT INTO cat_cuadro (
							id_cliente,
							fecha_inicio,
							fecha_fin,
							estatus,
							id_usuario_captura,
							fecha_captura,
							id_tipo_cuadro,
							no_contrato,
							no_evento,
							monto_adjudicado,
							fecha_contrato,
							id_clasificacion_evento) 
							VALUES (
							'$id_cliente',
							'$fecha_inicio',
							'$fecha_fin',
							'1',
							'$userID',
							'$fecha',
							'$id_tipo_cuadro',
							$contrato,
							'$no_evento',
							$monto_adjudicado,
							'$fecha_contrato',
							'$id_clasificacion_evento') RETURNING id_cuadro";

							$res = $db->query($sql_cat_cuadro);
							$row = $db->fetch_assoc($res);

							$id_cuadro = $row['id_cuadro'];

							$db->query("INSERT INTO vta_licitacion_fallo (id_licitacion,id_usuario_captura,fecha_captura) VALUES ('$id_licitacion','$userID','$fecha')");


							$partidas = "";
							foreach ($rows as $key => $row) {
								$partidas .= "'".$rows[$key]['partida']."',"; 
							}

							$sql_cat_cuadro_producto = " INSERT INTO cat_cuadro_producto(
							id_cuadro,
							id_cliente,
							clave_cliente,
							descripcion,
							marca,
							precio,
							presentacion,
							fabricante,
							minimo,
							maximo	
							)
							(SELECT '$id_cuadro','$id_cliente', b.clave_cbn,b.descripcion,a.marca,a.costo,'b.presentacion',b.laboratorio, a.cantidad, a.cantidad_maxima
							FROM vta_licitacion_fallo_detalle a
							INNER JOIN vta_licitacion_detalle b on b.id_licitacion = a.id_licitacion and a.partida = b.partida and a.clave_cbn = b.clave_cbn
							WHERE a.id_licitacion= '466' AND a.id_oferente = 1)";	
							$db->query($sql_cat_cuadro_producto);

							//rows con Provedor adjudicado  sin  EMPRESA
							$partidas = rtrim($partidas,',');
							$sql_cat_proveedor_producto_evento = " INSERT INTO cat_proveedor_producto_evento(
							codigo_barras,
							id_proveedor,
							id_cuadro,
							nombre_comercial,
							descripcion,
							precio,
							id_usuario_captura,
							fecha_captura,
							clave,
							clave_cliente,
							id_laboratorio,
							id_proveedor_secundario
							)
							(SELECT 'codigo_barras', proveedor,'$id_cuadro', 'nombre_comercial', 'descripcion', costo, '$userID', '$fecha', 'clave_sistema',
							'clave_cbn', laboratorio, proveedor FROM vta_licitacion_detalle  where partida IN($partidas) AND  id_licitacion  = '$id_licitacion' AND empresa is NULL)";	
							$db->query($sql_cat_proveedor_producto_evento);

							

							//rows con Provedor adjudicado  CON  EMPRESA
							$sql_cat_proveedor_producto_evento2 = " INSERT INTO cat_proveedor_producto_evento(
							codigo_barras,
							id_proveedor,
							id_cuadro,
							nombre_comercial,
							descripcion,
							precio,
							id_usuario_captura,
							fecha_captura,
							clave,
							clave_cliente,
							id_laboratorio,
							id_proveedor_secundario
							)
							(SELECT 'a.codigo_barras', b.id_proveedor_relacionado ,'$id_cuadro', 'a.nombre_comercial', 'a.descripcion', 0, '$userID', '$fecha', 'a.clave_sistema',
							'a.clave_cbn', a.laboratorio, a.proveedor FROM vta_licitacion_detalle a  INNER JOIN sinc_proveedor_cliente b ON a.empresa = b.nombre_empresa WHERE a.partida IN($partidas) AND  a.id_licitacion  = '$id_licitacion' AND a.empresa IS NOT NULL)";	
							$db->query($sql_cat_proveedor_producto_evento2);

							
							










							$contrato = ($data->contrato == '') ? "NULL" : $data->contrato;
							//INSERTAN EN LAS BASES DE DATOS SECUNDARIAS
							$sql = "SELECT id_proveedor_relacionado ,id_cliente_relacionado, name_base_datos , nombre_empresa FROM  sinc_proveedor_cliente";
							$res = $db->query($sql);

							while ($row = $db->fetch_assoc($res)) {
								

								//CREA E INICIA LA CONEXION								
								$db->query("SELECT dblink_connect('".$row['name_base_datos']."', 'dbname=".$row['name_base_datos']." user=postgres password=Test')");
								$db->query("SELECT dblink('".$row['name_base_datos']."','BEGIN;')");
								

								$res_ext = $db->query("SELECT id_cuadro FROM dblink('".$row['name_base_datos']."',
									'INSERT INTO cat_cuadro(
									id_cliente,
									fecha_inicio,
									fecha_fin,
									estatus,
									id_usuario_captura,
									fecha_captura,
									id_tipo_cuadro,
									no_contrato,
									no_evento,
									monto_adjudicado,
									fecha_contrato,
									id_clasificacion_evento) 
									VALUES (
									''".$row['id_cliente_relacionado']."'',
									''$fecha_inicio'',
									''$fecha_fin'',
									''1'',
									''$userID'',
									''$fecha'',
									''$id_tipo_cuadro'',
									''$contrato'',
									''$no_evento'',
									$monto_adjudicado,
									''$fecha_contrato'',
									''$id_clasificacion_evento'') RETURNING id_cuadro 
									') as t (id_cuadro INTEGER)");//END QUERY AND DBLINK 
								$row_ext = $db->fetch_assoc($res_ext);

								$db->query("
									INSERT INTO sinc_cuadro_relacionado(
									id_proveedor_relacionado,
									id_cuadro,
									id_cuadro_relacionado)
									VALUES (
									".$row['id_proveedor_relacionado'].",
									".$id_cuadro.",
									".$row_ext['id_cuadro']."
									)");




								// SE BUSCAN LOS PRODUCTOS DE LA EMPRESA Test O QUIROPRACTICO O 
								$sql2 = "SELECT  id_licitacion, partida, clave_cbn, descripcion, cantidad_minima, costo, nombre_comercial,
								presentacion, cantidad_maxima, proveedor, clave_sistema, precio, laboratorio, codigo_barras, empresa
								FROM vta_licitacion_detalle  where  id_licitacion  = '$id_licitacion' AND empresa = '".$row['nombre_empresa']."'"; 
								$res2 = $db->query($sql2);
								while ($row2 = $db->fetch_assoc($res2)){
									
									$db->query("SELECT dblink('".$row['name_base_datos']."',
										'INSERT INTO cat_proveedor_producto_evento(
										codigo_barras,
										id_proveedor,
										id_cuadro,
										nombre_comercial,
										descripcion,
										precio,
										id_usuario_captura,
										fecha_captura,
										clave,
										clave_cliente,
										id_laboratorio,
										id_proveedor_secundario) 
										VALUES (
										''".$row2['codigo_barras']."'',
										''".$row2['proveedor']."'',
										''".$row_ext['id_cuadro']."'',
										''".$row2['nombre_comercial']."'',
										''".$row2['descripcion']."'',
										''".$row2['precio']."'',
										''".$userID."'',
										''".$fecha."'',
										''".$row2['clave_sistema']."'',
										''".$row2['clave_cbn']."'',
										''".$row2['laboratorio']."'',
										''".$row2['proveedor']."''
										)
									')");
								}

								//EJECUTA Y CIERRA EL LINK
								$db->query("SELECT dblink('".$row['name_base_datos']."','COMMIT;')");
								$db->query("SELECT dblink_disconnect('".$row['name_base_datos']."')");

							}

							$db->query('COMMIT');
							echo 'Fallo Finalizado';
							break;
   }// end switch
   ?>             


