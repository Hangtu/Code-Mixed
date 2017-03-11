<?php
include ("../../../sesion/validaAjax.php");
include ("../../../class/class.json.php");
include ("../../../class/class.valida.php");
$db   	   = new db ('postgres' , 'Test');
$json 	   = new json();


if(isset($_POST['accion'])){
	switch ($_POST['accion']){


		case 'subir_archivo':
		$jPost = $json->getPostToJson();
		if(count($_FILES) >= 0){
			$upload_folder  = "/home/Test/public_html/ventas/licitaciones/eventos";
			$tipo_archivo="";
			foreach($_FILES AS $file){
				$nombre_archivo = stripAccents(utf8_decode($file['name']));
				$tipo_archivo   = $file['type'];
				$tamano_archivo = $file['size'];
				$tmp_archivo    = $file['tmp_name'];
				$error_archivo  = $file['error'];
				
				$extension 		= strtolower(substr($nombre_archivo , strlen($nombre_archivo)-3 , strlen($nombre_archivo)));
				
				$json->setData(
					array(
						"fileName" => $nombre_archivo,
						"tmpName" => $tmp_archivo,
						"extension" => $extension
						)
					);

				if($json->getSuccess()){
					if($tamano_archivo > 20248729)
						$json->setMensajeError("El archivo sobrepasa el tamaño permitido para importar");
					if($error_archivo != ""){
						$msn_error="";
						switch ($error_archivo){
							case 1: case 2: 
							$msn_error="El archivo sobrepasa el tamaño permitido para importar"; 
							break;
							case 3: case 4: case 7: 
							$msn_error="Error al cargar el archivo intentelo de nuevo"; 
							break;
							default: 
							$msn_error="Error al cargar el archivo se encuentra dañado"; 
							break;
						}
						$json->setMensajeError($msn_error);
					}
					else{
						$ArchivoTipo = "";
						switch ($extension){
							case "xls": 
							$ArchivoTipo = "application/vnd.ms-excel";
							break;
							case "lsx":
							$ArchivoTipo = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
							break;
							case "pdf":
							$ArchivoTipo = "application/pdf";
							break;
							case "ocx": 
							$ArchivoTipo = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
							break;
							case "doc": 
							$ArchivoTipo="application/msword"; 
							break;

							case "jpg": 
							$ArchivoTipo="image/jpeg"; 
							break;

							case "png": 
							$ArchivoTipo="image/png"; 
							break;

						}
						
						if($ArchivoTipo != $tipo_archivo){
							$json->setMensajeError("El tipo de archivo a subir debe ser Excel, Word, PDF, JPG O PNG");
							// $json->setMensajeError($extension);
						}else{
							if(!move_uploaded_file($tmp_archivo , $upload_folder.$tmp_archivo))
								$json->setMensajeError("Ocurrio un error al subir el archivo. No pudo guardarse.");
						}
					}
				}
			}
			$json->setVar(array("tipo_archivo" => $jPost->tipo_archivo));
			$json->setVar(array("formato_archivo" => $tipo_archivo));
			
			
			
		}
		else
			$json->mensaje .= "\n No se ha importado un archivo";
		
		$json->setVar(array("archivos_duplicados" => $jPost->archivos_duplicados));
		$json->jsonEncode();
		break;




















		case "guardar":
		$error=false;
		$sostenimiento_oferta='null';

		#Validar si el numero de evento ya existe
		$sql_no_evento = "SELECT * FROM vta_evento WHERE no_evento = UPPER('".$_POST['no_evento']."')";
		$rst_no_evento = $db->query($sql_no_evento);
		if($db->num_rows($rst_no_evento)>0){
			$json->setMensajeError("El número de Evento '".strtoupper($_POST['no_evento'])."' ya se encuentra registrado");
			$json->jsonEncode();
			exit();
		}

		if($_POST['sostenimiento_oferta']!=-1)
			$sostenimiento_oferta=$_POST['sostenimiento_oferta'];
		$precio_requerido ='null';
		if($_POST['precio_requerido']!='-1' && $_POST['precio_requerido']!='')
			$precio_requerido="'".$_POST['precio_requerido']."'";
		$fecha_muestras='null';
		if(trim($_POST['fecha_muestras'])!='')
			$fecha_muestras="'".$_POST['fecha_muestras']."'";
		$hora_muestras ='null';
		if(trim($_POST['hora_muestras'])!=':')
			$hora_muestras="'".$_POST['hora_muestras']."'";
		

		$fecha_fallo ='null';
		if(trim($_POST['fecha_fallo'])!='')
			$fecha_fallo="'".$_POST['fecha_fallo']."'";
		$hora_fallo ='null';
		if(trim($_POST['hora_fallo'])!=':')
			$hora_fallo="'".$_POST['hora_fallo']."'";

		$fecha_preguntas ='null';
		if(trim($_POST['fecha_preguntas'])!='')
			$fecha_preguntas="'".$_POST['fecha_preguntas']."'";
		$hora_preguntas ='null';
		if(trim($_POST['hora_preguntas'])!=':')
			$hora_preguntas="'".$_POST['hora_preguntas']."'";


		$fecha_visita ='null';
		if(trim($_POST['fecha_visita'])!='')
			$fecha_visita="'".$_POST['fecha_visita']."'";
		$hora_visita ='null';
		if(trim($_POST['hora_visita'])!=':')
			$hora_visita="'".$_POST['hora_visita']."'";

		$fecha_limite_pago = 'null';
		if(trim($_POST['fecha_limite_pago'])!='')
			$fecha_limite_pago="'".$_POST['fecha_limite_pago']."'";

		$fecha_publicacion = 'null';
		if(trim($_POST['fecha_publicacion'])!='')
			$fecha_publicacion="'".$_POST['fecha_publicacion']."'";
		$hora_publicacion ='null';
		if(trim($_POST['hora_publicacion'])!=':')
			$hora_publicacion="'".$_POST['hora_publicacion']."'";

		$fecha_aclaraciones = 'null';
		if(trim($_POST['fecha_aclaraciones'])!='')
			$fecha_aclaraciones="'".$_POST['fecha_aclaraciones']."'";
		$hora_aclaraciones ='null';
		if(trim($_POST['hora_aclaraciones'])!=':')
			$hora_aclaraciones="'".$_POST['hora_aclaraciones']."'";

		$nacionalidad_producto = 'null';
		if($_POST['nacionalidad_producto']!=-1 && $_POST['nacionalidad_producto']!='')
			$nacionalidad_producto ="'".$_POST['nacionalidad_producto']."'";

		$presentacion_producto = 'null';
		if($_POST['presentacion_producto']!=-1 && $_POST['presentacion_producto']!='')
			$presentacion_producto ="'".$_POST['presentacion_producto']."'";
			/*$id_cliente='null';
			if($_POST['id_cliente']!=0)
			$id_cliente=$_POST['id_cliente'];*/
			
			$porcentaje_penalizacion = ($_POST['porcentaje_penalizacion']=='')?0:$_POST['porcentaje_penalizacion'];
			$porcentaje_muestras = ($_POST['porcentaje_muestras']=='')?0:$_POST['porcentaje_muestras'];
			$porcentaje_registros = ($_POST['porcentaje_registros']=='')?0:$_POST['porcentaje_registros'];
			$porcentaje_apoyo = ($_POST['porcentaje_apoyo']=='')?0:$_POST['porcentaje_apoyo'];
			$no_entrega = ($_POST['no_entrega']=='')?0:$_POST['no_entrega'];
			$techo_presupuestal = ($_POST['techo_presupuestal']=='')?0:str_replace(",","",$_POST['techo_presupuestal']);
			$costo_base = ($_POST['costo_base']=='')?0:str_replace(",","",$_POST['costo_base']);
			
			$db->query("START TRANSACTION");
			$sql_evento="INSERT INTO vta_evento(no_evento, id_cliente, nuevo_cliente, id_estado, costo_base, 
			datos_banco, tipo_licitacion, tipo_producto, no_entrega, criterio_entrega, 
			modalidad, empresa_adicional, criterios_penalizacion, sostenimiento_oferta, 
			porcentaje_penalizacion, nacionalidad_producto, techo_presupuestal, precio_requerido, 
			requisitos_tecnicos, requisitos_contables, carta_apoyo, porcentaje_apoyo,
			formato_carta_apoyo, registro_sanitarios, porcentaje_registros, certificados_practicas, 
			muestras, otros_documentos, descripcion_otros_documentos, 
			motivo_rechazo, fecha_publicacion, hora_publicacion, fecha_aclaraciones, hora_aclaraciones, fecha_muestras, lugar_muestras, lugar_aclaraciones, fecha_apertura, 
			hora_apertura, lugar_apertura, fecha_fallo, hora_fallo, lugar_fallo, hora_muestras, fecha_limite_pago, id_usuario_captura, fecha_captura, expediente,
			fecha_visita, hora_visita, lugar_visita, presentacion_producto, porcentaje_muestras,
			fecha_preguntas, hora_preguntas, lugar_preguntas, caducidad)
			VALUES (UPPER('".utf8_decode(str_replace("'","''",$_POST['no_evento']))."'), ".$_POST['id_cliente'].", UPPER('".utf8_decode(str_replace("'","''",$_POST['nuevo_cliente']))."'), ".$_POST['id_estado'].", ".str_replace(",","",$costo_base).", 
			UPPER('".utf8_decode(str_replace("'","''",$_POST['datos_banco']))."'), ".$_POST['tipo_licitacion'].", UPPER('".utf8_decode(str_replace("'","''",$_POST['tipo_producto']))."'), ".$no_entrega.", UPPER('".utf8_decode(str_replace("'","''",$_POST['criterio_entrega']))."'), ".$_POST['modalidad'].", UPPER('".utf8_decode(str_replace("'","''",$_POST['empresa_adicional']))."'), UPPER('".utf8_decode(str_replace("'","''",$_POST['criterios_penalizacion']))."'), ".$sostenimiento_oferta.", ".$porcentaje_penalizacion.", ".$nacionalidad_producto.", ".$techo_presupuestal.", ".$precio_requerido.", UPPER('".utf8_decode(str_replace("'","''",$_POST['requisitos_tecnicos']))."'), UPPER('".utf8_decode(str_replace("'","''",$_POST['requisitos_contables']))."'), ".$_POST['carta_apoyo'].", ".$porcentaje_apoyo.", ".$_POST['formato_carta_apoyo'].", ".$_POST['registro_sanitarios'].", ".$porcentaje_registros.", ".$_POST['certificados_practicas'].", 
			".$_POST['muestras'].", ".$_POST['otros_documentos'].", UPPER('".utf8_decode(str_replace("'","''",$_POST['descripcion_otros_documentos']))."'), UPPER('".utf8_decode(str_replace("'","''",$_POST['motivo_rechazo']))."'),
			".$fecha_publicacion.", ".$hora_publicacion.", ".$fecha_aclaraciones.", ".$hora_aclaraciones.", ".$fecha_muestras.", UPPER('".utf8_decode(str_replace("'","''",$_POST['lugar_muestras']))."'), UPPER('".utf8_decode(str_replace("'","''",$_POST['lugar_aclaraciones']))."'), 
			'".$_POST['fecha_apertura']."', '".$_POST['hora_apertura']."', UPPER('".utf8_decode(str_replace("'","''",$_POST['lugar_apertura']))."'), ".$fecha_fallo.", ".$hora_fallo.", UPPER('".utf8_decode(str_replace("'","''",$_POST['lugar_fallo']))."'), 
			".$hora_muestras.", ".$fecha_limite_pago.", ".$_SESSION['idUser'].", '".date("Y-m-d H:i:s")."', UPPER('".utf8_decode(str_replace("'","''",$_POST['expediente']))."'), 
			".$fecha_visita.", ".$hora_visita.", UPPER('".utf8_decode(str_replace("'","''",$_POST['lugar_visita']))."'), ".$presentacion_producto.", ".$porcentaje_muestras.",
			".$fecha_preguntas.", ".$hora_preguntas.", UPPER('".utf8_decode(str_replace("'","''",$_POST['lugar_preguntas']))."'),UPPER('".utf8_decode(str_replace("'","''",$_POST['caducidad']))."')) RETURNING id_evento";
			$rst_evento = $db->query($sql_evento);
			$row_evento = $db->fetch_assoc($rst_evento);

			  #Insertar Archivos del evento
			$archivos_actas_aclaraciones = "";
			if($_POST['actas_aclaraciones']!=''){
				$array_actas_aclaraciones=explode(",",$_POST['actas_aclaraciones']);

				$coma = "";
				foreach($array_actas_aclaraciones AS $acta_aclaraciones){
					list($a_tmp,$a_name)=explode("@@",$acta_aclaraciones);
					$archivos_actas_aclaraciones.=$coma.$a_name;
					$coma = ",";
				}
			}

			$archivos_actas_apertura = "";	
			if($_POST['actas_apertura']!=''){
				$array_actas_apertura=explode(",",$_POST['actas_apertura']);

				$coma = "";
				foreach($array_actas_apertura AS $acta_apertura){
					list($a_tmp,$a_name)=explode("@@",$acta_apertura);
					$archivos_actas_apertura.=$coma.$a_name;
					$coma = ",";
				}
			}

			$array_actas_fallo=explode(",",$_POST['actas_fallo']);
			$archivos_actas_fallo = "";
			$coma = "";

			if($_POST['actas_fallo']!=''){
				foreach($array_actas_fallo AS $acta_fallo){
					list($a_tmp,$a_name)=explode("@@",$acta_fallo);
					$archivos_actas_fallo.=$coma.$a_name;
					$coma = ",";
				}
			}


			//CUANDO SOLO ERA UN ARCHIVO
			// $archivo_bases=explode("@@",$_POST['archivo_bases']);
			//archivo_bases[1]: in sql_archivos
			
			$array_archivo_bases=explode(",",$_POST['archivo_bases']);
			$archivos_archivo_bases = "";
			$coma = "";
			if($_POST['archivo_bases']!=''){
				foreach($array_archivo_bases AS $acta_fallo){
					list($a_tmp,$a_name)=explode("@@",$acta_fallo);
					$archivos_archivo_bases.=$coma.$a_name;
					$coma = ",";
				}
			}



			$array_peticion_documental=explode(",",$_POST['peticion_documental']);
			$archivos_peticion_documental = "";
			$coma = "";
			if($_POST['peticion_documental']!=''){
				foreach($array_peticion_documental AS $acta_fallo){
					list($a_tmp,$a_name)=explode("@@",$acta_fallo);
					$archivos_peticion_documental.=$coma.$a_name;
					$coma = ",";
				}
			}



			$archivo_acuerdo_licitado="";
			if($_POST['acuerdo_licitado']!=''){
				$acuerdo_licitado=explode("@@",$_POST['acuerdo_licitado']);
				$archivo_acuerdo_licitado=$acuerdo_licitado[1];
			}

			$archivo_tabla_productos="";
			if($_POST['tabla_productos']!=''){
				$tabla_productos=explode("@@",$_POST['tabla_productos']);
				$archivo_tabla_productos = $tabla_productos[1];
			}
			$archivo_productos_adjudicados="";
			if($_POST['productos_adjudicados']!=''){
				$productos_adjudicados=explode("@@",$_POST['productos_adjudicados']);
				$archivo_productos_adjudicados = $productos_adjudicados[1];
			}

			$sql_archivos="INSERT INTO vta_evento_archivos(id_evento, bases_licitacion, actas_aclaracion, actas_apertura, 
			actas_fallo, formato_carta_apoyo, tabla_productos, productos_adjudicados,peticion_documental)
			VALUES (".$row_evento['id_evento'].", '".utf8_decode(str_replace("'","''",$archivos_archivo_bases))."', '".utf8_decode(str_replace("'","''",$archivos_actas_aclaraciones))."', 
			'".utf8_decode(str_replace("'","''",$archivos_actas_apertura))."', '".utf8_decode(str_replace("'","''",$archivos_actas_fallo))."', '".utf8_decode(str_replace("'","''",$archivo_acuerdo_licitado))."', '".utf8_decode(str_replace("'","''",$archivo_tabla_productos))."', '".utf8_decode(str_replace("'","''",$archivo_productos_adjudicados))."', 
			'".utf8_decode(str_replace("'","''",$archivos_peticion_documental))."');";
			$db->query($sql_archivos);

			$lista_archivos="";
			$coma="";
			

			if($_POST['actas_aclaraciones']!=''){
				$lista_archivos.=$coma.$_POST['actas_aclaraciones'];
				$coma=",";
			}

			if($_POST['actas_apertura']!=''){
				$lista_archivos.=$coma.$_POST['actas_apertura'];
				$coma=",";
			}

			if($_POST['actas_fallo']!=''){
				$lista_archivos.=$coma.$_POST['actas_fallo'];
				$coma=",";
			}

			if($_POST['archivo_bases']!=''){
				$lista_archivos.=$coma.$_POST['archivo_bases'];
				$coma=",";
			}

			if($_POST['peticion_documental']!=''){
				$lista_archivos.=$coma.$_POST['peticion_documental'];
				$coma=",";
			}


			if($_POST['acuerdo_licitado']!=''){
				$lista_archivos.=$coma.$_POST['acuerdo_licitado'];
				$coma=",";
			}

			if($_POST['tabla_productos']!=''){
				$lista_archivos.=$coma.$_POST['tabla_productos'];
				$coma=",";
			}

			if($_POST['productos_adjudicados']!=''){
				$lista_archivos.=$coma.$_POST['productos_adjudicados'];
			}

			$json->setVar(array("id_evento" => $row_evento['id_evento']));
			if(trim($_POST['expediente'])=='')
				$json->setVar(array("no_evento" => utf8_decode(str_replace("'","''",$_POST['no_evento']))));
			else
				$json->setVar(array("no_evento" => utf8_decode(str_replace("'","''",$_POST['expediente']))));
			$json->setVar(array("archivos" => $lista_archivos));
			$json->setVar(array("archivos_eliminar" => $_POST['archivos_eliminados']));

			$error = $db->query("COMMIT");
			$json->jsonEncode();
			break;













			case 'mover_archivos':
			$id_evento = $_POST['id_evento'];
			$no_evento = $_POST['no_evento'];
			if(isset($_POST['no_evento_ant']))
				$no_evento_ant = $_POST['no_evento_ant'];
			else
				$no_evento_ant ="";
			$array_archivos = $_POST['archivos'];
			$array_archivos_eliminar = explode(",",$_POST['archivos_eliminar']);
			$folder_actuality = "/home/Test/public_html/ventas/licitaciones/eventos";
			$upload_folder  = "/Test/".$no_evento.".".$id_evento;
			
			$connection = ssh2_connect('192.168.0.11', 22);
			ssh2_auth_password($connection, 'admin', 'tHL3Vm_o');
			$sftp       = ssh2_sftp($connection);
			
			if($no_evento_ant!=$no_evento){
				ssh2_sftp_rename($sftp, '/Test/'.$no_evento_ant.'.'.$id_evento, $upload_folder);
			}
			
			
			if($_POST['archivos_eliminar']!=''){
				foreach($array_archivos_eliminar AS $archivo_eliminar){
					if(strpos($archivo_eliminar, '@@') !== false){
						list($name_tmp_eliminar, $name_eliminar) = explode("@@", $archivo_eliminar);
						ssh2_sftp_unlink($sftp, $upload_folder.$name_tmp_eliminar);
					}
					else
						ssh2_sftp_unlink($sftp, $upload_folder.'/'.$archivo_eliminar);
				}
			}

			if($array_archivos!=''){
				#Crear carpeta con evento.
				if(!is_dir($upload_folder)){
					ssh2_sftp_mkdir($sftp, $upload_folder,0777);
				}
				
				foreach(explode(",",$array_archivos) AS $archivo){
					list($name_tmp, $name) = explode("@@",$archivo);
					$tmp_archivo = $folder_actuality.$name_tmp;
					
					if(!ssh2_scp_send($connection, $tmp_archivo, $upload_folder.'/'.$name, 0777))
						$json->setMensajeError("Error al cargar el archivo ".$name);
					
					unlink($tmp_archivo);
				}
			}
			$json->jsonEncode();
			break;




















			case 'buscar_evento':
			$txt_evento = $_POST['txt_evento'];
			$sql_evento = "SELECT *
			FROM vta_evento
			WHERE no_evento=UPPER('".$txt_evento."')";
			$rst_evento = $db->query($sql_evento);
			if($db->num_rows($rst_evento)>0){
				$row_evento = $db->fetch_assoc($rst_evento);
				$json->setData(   array('id_evento'=>utf8_encode($row_evento['id_evento']),
					'no_evento'=>utf8_encode($row_evento['no_evento']),
					'id_cliente'=>utf8_encode($row_evento['id_cliente']),
					'nuevo_cliente'=>utf8_encode($row_evento['nuevo_cliente']),
					'id_estado'=>utf8_encode($row_evento['id_estado']),
					'costo_base'=>utf8_encode($row_evento['costo_base']),
					'datos_banco'=>utf8_encode($row_evento['datos_banco']),
					'tipo_licitacion'=>utf8_encode($row_evento['tipo_licitacion']),
					'tipo_producto'=>utf8_encode($row_evento['tipo_producto']),
					'no_entrega'=>utf8_encode($row_evento['no_entrega']),
					'criterio_entrega'=>utf8_encode($row_evento['criterio_entrega']),
					'modalidad'=>utf8_encode($row_evento['modalidad']),
					'empresa_adicional'=>utf8_encode($row_evento['empresa_adicional']),
					'criterios_penalizacion'=>utf8_encode($row_evento['criterios_penalizacion']),
					'sostenimiento_oferta'=>utf8_encode($row_evento['sostenimiento_oferta']),
					'porcentaje_penalizacion'=>utf8_encode($row_evento['porcentaje_penalizacion']),
					'nacionalidad_producto'=>utf8_encode($row_evento['nacionalidad_producto']),
					'techo_presupuestal'=>utf8_encode($row_evento['techo_presupuestal']),
					'precio_requerido'=>utf8_encode($row_evento['precio_requerido']),
					'requisitos_tecnicos'=>utf8_encode($row_evento['requisitos_tecnicos']),
					'requisitos_contables'=>utf8_encode($row_evento['requisitos_contables']),
					'carta_apoyo'=>utf8_encode($row_evento['carta_apoyo']),
					'porcentaje_apoyo'=>utf8_encode($row_evento['porcentaje_apoyo']),
					'formato_carta_apoyo'=>utf8_encode($row_evento['formato_carta_apoyo']),
					'registro_sanitarios'=>utf8_encode($row_evento['registro_sanitarios']),
					'porcentaje_registros'=>utf8_encode($row_evento['porcentaje_registros']),
					'certificados_practicas'=>utf8_encode($row_evento['certificados_practicas']),
					'muestras'=>utf8_encode($row_evento['muestras']),
					'otros_documentos'=>utf8_encode($row_evento['otros_documentos']),
					'descripcion_otros_documentos'=>utf8_encode($row_evento['descripcion_otros_documentos']),
					'motivo_rechazo'=>utf8_encode($row_evento['motivo_rechazo']),
					'fecha_publicacion'=>utf8_encode($row_evento['fecha_publicacion']),
					'hora_publicacion'=>utf8_encode($row_evento['hora_publicacion']),
					'fecha_aclaraciones'=>utf8_encode($row_evento['fecha_aclaraciones']),
					'hora_aclaraciones'=>utf8_encode($row_evento['hora_aclaraciones']),
					'fecha_muestras'=>utf8_encode($row_evento['fecha_muestras']),
					'lugar_muestras'=>utf8_encode($row_evento['lugar_muestras']),
					'lugar_aclaraciones'=>utf8_encode($row_evento['lugar_aclaraciones']),
					'fecha_apertura'=>utf8_encode($row_evento['fecha_apertura']),
					'hora_apertura'=>utf8_encode($row_evento['hora_apertura']),
					'lugar_apertura'=>utf8_encode($row_evento['lugar_apertura']),
					'fecha_fallo'=>utf8_encode($row_evento['fecha_fallo']),
					'hora_fallo'=>utf8_encode($row_evento['hora_fallo']),
					'lugar_fallo'=>utf8_encode($row_evento['lugar_fallo']),
					'hora_muestras'=>utf8_encode($row_evento['hora_muestras']),
					'fecha_limite_pago'=>utf8_encode($row_evento['fecha_limite_pago']),
					'estatus'=>utf8_encode($row_evento['estatus']),
					'porcentaje_muestras'=>utf8_encode($row_evento['porcentaje_muestras']),
					'fecha_visita'=>utf8_encode($row_evento['fecha_visita']),
					'hora_visita'=>utf8_encode($row_evento['hora_visita']),
					'lugar_visita'=>utf8_encode($row_evento['lugar_visita']),
					'expediente'=>utf8_encode($row_evento['expediente']),
					'presentacion_producto'=>utf8_encode($row_evento['presentacion_producto']),
					
					'fecha_preguntas'=>utf8_encode($row_evento['fecha_preguntas']),
					'lugar_preguntas'=>utf8_encode($row_evento['lugar_preguntas']),
					'hora_preguntas'=>utf8_encode($row_evento['hora_preguntas']),
					'caducidad'=>utf8_encode($row_evento['caducidad'])
					));
				$sql_archivos = "SELECT *
				FROM vta_evento_archivos
				WHERE id_evento=".$row_evento['id_evento'];
				$rst_archivo = $db->query($sql_archivos);
				if($db->num_rows($rst_archivo)>0){
					$row_archivos = $db->fetch_assoc($rst_archivo);
					$json->setData(array('bases_licitacion'=>utf8_encode($row_archivos['bases_licitacion']),
						'actas_aclaracion'=>utf8_encode($row_archivos['actas_aclaracion']),
						'actas_apertura'=>utf8_encode($row_archivos['actas_apertura']),
						'actas_fallo'=>utf8_encode($row_archivos['actas_fallo']),
						'formato_carta_apoyo'=>utf8_encode($row_archivos['formato_carta_apoyo']),
						'tabla_productos'=>utf8_encode($row_archivos['tabla_productos']),
						'productos_adjudicados'=>utf8_encode($row_archivos['productos_adjudicados']),
						'bases_licitacion'=>utf8_encode($row_archivos['bases_licitacion']),
						'peticion_documental'=>utf8_encode($row_archivos['peticion_documental'])
						));
				}
			}
			else
				$json->setMensajeError("El evento '".strtoupper($txt_evento)."' no existe");
			
			$json->jsonEncode();
			break;














			case 'editar': case 'aceptada':
			#Validar si el numero de evento ya existe
			$sql_no_evento = "SELECT * FROM vta_evento WHERE no_evento = UPPER('".$_POST['no_evento']."') AND id_evento!=".$_POST['id_evento'];
			$rst_no_evento = $db->query($sql_no_evento);
			if($db->num_rows($rst_no_evento)>0){
				$json->setMensajeError("El número de Evento '".strtoupper($_POST['no_evento'])."' ya se encuentra registrado");
				$json->jsonEncode();
				exit();
			}
			
			#Obtener el número de evento y expediente anterior
			$sql_evento_ant = "SELECT no_evento, COALESCE(expediente,'') AS expediente
			FROM vta_evento
			WHERE id_evento=".$_POST['id_evento'];
			
			$rst_evento_ant = $db->query($sql_evento_ant);
			$row_evento_ant = $db->fetch_assoc($rst_evento_ant);
			$no_evento_ant = $row_evento_ant['expediente'];
			if($row_evento_ant['expediente']=='')
				$no_evento_ant = $row_evento_ant['no_evento'];
			
			$error=false;
			$fecha_actual = date("Y-m-d H:i:s");
			$sostenimiento_oferta='null';
			if($_POST['sostenimiento_oferta']!=-1)
				$sostenimiento_oferta=$_POST['sostenimiento_oferta'];
			$precio_requerido ='null';
			if($_POST['precio_requerido']!='-1' && $_POST['precio_requerido']!='')
				$precio_requerido="'".$_POST['precio_requerido']."'";
			$fecha_muestras='null';
			if(trim($_POST['fecha_muestras'])!='')
				$fecha_muestras="'".$_POST['fecha_muestras']."'";
			$hora_muestras ='null';
			if(trim($_POST['hora_muestras'])!=':')
				$hora_muestras="'".$_POST['hora_muestras']."'";


			$fecha_fallo ='null';
			if(trim($_POST['fecha_fallo'])!='')
				$fecha_fallo="'".$_POST['fecha_fallo']."'";
			$hora_fallo ='null';
			if(trim($_POST['hora_fallo'])!=':')
				$hora_fallo="'".$_POST['hora_fallo']."'";


			$fecha_preguntas ='null';
			if(trim($_POST['fecha_preguntas'])!='')
				$fecha_preguntas="'".$_POST['fecha_preguntas']."'";
			$hora_preguntas ='null';
			if(trim($_POST['hora_preguntas'])!=':')
				$hora_preguntas="'".$_POST['hora_preguntas']."'";

			$fecha_visita ='null';
			if(trim($_POST['fecha_visita'])!='')
				$fecha_visita="'".$_POST['fecha_visita']."'";
			$hora_visita ='null';
			if(trim($_POST['hora_visita'])!=':')
				$hora_visita="'".$_POST['hora_visita']."'";
			
			$fecha_limite_pago = 'null';
			if(trim($_POST['fecha_limite_pago'])!='')
				$fecha_limite_pago="'".$_POST['fecha_limite_pago']."'";
			
			$fecha_publicacion = 'null';
			if(trim($_POST['fecha_publicacion'])!='')
				$fecha_publicacion="'".$_POST['fecha_publicacion']."'";
			$hora_publicacion ='null';
			if(trim($_POST['hora_publicacion'])!=':')
				$hora_publicacion="'".$_POST['hora_publicacion']."'";
			
			$fecha_aclaraciones = 'null';
			if(trim($_POST['fecha_aclaraciones'])!='')
				$fecha_aclaraciones="'".$_POST['fecha_aclaraciones']."'";
			$hora_aclaraciones ='null';
			if(trim($_POST['hora_aclaraciones'])!=':')
				$hora_aclaraciones="'".$_POST['hora_aclaraciones']."'";
			
			$nacionalidad_producto = 'null';
			if($_POST['nacionalidad_producto']!=-1 && $_POST['nacionalidad_producto']!='')
				$nacionalidad_producto ="'".$_POST['nacionalidad_producto']."'";
			
			$presentacion_producto = 'null';
			if($_POST['presentacion_producto']!=-1 && $_POST['presentacion_producto']!='')
				$presentacion_producto ="'".$_POST['presentacion_producto']."'";
			
			$porcentaje_penalizacion = ($_POST['porcentaje_penalizacion']=='')?0:$_POST['porcentaje_penalizacion'];
			$porcentaje_registros = ($_POST['porcentaje_registros']=='')?0:$_POST['porcentaje_registros'];
			$porcentaje_apoyo = ($_POST['porcentaje_apoyo']=='')?0:$_POST['porcentaje_apoyo'];
			$porcentaje_muestras = ($_POST['porcentaje_muestras']=='')?0:$_POST['porcentaje_muestras'];
			$no_entrega = ($_POST['no_entrega']=='')?0:$_POST['no_entrega'];
			$techo_presupuestal = ($_POST['techo_presupuestal']=='')?0:$_POST['techo_presupuestal'];
			
			$set_estatus="";
			if($_POST['accion']=='aceptada'){
				$set_estatus=", estatus=2";
			}
			
			
			
			$db->query("START TRANSACTION");
			$sql_evento="UPDATE vta_evento
			SET no_evento='".$_POST['no_evento']."', 
			expediente=UPPER('".$_POST['expediente']."'), 
			id_cliente=".$_POST['id_cliente'].",  
			nuevo_cliente=UPPER('".utf8_decode(str_replace("'","''",$_POST['nuevo_cliente']))."'), 
			id_estado=".$_POST['id_estado'].", 
			costo_base=".str_replace(",","",$_POST['costo_base']).", 
			datos_banco=UPPER('".utf8_decode(str_replace("'","''",$_POST['datos_banco']))."'), 
			tipo_licitacion=".$_POST['tipo_licitacion'].", 
			tipo_producto='".$_POST['tipo_producto']."', 
			no_entrega=".$no_entrega.", 
			criterio_entrega='".$_POST['criterio_entrega']."',
			caducidad='".$_POST['caducidad']."',  
			modalidad=".$_POST['modalidad'].", 
			empresa_adicional=UPPER('".$_POST['empresa_adicional']."'), 
			criterios_penalizacion=UPPER('".utf8_decode(str_replace("'","''",$_POST['criterios_penalizacion']))."'), 
			sostenimiento_oferta=".$sostenimiento_oferta.", 
			porcentaje_penalizacion=".$porcentaje_penalizacion.", 
			nacionalidad_producto=".$nacionalidad_producto.", 
			techo_presupuestal=".str_replace(",","",$techo_presupuestal).", 
			precio_requerido=".$precio_requerido.", 
			requisitos_tecnicos=UPPER('".utf8_decode(str_replace("'","''",$_POST['requisitos_tecnicos']))."'), 
			requisitos_contables=UPPER('".utf8_decode(str_replace("'","''",$_POST['requisitos_contables']))."'), 
			carta_apoyo=".$_POST['carta_apoyo'].", 
			porcentaje_apoyo=".$porcentaje_apoyo.", 
			formato_carta_apoyo=".$_POST['formato_carta_apoyo'].", 
			registro_sanitarios=".$_POST['registro_sanitarios'].", 
			porcentaje_registros=".$porcentaje_registros.", 
			certificados_practicas=".$_POST['certificados_practicas'].", 
			muestras=".$_POST['muestras'].", 
			porcentaje_muestras=".$porcentaje_muestras.", 
			otros_documentos=".$_POST['otros_documentos'].", 
			descripcion_otros_documentos=UPPER('".utf8_decode(str_replace("'","''",$_POST['descripcion_otros_documentos']))."'), 
			motivo_rechazo=UPPER('".utf8_decode(str_replace("'","''",$_POST['motivo_rechazo']))."'), 
			fecha_publicacion=".$fecha_publicacion.", 
			hora_publicacion=".$hora_publicacion.", 
			fecha_aclaraciones=".$fecha_aclaraciones.", 
			hora_aclaraciones=".$hora_aclaraciones.", 
			fecha_muestras=".$fecha_muestras.", 
			lugar_muestras=UPPER('".utf8_decode(str_replace("'","''",$_POST['lugar_muestras']))."'), 
			lugar_aclaraciones=UPPER('".utf8_decode(str_replace("'","''",$_POST['lugar_aclaraciones']))."'), 
			fecha_apertura='".$_POST['fecha_apertura']."',
			hora_apertura='".$_POST['hora_apertura']."', 
			lugar_apertura=UPPER('".utf8_decode(str_replace("'","''",$_POST['lugar_apertura']))."'), 
			fecha_fallo=".$fecha_fallo.", 
			hora_fallo=".$hora_fallo.", 
			lugar_fallo=UPPER('".utf8_decode(str_replace("'","''",$_POST['lugar_fallo']))."'),

			fecha_preguntas=".$fecha_preguntas.", 
			hora_preguntas=".$hora_preguntas.", 
			lugar_preguntas=UPPER('".utf8_decode(str_replace("'","''",$_POST['lugar_preguntas']))."'),

			hora_muestras=".$hora_muestras.", 
			fecha_limite_pago=".$fecha_limite_pago.", 
			id_usuario_modificacion=".$_SESSION['idUser'].", 
			fecha_modificacion='".$fecha_actual."',
			presentacion_producto = ".$presentacion_producto.",
			fecha_visita = ".$fecha_visita.",
			hora_visita = ".$hora_visita.",
			lugar_visita = UPPER('".utf8_decode(str_replace("'","''",$_POST['lugar_visita']))."')
			".$set_estatus."
			WHERE id_evento=".$_POST['id_evento'];

			$db->query($sql_evento);

			  #Insertar Archivos del evento
			$lista_archivos="";
			$array_actas_aclaraciones=explode(",",$_POST['actas_aclaraciones']);
			$archivos_actas_aclaraciones = "";
			$coma = "";
			$coma_tmp = "";
			foreach($array_actas_aclaraciones AS $acta_aclaraciones){
				if(strpos($acta_aclaraciones, '@@') !== false){
					$lista_archivos.=$coma_tmp.$acta_aclaraciones;
					list($a_tmp,$a_name)=explode("@@",$acta_aclaraciones);
					$coma_tmp = ",";
				}
				else{
					$a_tmp='';
					$a_name = $acta_aclaraciones;
				}

				$archivos_actas_aclaraciones.=$coma.$a_name;
				$coma = ",";
			}

			$array_actas_apertura=explode(",",$_POST['actas_apertura']);
			$archivos_actas_apertura = "";
			$coma = "";
			foreach($array_actas_apertura AS $acta_apertura){
				if(strpos($acta_apertura, '@@') !== false){
					$lista_archivos.=$coma_tmp.$acta_apertura;
					list($a_tmp,$a_name)=explode("@@",$acta_apertura);
					$coma_tmp = ",";
				}
				else{
					$a_tmp='';
					$a_name = $acta_apertura;
				}
				$archivos_actas_apertura.=$coma.$a_name;
				$coma = ",";
			}

			

			$array_actas_fallo=explode(",",$_POST['actas_fallo']);
			$archivos_actas_fallo = "";
			$coma = "";
			if($_POST['actas_fallo']!=''){
				foreach($array_actas_fallo AS $acta_fallo){
					if(strpos($acta_fallo, '@@') !== false){
						$lista_archivos.=$coma_tmp.$acta_fallo;
						list($a_tmp,$a_name)=explode("@@",$acta_fallo);
						$coma_tmp = ",";
					}
					else{
						$a_tmp='';
						$a_name = $acta_fallo;
					}
					$archivos_actas_fallo.=$coma.$a_name;
					$coma = ",";
				}
			}



			// NEW
			$array_archivo_bases=explode(",",$_POST['archivo_bases']);
			$archivos_archivo_bases = "";
			$coma = "";
			if($_POST['archivo_bases']!=''){
				foreach($array_archivo_bases AS $acta_fallo){
					if(strpos($acta_fallo, '@@') !== false){
						$lista_archivos.=$coma_tmp.$acta_fallo;
						list($a_tmp,$a_name)=explode("@@",$acta_fallo);
						$coma_tmp = ",";
					}
					else{
						$a_tmp='';
						$a_name = $acta_fallo;
					}
					$archivos_archivo_bases.=$coma.$a_name;
					$coma = ",";
				}
			}




			$array_peticion_documental=explode(",",$_POST['peticion_documental']);
			$archivos_peticion_documental = "";
			$coma = "";
			if($_POST['peticion_documental']!=''){
				foreach($array_peticion_documental AS $acta_fallo){
					if(strpos($acta_fallo, '@@') !== false){
						$lista_archivos.=$coma_tmp.$acta_fallo;
						list($a_tmp,$a_name)=explode("@@",$acta_fallo);
						$coma_tmp = ",";
					}
					else{
						$a_tmp='';
						$a_name = $acta_fallo;
					}
					$archivos_peticion_documental.=$coma.$a_name;
					$coma = ",";
				}
			}




			$acuerdo_licitado="";
			if($_POST['acuerdo_licitado']!=''){
				if(strpos($_POST['acuerdo_licitado'], '@@') !== false){
					$a_acuerdo_licitado=explode("@@",$_POST['acuerdo_licitado']);
					$acuerdo_licitado=$a_acuerdo_licitado[1];
					$lista_archivos.=$coma_tmp.$_POST['acuerdo_licitado'];
					$coma_tmp = ",";
				}
				else
					$acuerdo_licitado=$_POST['acuerdo_licitado'];
			}

			$tabla_productos="";
			if($_POST['tabla_productos']!=''){
				if(strpos($_POST['tabla_productos'], '@@') !== false){
					$a_tabla_productos=explode("@@",$_POST['tabla_productos']);
					$tabla_productos=$a_tabla_productos[1];
					$lista_archivos.=$coma_tmp.$_POST['tabla_productos'];
					$coma_tmp = ",";
				}
				else
					$tabla_productos=$_POST['tabla_productos'];
			}


			
			




			$productos_adjudicados = "";
			
			if($_POST['productos_adjudicados']!=''){
				if(strpos($_POST['productos_adjudicados'], '@@') !== false){
					
					$a_productos_adjudicados=explode("@@",$_POST['productos_adjudicados']);
					$productos_adjudicados=$a_productos_adjudicados[1];
					$lista_archivos.=$coma_tmp.$_POST['productos_adjudicados'];
					$coma_tmp = ",";
					

					$mimes = array('application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					if(in_array($a_productos_adjudicados[2],$mimes)){
						if($a_productos_adjudicados[2] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
							require_once("../../../librerias/excel2007/PHPExcel.php");
							require_once("../../../librerias/excel2007/PHPExcel/Reader/Excel2007.php");
							$objLeer 		= new PHPExcel_Reader_Excel2007();
							$objPHPExcel	= $objLeer->load("/home/Test/public_html/ventas/licitaciones/eventos".$a_productos_adjudicados[0]);
						} 
						
						if($a_productos_adjudicados[2] == "application/vnd.ms-excel"){
							require_once("../../../librerias/excel2007/PHPExcel.php");
							require_once("../../../librerias/excel2007/PHPExcel/Reader/Excel5.php");
							$objLeer 		= new PHPExcel_Reader_Excel5();
							$objPHPExcel	= $objLeer->load("/home/Test/public_html/ventas/licitaciones/eventos".$a_productos_adjudicados[0]);
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
										
										//$data[$columnNames[$i]] = $datos[$i];
										// /[^A-Za-z0-9\- ]/ ALPHANNUMERIC WITH SPACES
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

							 // print_r($allData); // TODOS LOS DATOS CON SUS CORRESPONDIENTES CAMPOS
							 // exit();

							$id_cliente='null';
							if($_POST['id_cliente']!=0)
								$id_cliente=$_POST['id_cliente'];

							#Crear liictacion.
							$sql_new_licitacion = "INSERT INTO vta_licitacion (numero_evento, id_cliente, cliente_nuevo,id_estado,
							fecha_licitacion,fecha_captura,id_usuario_captura, id_evento)
							VALUES ('".$_POST['no_evento']."',".$id_cliente.", '".$_POST['nuevo_cliente']."', ".$_POST['id_estado'].",
							'".$_POST['fecha_apertura']." ".$_POST['hora_apertura'].":00','".$fecha_actual."',".$_SESSION['idUser'].",".$_POST['id_evento'].")";

							$id_licitacion= $db->insert_id($sql_new_licitacion,"id_licitacion");

							

							$i = 1;
							$o = 1;
							$verifyProveedor = 0;

							//START VALIDATION AREA
							foreach($allData AS $Akey => $registro){ 
								// if there is proveedor then we've  to validate the extras values : clave_sistema','costo','precio','laboratorio', 'codigo_barras','empresa'
								if ($allData[$Akey]['proveedor'] == ''){ 
									if(isset($registro['clave_sistema']) && $registro['clave_sistema'] != ''){
										echo "En la fila No. ".($i+1)." se necesita un proveedor para agregar la columna Clave Sistema\n";
										exit();
									}else{
										unset($allData[$Akey]['clave_sistema']);
										unset($registro['clave_sistema']);
									}

									if(isset($registro['costo']) && $registro['costo'] != ''){
										echo "En la fila No. ".($i+1)." se necesita un proveedor para agregar la columna Costo\n";
										exit();
									}else{
										unset($allData[$Akey]['costo']);
										unset($registro['costo']);
									}

									if(isset($registro['precio']) && $registro['precio'] != ''){
										echo "En la fila No. ".($i+1)." se necesita un proveedor para agregar la columna Precio\n";
										exit();
									}else{
										unset($allData[$Akey]['precio']);
										unset($registro['precio']);
									}

									if(isset($registro['laboratorio']) && $registro['laboratorio'] != ''){
										echo "En la fila No. ".($i+1)." se necesita un proveedor para agregar la columna Laboratorio\n";
										exit();
									}else{
										unset($allData[$Akey]['laboratorio']);
										unset($registro['laboratorio']);
									}

									if(isset($registro['codigo_barras']) && $registro['codigo_barras'] != ''){
										echo "En la fila No. ".($i+1)." se necesita un proveedor para agregar la columna Codigo Barras\n";
										exit();
									}else{
										unset($allData[$Akey]['codigo_barras']);
										unset($registro['codigo_barras']);
									}

									if(isset($registro['empresa']) && $registro['empresa'] != ''){
										echo "En la fila No. ".($i+1)." se necesita un proveedor para agregar la columna Empresa\n";
										exit();
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
										exit();
									}

									if($key == 'partida' && (($value == '') || !is_numeric($value)  ||  $value < 1)){
										echo "En la fila No. ".($i+1)." la columna de Partida debe ser un valor númerico mayor a cero o se encuentra vacia\n";
										exit();
									}


									if ($key == 'descripcion' && ($value == '') ){
										echo "En la fila No. ".($i+1)." la columna de Descripción se encuentra vacia \n";
										exit();
									}

									if( $key == 'cantidad_minima' && (($value == '') || !is_numeric($value) || ($value) < 1)){
										echo "En la fila No. ".($i+1)." la columna de Cantidad Minima debe ser mayor a cero, debe ser un valor númerico o se encuentra vacia\n";
										exit();
									}

									if( $key == 'cantidad_maxima' &&  $value != ''){
										if ((!is_numeric($value))) {
											echo "En la fila No. ".($i+1)." la columna de Cantidad Maxima debe ser un valor númerico\n";
											exit();
										}
									}

									if( $key == 'cantidad_maxima'){
										if(is_numeric($value) && ($value) < 1){
											echo "En la fila No. ".($i+1)." la columna de Cantidad Maxima debe ser un valor mayor a cero\n";
											exit();
										}
									}


									if ($key == 'proveedor' && ($value == '')){
										echo "En la fila No. ".($i+1)." la columna Proveedor se encuentra vacia \n";
										exit();
									}

									if($key == 'proveedor' && $value != ''){

										$proveedorSQL = "SELECT id_proveedor, nombre FROM cat_proveedor WHERE  upper(nombre) LIKE upper('%$value%') AND estatus = 1";
										$res = $db->query($proveedorSQL);
										$no_resp = $db->num_rows($res);

										if ($no_resp > 1) {
											echo "En la fila No. ".($i+1)." el nombre proveedor debe ser mas especifico, se encontraron dos o mas resultados similares \n";
											exit();
										}

										if ($no_resp == 0){
											echo "En la fila No. ".($i+1)." el nombre de proveedor no existe \n";
											exit();
										}

										$row = $db->fetch_assoc($res);
										$allData[$Akey]['proveedor'] = $row['id_proveedor'];

									}



									if ($key == 'clave_sistema' && ($value == '') ){
										echo "En la fila No. ".($i+1)." la columna Clave Corporativo se encuentra vacia \n";
										exit();
									}

									if($key == 'clave_sistema' && $value != ''){

										$claveSQL ="SELECT clave FROM catalogo_producto WHERE estatus = 1 AND clave ='$value'";
										$res = $db->query($claveSQL);
										$no_resp = $db->num_rows($res);


										if ($no_resp > 1) {
											echo "En la fila No. ".($i+1)." la clave esta duplicada \n";
											exit();
										}

										if ($no_resp == 0){
											echo "En la fila No. ".($i+1)." la clave no existe \n";
											exit();
										}

									}

									if( $key == 'costo' && (($value == '') || !is_numeric($value) || $value < 1 )){
										echo "En la fila No. ".($i+1)." la columna de Costo, debe ser un valor númerico mayor a cero o se encuentra vacia\n";
										exit();
									}

									if( $key == 'precio' && (($value == '') || !is_numeric($value)|| $value < 1 )){
										echo "En la fila No. ".($i+1)." la columna de Precio, debe ser un valor númerico mayor a cero o se encuentra vacia\n";
										exit();
									}



									if($key == 'laboratorio' && $value != ''){

										$value = str_replace('.', '', $value);

										$laboratorioSQL = "SELECT id_laboratorio, laboratorio FROM cat_laboratorio WHERE  REPLACE(upper(laboratorio),'.','') LIKE upper('%$value%') AND estatus = 1";
										$res = $db->query($laboratorioSQL);
										$no_resp = $db->num_rows($res);

										if ($no_resp > 1) {
											echo "En la fila No. ".($i+1)." el laboratorio debe ser mas especifico, se encontraron dos o mas resultados similares \n";
											exit();
										}

										if ($no_resp == 0){
											echo "En la fila No. ".($i+1)." el nombre de laboratorio no existe \n";
											exit();
										}

										$row = $db->fetch_assoc($res);
										$allData[$Akey]['laboratorio'] = $row['id_laboratorio'];

									}




									if ($key == 'empresa' && $value != ''){
										if (strtoupper($value) != 'Test' && strtoupper($value) != 'QUIROPRACTICO'){
											echo "En la fila No. ".($i+1)." la columna Empresa debe ser Test o QUIROPRACTICO : Se encontro $value \n";
											exit();
										}
									}



									if ($key == 'empresa' && (strtolower($value) == 'Test') ){
										echo "En la fila No. ".($i+1)." la columna Empresa debe ser diferente a la que licita \n";
										exit();
									}
								}
								$i++;
							}


							$claves = array_count_values(array_column($allData, 'clave_cbn')); // CUENTA SI HAY CLAVES REPETIDAS
							$partidas = array_count_values(array_column($allData, 'partida')); // CUNETA SI HAY PARTIDAS REPETIDAS

							foreach ($claves as $key => $clave) {
								if ($clave > 1) {
									echo 'La clave '.$key.' esta duplicada';
									exit();
								}
							}

							foreach ($partidas as $key => $partida) {
								if ($partida > 1) {
									echo 'La partida '.$key.' esta duplicada';
									exit();
								}
							}
						    //END VALIDATIONS AREA

						    // print_r($allData); exit();


							foreach($allData AS $key => $registro){ // SE RECORRE EL ARREGLO DE TODOS LOS REGISTROS
								
								$columnNamesSQL = "";
								$createData ="";
								
								foreach ($registro as $key => $value) { // SE RECORREN LOS VALORES DENTRO DE ESE REGISTRO PARA LIMPIARLOS Y CONCATENARLOS
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
						}
					}else{
						$productos_adjudicados=$_POST['productos_adjudicados'];
					}
				}
				


				$sql_archivos="UPDATE vta_evento_archivos
				SET bases_licitacion='".utf8_decode(str_replace("'","''",$archivos_archivo_bases))."', 
				actas_aclaracion='".utf8_decode(str_replace("'","''",$archivos_actas_aclaraciones))."',
				peticion_documental='".utf8_decode(str_replace("'","''",$archivos_peticion_documental))."', 
				actas_apertura='".utf8_decode(str_replace("'","''",$archivos_actas_apertura))."', 
				actas_fallo='".utf8_decode(str_replace("'","''",$archivos_actas_fallo))."', 
				formato_carta_apoyo='".utf8_decode(str_replace("'","''",$acuerdo_licitado))."', 
				tabla_productos='".utf8_decode(str_replace("'","''",$tabla_productos))."', 
				productos_adjudicados='".utf8_decode(str_replace("'","''",$productos_adjudicados))."'
				WHERE id_evento=".$_POST['id_evento'];
				$db->query($sql_archivos);

				$json->setVar(array("id_evento" => $_POST['id_evento']));
				//$json->setVar(array("no_evento" => strtoupper($_POST['no_evento'])));
				if(trim($_POST['expediente'])=='')
					$json->setVar(array("no_evento" => utf8_decode(str_replace("'","''",$_POST['no_evento']))));
				else
					$json->setVar(array("no_evento" => utf8_decode(str_replace("'","''",$_POST['expediente']))));
				$json->setVar(array("archivos_eliminar" => $_POST['archivos_eliminados']));
				$json->setVar(array("archivos" => $lista_archivos));
				$json->setVar(array("no_evento_ant" => $no_evento_ant));

				$error = $db->query("COMMIT");
				$json->jsonEncode();
				break;













































				case 'rechazar_evento':
				$id_evento= $_POST['id_evento'];
				$db->query("START TRANSACTION");
				$sql_evento = " UPDATE vta_evento
				SET estatus=3,
				motivo_rechazo =UPPER('".$_POST['motivo_rechazo']."')
				WHERE id_evento=".$id_evento;
				$db->query($sql_evento);
				$rst_evento = $db->query($sql_evento);
				$db->query("COMMIT");
				$json->setVar(array("return" => 1));
				$json->jsonEncode();
				break;
				case 'paises_tratado':
				//SELECT pais FROM cat_pais WHERE tratado=1 ORDER BY pais
				$sql_paises = "SELECT pais FROM cat_pais ORDER BY pais";
				$rst_paises = $db->query($sql_paises);
				$tabla ="<div><table align='center' class=''><tr>";
				$cont=0;
				while($row_paises = $db->fetch_assoc($rst_paises)){
					if($cont% 4 == 0)
						$tabla .="</tr><tr>";
					$tabla.="<td style='width:25%'>".$row_paises['pais']."</td>";
					$cont++;
				}
				$tabla.="</tr></table></div>" ;
				echo $tabla;
				break;
			}
		}
		else{
			$json->setMensajeError("Archivo dañado");
			$json->jsonEncode();
		}

		function stripAccents($string){
			$string=strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
			$string=str_replace(" ","_",$string);
			$string=str_replace("&","",$string);
			$string=str_replace("(","",$string);
			$string=str_replace(")","",$string);
			$string=str_replace("/","",$string);
			$string=str_replace("?","",$string);
			$string=str_replace("¿","",$string);
			$string=str_replace("'","",$string);
			$string=str_replace("!","",$string);
			$string=str_replace("¡","",$string);
			$string=str_replace("%","",$string);
			$string=str_replace("#","",$string);
			$string=str_replace("*","",$string);
			return $string;
		}
		?>              