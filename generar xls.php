<?php 
//CREADO POR HANG TU WONG LEY FRANCO
//28/09/2016
include '../../../librerias/excel2007/PHPExcel.php';
include '../../../librerias/excel2007/PHPExcel/Writer/Excel2007.php';
include ("../../../sesion/validaAjax.php");
$db = new Db("postgres","");
$localidad=$_GET['localidad'];
$proveedor = $_GET['proveedor'];
$fecha_inicio=$_GET['fecha_inicio'];
$fecha_fin=$_GET['fecha_fin'];

$usuarioSQL = "SELECT (nombre ||' '||paterno ||' '||materno) usuario FROM adm_usuario WHERE id_usuario =".$_SESSION['idUser'];
$res = $db->query($usuarioSQL);
$row = $db->fetch_assoc($res);
$user = $row['usuario'];

$filterOne = '';
if ($localidad != -999){
	$filterOne = "AND c.id_localidad IN (".$localidad.")";
}

$filterTwo = '';
if ($proveedor != -999){
	$filterTwo = "AND c.id_proveedor IN (".$proveedor.")";
}

$consulta ="SELECT c.id_compra, l.localidad, pp.nombre, c.fecha_captura, u.nombre||' '||u.paterno||' '||u.materno AS comprador, 
CASE WHEN cl.alias<>'' THEN cl.razon_social||' - '||cl.alias ELSE cl.razon_social END razon_social,
d.clave, p.descripcion||' '||p.presentacion AS descripcion_producto, cd.cantidad_pedida, cd.precio,
COALESCE(d.inventario,0) AS existencias_localidad,
COALESCE(d.oc_abiertas,0) AS oc_pendientes, 
COALESCE(d.pedidos_pendientes,0) AS  pedidos_pendientes,
COALESCE(d.cpm_cliente,0) AS cpm_cliente,
COALESCE(d.oc_pendientes_autorizacion,0) AS oc_pendientes_autorizacion,
COALESCE(d.precio_facturacion,0) AS precio_facturacion, 
COALESCE(d.perdida,0) AS perdida,  
COALESCE(d.mejor_precio,0) AS mejor_precio, ppm.nombre AS mejor_proveedor,
c.fecha_autorizacion_planeacion,
c.fecha_autorizacion_compras,
c.fecha_autorizacion_gerencia,
(up.nombre||' '||up.paterno||' '||up.materno) as nom_planeador,
(ug.nombre||' '||ug.paterno||' '||ug.materno) as nom_gerente,
(ul.nombre||' '||ul.paterno||' '||ul.materno) as nom_comercializacion
FROM comp_compra_autorizacion_detalle d
INNER JOIN comp_compra c ON c.id_compra=d.id_compra
INNER JOIN cat_localidad l ON l.id_localidad=c.id_localidad $filterOne
INNER JOIN cat_proveedor pp ON c.id_proveedor=pp.id_proveedor $filterTwo
INNER JOIN adm_usuario u ON u.id_usuario=c.id_usuario_captura
INNER JOIN catalogo_producto p ON d.clave=p.clave
LEFT JOIN cat_cliente cl ON cl.id_cliente=c.id_cliente
LEFT JOIN adm_usuario up ON up.id_usuario=c.id_usuario_autorizacion_planeacion 
LEFT JOIN adm_usuario ul ON ul.id_usuario=c.id_usuario_autorizacion_compras
LEFT JOIN adm_usuario ug ON ug.id_usuario=c.id_usuario_autorizacion_gerencia
LEFT JOIN cat_proveedor ppm ON ppm.id_proveedor=d.id_mejor_proveedor 
LEFT JOIN comp_compra_detalle cd ON cd.id_compra=d.id_compra AND cd.clave=d.clave AND cd.codigo_barras=d.codigo_barras AND cd.precio!=0
WHERE c.fecha_captura::DATE BETWEEN '$fecha_inicio' AND '$fecha_fin'
--AND d.fecha_autorizacion=c.fecha_autorizacion_planeacion
ORDER BY c.id_compra--, d.fecha_autorizacion;
";

//INFORMACION DEL ARCHIVO EXCEL
$nombre_archivo = 'Reporte Autorizaciones.xlsx';
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Hoja 1');

# Formato a cabeceras 
$styleTitulo = array('font' => array('bold' => true,),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),);
$objPHPExcel->getActiveSheet()->getStyle('A4:AZ4')->applyFromArray($styleTitulo);

/////////////////////////////////////////////////////////////////////////////////////////
$renglonTitulos = 4; //NUMERO DE RENGLON EN EL QUE SE PINTAN LOS TITULOS
$fila = 5; //RENGLON DONDE EMPIEZAN A PINTARSE LOS RESULTADOS

$rst = $db->query($consulta);

if($db->num_rows($rst) == 0){ //SI NO HAY RESULTADOS SE PINTA LA LEYENDA
	$objPHPExcel->getActiveSheet()->SetCellValue('A1','NO SE ENCONTRARON RESULTADOS PARA ESTA HOJA DEL REPORTE');
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
}else{
	$objPHPExcel->getActiveSheet()->SetCellValue('A1','GENERADO POR: '.$user);
	$objPHPExcel->getActiveSheet()->SetCellValue('A2','FECHA: '.date("d/m/Y"));
	$objPHPExcel->getActiveSheet()->SetCellValue('A3','RUTA: REPORTES/COMPRAS/REPORTE AUTORIZACIONES');

$datos = array(); // SE HACE LA CONSULTA Y SE OBTIENE TODOS LOS DATOS
while ($row = $db->fetch_assoc($rst)) {
	$datos []  = $row;
}

$abecedario = range('A', 'Z'); // ABECEDARIO DE LA A TO LA Z
$size = sizeof($abecedario);

for($i = 0; $i < $size; $i++){ // ABECEDARIO DE LA A TO LA AZ
	$abecedario[] = 'A'.$abecedario[$i];
}

 //SE OBTIENE LOS TITULOS DEL ARREGLO  Y  SE PINTAN
$titulos = array();
foreach ($datos[0] as $key => $value) {
	$titulos [] = $key;
}

for($i = 0; $i < sizeof($titulos); $i++){
	$titulos[$i] = strtoupper(str_replace("_"," ", $titulos[$i]));

	$objPHPExcel->getActiveSheet()->SetCellValue($abecedario[$i].$renglonTitulos, ucwords($titulos[$i]));
	$objPHPExcel->getActiveSheet()->getColumnDimension($abecedario[$i])->setAutoSize(true);
}
///////////////////////////////////////////////////////////////
//SE PINTAN LOS RESULTADOS
$contadorAlpha = 0;
  for($i = 0; $i < sizeof($datos); $i++){ // SE RECORREN TODOS LOS DATOS
  	foreach ($datos[$i] as $key => $value) {
  		$objPHPExcel->getActiveSheet()->SetCellValue($abecedario[$contadorAlpha].$fila, $value);
  		$contadorAlpha++;
  	}
  	$contadorAlpha = 0;
  	$fila++;
  } 
}//end else

/////////////////////////////////HOJA NUMERO 2////////////////////////////////////
$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setTitle("Hoja 2");
$styleTitulo = array('font' => array('bold' => true,),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),);
$objPHPExcel->getActiveSheet()->getStyle('A4:AZ4')->applyFromArray($styleTitulo);

$filterOne = '';
if ($localidad != -999){
	$filterOne = "AND cc.id_localidad IN (".$localidad.")";
}

$filterTwo = '';
if ($proveedor != -999){
	$filterTwo = "AND cc.id_proveedor IN (".$proveedor.")";
}

$consulta="SELECT c.id_compra, l.localidad, pp.nombre, c.fecha_captura AS fecha_orden_compra, u.nombre||' '||u.paterno||' '||u.materno AS comprador, 
CASE WHEN cl.alias<>'' THEN cl.razon_social||' - '||cl.alias ELSE cl.razon_social END razon_social, c.observacion_rechazo,
d.clave, p.descripcion||' '||p.presentacion AS descripcion_producto, cd.cantidad_pedida, d.precio, d.observacion AS observacion_clave, 
COALESCE(d.inventario,0) AS existencias_localidad,
COALESCE(d.oc_abiertas,0) AS oc_pendientes, 
COALESCE(d.pedidos_pendientes,0) AS  pedidos_pendientes,
COALESCE(d.cpm_cliente,0) AS cpm_cliente,
COALESCE(d.oc_pendientes_autorizacion,0) AS oc_pendientes_autorizacion,
COALESCE(d.precio_facturacion,0) AS precio_facturacion, 
COALESCE(d.perdida,0) AS perdida,  
COALESCE(d.mejor_precio,0) AS mejor_precio, ppm.nombre AS mejor_proveedor
FROM comp_compra_rechazada c
INNER JOIN comp_compra cc ON c.id_compra=cc.id_compra
INNER JOIN cat_localidad l ON l.id_localidad=cc.id_localidad $filterOne
INNER JOIN adm_usuario u ON u.id_usuario=c.id_usuario_rechazo 
INNER JOIN cat_proveedor pp ON cc.id_proveedor=pp.id_proveedor $filterTwo
INNER JOIN comp_compra_rechazada_detalle d ON c.id_compra=d.id_compra
INNER JOIN catalogo_producto p ON d.clave=p.clave
LEFT JOIN comp_compra_detalle cd ON cd.id_compra=cc.id_compra AND cd.clave=d.clave AND cd.codigo_barras=d.codigo_barras AND cd.precio=d.precio      
LEFT JOIN cat_proveedor ppm ON ppm.id_proveedor=d.id_mejor_proveedor
LEFT JOIN cat_cliente cl ON cl.id_cliente=cc.id_cliente
WHERE c.fecha_captura::DATE BETWEEN '$fecha_inicio' AND '$fecha_fin'
";

$rst = $db->query($consulta);

$renglonTitulos = 4; //NUMERO DE RENGLON EN EL QUE SE PINTAN LOS TITULOS
$fila = 5; //RENGLON DONDE EMPIEZAN A PINTARSE LOS RESULTADOS

if($db->num_rows($rst) == 0){ //SI NO HAY RESULTADOS SE PINTA LA LEYENDA
	$objPHPExcel->getActiveSheet()->SetCellValue('A1','NO SE ENCONTRARON RESULTADOS PARA ESTA HOJA DEL REPORTE');
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
}else{

	$objPHPExcel->getActiveSheet()->SetCellValue('A1','GENERADO POR: '.$user);
	$objPHPExcel->getActiveSheet()->SetCellValue('A2','FECHA: '.date("d/m/Y"));
	$objPHPExcel->getActiveSheet()->SetCellValue('A3','RUTA: REPORTES/COMPRAS/REPORTE AUTORIZACIONES');

$datos = array(); // SE HACE LA CONSULTA Y SE OBTIENE TODOS LOS DATOS
while ($row = $db->fetch_assoc($rst)) {
	$datos []  = $row;
}

$abecedario = range('A', 'Z'); // ABECEDARIO DE LA A TO LA Z
$size = sizeof($abecedario);

for($i = 0; $i < $size; $i++){ // ABECEDARIO DE LA A TO LA AZ
	$abecedario[] = 'A'.$abecedario[$i];
}

 //SE OBTIENE LOS TITULOS DEL ARREGLO  Y  SE PINTAN
$titulos = array();
foreach ($datos[0] as $key => $value) {
	$titulos [] = $key;
}

for($i = 0; $i < sizeof($titulos); $i++){
	$titulos[$i] = strtoupper(str_replace("_"," ", $titulos[$i]));
	$objPHPExcel->getActiveSheet()->SetCellValue($abecedario[$i].$renglonTitulos, ucwords($titulos[$i]));
	$objPHPExcel->getActiveSheet()->getColumnDimension($abecedario[$i])->setAutoSize(true);
}

//SE PINTAN LOS RESULTADOS
$contadorAlpha = 0;
for($i = 0; $i < sizeof($datos); $i++){ // SE RECORREN TODOS LOS DATOS
	foreach ($datos[$i] as $key => $value) {
		$objPHPExcel->getActiveSheet()->SetCellValue($abecedario[$contadorAlpha].$fila, $value);
		$contadorAlpha++;
	}
	$contadorAlpha = 0;
	$fila++;
 }
}//end else

$objPHPExcel->setActiveSheetIndex(0);
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");	
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$nombre_archivo.'"');
header('Cache-Control: max-age=0');
ob_end_clean();
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  
$objWriter->save('php://output');
?>