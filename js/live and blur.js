/*
	Nombre: Hang Tu Wong Ley
    Fecha:2016-10-19
    */
    var url = "ventas/licitaciones/consulta_licitacion/acciones.php";
    var $$ = jQuery;

    jQuery(document).ready(function($){
      var value_ant = '';
      var input = 

      $$('#evento').val('100A');
      consultar();

      $$(".cell > input").live("focus",function(){ 
       value_ant = $$(this).val();
     });

      $$(".cell > input").live("blur",function(){ 

       tr = $$(this).closest('tr');
       input = $$(this);
       column = $$(this).attr('class'); 
       datos = $$(this).val();
       

       //newest element{
         if(tr.data('newest') == true){
           $$(tr).each(function() {
             //required values 
             var partida = $$(this).find(".partida").val().trim();
             var clave =   $$(this).find(".clave_cbn").val().trim();
             var descripcion = $$(this).find(".descripcion").val().trim();
             var cantidad_minima = $$(this).find(".cantidad_minima").val().trim();

             if(partida !== '' && clave !== '' && descripcion !== '' && cantidad_minima !== ''){ 

              data = {
                accion: 'insert',
                datos:[{
                  id_licitacion:$$('#licitacionID').val(), 
                  partida:partida,
                  clave_cbn:clave,
                  descripcion:descripcion,
                  nombre_comercial:$$(this).find(".nombre_comercial").val().trim(),
                  presentacion:$$(this).find(".presentacion").val().trim(),
                  cantidad_minima:cantidad_minima,
                  cantidad_maxima:$$(this).find(".cantidad_maxima").val().trim(),
                  proveedor:$$(this).find(".proveedor").val().trim(),
                  clave_sistema:$$(this).find(".clave_sistema").val().trim(),
                  costo:$$(this).find(".costo").val().trim(),
                  precio:$$(this).find(".precio").val().trim(),
                  laboratorio:$$(this).find(".laboratorio").val().trim(),
                  codigo_barras:$$(this).find(".codigo_barras").val().trim(),
                  empresa:$$(this).find(".empresa").val().trim()
                }]
              };

              var insert = $$.post(url, {data:JSON.stringify(data)});

              insert.done(function(data){

                tr.data("newest",false); 
                tr.data("partida",partida);
                tr.data("clave_cbn",clave);

                tr.attr("data-newest",false); 
                tr.attr("data-partida",partida);
                tr.attr("data-clave_cbn",clave);

              });

              insert.fail(function(data){
                alert(data.responseText);
              });
            }
          });
           return;
         }
       //}




       //updating element
       var proveedor = "";
       $$(tr).each(function() {
         proveedor = $$(this).find(".proveedor").val().trim();
       });

       //INSERTANDO EL PROVEEDOR EN LA FILA


      

      disable_rows(tr);//Si no hay proveedor habilita o desahabilita las columnas




      id_licitacion = tr.data('id-licitacion');
      partida    = tr.data('partida');
      clave      = tr.data('clave_cbn');

      data = {
        accion: 'update',
        column: column,
        data: datos,
        id_licitacion: id_licitacion,
        partida: partida,
        clave: clave,
        value_ant: value_ant,
        proveedor:proveedor
      };

      if(datos.trim() != value_ant.trim()){ //si existen cambios en la informacion

       var update = $$.post(url,{data:JSON.stringify(data)});

       update.done(function(data){
         console.log('datos actualizados:'+data);
       });

       update.fail(function(data){
         $$(input).val(value_ant);
         alert(data.responseText);
       });

     }
   });
});


function consultar(){
 $$("#tbl_licitaciones").html('');

 if($$.trim($$("#evento").val()) == ''){
  alert("Debe de indicar un numero de evento");
  return;
}

data = {
  accion: 'cargar_licitacion',
  evento: $$("#evento").val()
};

var search = $$.post(url,{data:JSON.stringify(data)});
search.done(function(data){
  try{
   resp = $$.parseJSON(data);
 }catch(e){
   console.log('something was wrong!');
   return;
 }


 $$('#txt_evento').val(resp.numero_evento);
 $$('#txt_cliente').val(resp.razon_social);
 $$('#txt_fecha_licitacion').val(resp.fecha_licitacion);
 $$('#txt_estado').val(resp.estado);
 $$('#licitacionID').val(resp.id_licitacion);

 if(resp.estado == ''){
   $$('#txt_estado').val('NACIONAL');
 }
           rows(resp.rows); // PINTA TODOS LOS DATOS DE EL DOCUMETO 
         });
} 


function add_row(){
  var licitacionID = $$('#licitacionID').val();
  var array = [{id_licitacion: licitacionID,
    partida:'',clave_cbn:'',descripcion:'',nombre_comercial:'',presentacion:'',cantidad_minima:'',cantidad_maxima:'',nom_proveedor:'',
    clave_sistema:'',costo:'',precio:'',nom_laboratorio:'',codigo_barras:'',empresa:''}];
    rows(array);
  }


  function delete_row(data){
    var r = confirm("¿Esta seguro que quiere eliminar este renglon?");
    if (r == true) {
      tr = $$(data).closest("tr");

      id_licitacion = tr.data('id-licitacion');
      partida    = tr.data('partida');
      clave      = tr.data('clave_cbn');

      data = {
        accion: 'delete',
        id_licitacion: id_licitacion,
        partida: partida,
        clave: clave
      };

      var remove = $$.post(url, {data: JSON.stringify(data)});

      remove.done(function(data){
       console.log(data);
       tr.remove();
     });

      remove.fail(function(data){
        alert('Ocurrio un error al eliminar los datos');
      });
    }
  }



  function rows(data){
    for (var i = 0; i < data.length; i++){
     tr = '<tr data-id-licitacion='+data[i].id_licitacion+' data-partida="'+data[i].partida+'" data-clave_cbn="'+data[i].clave_cbn+'" data-newest="false">';
     tr +='<td class="cell"></td>';
     tr +='<td class="cell"><input type="text" class="partida" value="'+data[i].partida+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" class="clave_cbn" value="'+data[i].clave_cbn+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" class="descripcion" value="'+data[i].descripcion+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" class="nombre_comercial" value="'+data[i].nombre_comercial+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" class="presentacion" value="'+data[i].presentacion+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" class="cantidad_minima" value="'+data[i].cantidad_minima+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" class="cantidad_maxima" value="'+data[i].cantidad_maxima+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" class="proveedor" value="'+data[i].nom_proveedor+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" class="clave_sistema" value="'+data[i].clave_sistema+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" class="costo" value="'+data[i].costo+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" class="precio" value="'+data[i].precio+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" class="laboratorio" value="'+data[i].nom_laboratorio+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" class="codigo_barras" value="'+data[i].codigo_barras+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" class="empresa" value="'+data[i].empresa+'" style="text-transform:uppercase"></td>';
     tr +='<td style="text-align:center" class="cell"><img src="iconos/delete.png" onclick="delete_row(this)" style="cursor:pointer;"></td>';
     tr +='<td class="cell"></td>';
     tr +='</tr>';

     var el = $$(tr);

       if (data[i].partida == ''){ // When we are inserting a new row 
         el.attr("data-newest",true);
         el.attr("data-partida",false);
         el.attr("data-clave_cbn",false);
       }

       disable_rows(el);

       $$('#tbl_licitaciones').append(el);
     }
   }


   function disable_rows(tr){
     $$(tr).each(function() {
      proveedor = $$(this).find(".proveedor").val().trim();
      if (proveedor == ''){
        $$(this).find(".clave_sistema").attr('disabled','disabled').val('');
        $$(this).find(".costo").attr('disabled','disabled').val('');
        $$(this).find(".precio").attr('disabled','disabled').val('');
        $$(this).find(".laboratorio").attr('disabled','disabled').val('');
        $$(this).find(".codigo_barras").attr('disabled','disabled').val('');
        $$(this).find(".empresa").attr('disabled','disabled').val('');
      }else{
        $$(this).find(".clave_sistema").removeAttr('disabled');
        $$(this).find(".costo").removeAttr('disabled');
        $$(this).find(".precio").removeAttr('disabled');
        $$(this).find(".laboratorio").removeAttr('disabled');
        $$(this).find(".codigo_barras").removeAttr('disabled');
        $$(this).find(".empresa").removeAttr('disabled');
      }
    });
   }




























   function modificar(){
    alert('Todos los datos se han actualizado');
  }















//  function cargar_excel(){
//    if($$('#licitacion')[0].files.length !== 0){
//     var inputFile = $$('#licitacion');	
//     var file = inputFile[0].files[0];
//     var size_max = 20248729; 
//     if ((file.name.endsWith('.xls') || file.name.endsWith('.xlsx')) && (file.size < size_max)){
// 			// console.log('working well');
// 		}else{
// 			alert('Error: Unicamente se permiten archivos de Microsoft Excel');
// 			$$('#licitacion').val("");
// 			return;
// 		}
// 	}else{
// 		alert("Error: Debe Seleccionar un archivo");
// 		return;
// 	}

// 	var data  = new FormData();
// 	var url   = ruta_acciones;

// 	data.append('accion','cargar_excel');
// 	data.append('licitacion', $$('#licitacion')[0].files[0]);

// 	jQuery.ajax({
// 		url:url,
// 		type:'POST',
// 		contentType:false,
// 		data:data,
// 		processData:false,
// 		cache:false,
// 		beforeSend: function(){
// 		     //parent.document.getElementById("AjaxloadData").style.display = "block";
//        },
//        complete: function(){
// 			//parent.document.getElementById("AjaxloadData").style.display = "none";
// 		},
// 	}).done(function(data){

// 		try{
// 			var resp = $$.parseJSON(data);
// 		}catch(e){
// 			alert(data);
// 		}

// 		 // console.log(resp[0]);

// 		 for (var i = 0; i < resp.length; i++) {
// 		 	tr = "<tr>";

// 		 	tr+="<td class='cell'></td>";
// 		 	tr+="<td class='cell'>"+resp[0].descripcion+"</td>";
// 		 	tr+="<td class='cell'>"+resp[0].descripcion+"</td>";
// 		 	tr+="<td class='cell'>"+resp[0].descripcion+"</td>";
// 		 	tr+="<td class='cell'>"+resp[0].descripcion+"</td>";
// 		 	tr+="<td class='cell'>"+resp[0].descripcion+"</td>";
// 		 	tr+="<td class='cell'>"+resp[0].descripcion+"</td>";
// 		 	tr+="<td class='cell'>"+resp[0].descripcion+"</td>";
// 		 	tr+="<td class='cell'>"+resp[0].descripcion+"</td>";
// 		 	tr+="<td class='cell'>"+resp[0].descripcion+"</td>";
// 		 	tr+="<td class='cell'>"+resp[0].descripcion+"</td>";
// 		 	tr+="<td class='cell'>"+resp[0].descripcion+"</td>";
// 		 	tr+="<td class='cell'>"+resp[0].descripcion+"</td>";
// 		 	tr+="<td class='cell'>"+resp[0].descripcion+"</td>";
// 		 	tr+="<td class='cell'>"+resp[0].descripcion+"</td>";
// 		 	tr+="<td class='cell'>"+resp[0].descripcion+"</td>";
// 		 	tr+="<td class='cell'></td>";

// 		 	tr+="</tr>";
// 		 	$$('#tbl_licitaciones').append(tr);
// 		 }

// 		 var trs = $$("#tbl_licitaciones").find("tbody>tr");
// 		 console.log(trs);

// 		});
// }


// function mostrarFormato(){
// 	Dialog.alert(
// 		"<img src='ventas/licitaciones/eventos/imagenExcel.png' />", 
// 		{
// 			width:1200, 
// 			height:170, 
// 			className: "alphacube", 
// 			okLabel: "Cerrar",
// 			ok:function(win){ return true; }
// 		});
// }

