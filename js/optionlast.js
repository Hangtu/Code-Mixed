/*
	Nombre: Hang Tu Wong Ley
    Fecha:2016-10-19
    */
    var url = "ventas/licitaciones/fallo_licitacion/acciones.php";
    var $$ = jQuery;


    jQuery(document).ready(function($){

      var selectValue_ant = 0;

      $$('#txt_buscar_evento').val('100b');
      search();

      $$('#txt_buscar_evento').keypress(function(e) {
        if(e.which == 13) {
          search();
        }   
      });

      $$(".cell > input").live("focus",function(){ 
        $$(this).parent().parent().addClass('highlightedRow');
      });

      $$(".cell > input").live("blur",function(){ 
        $$(this).parent().parent().removeClass('highlightedRow');
      });

      
      $$('.cell > .oferente').live('focus',function(){
        selectValue_ant = $$(this).val();
      });


      $$('.cell > .oferente').live('change',function(){
       var el = $$(this);
       var value = $$(this).val();
       el.blur();

       if(value == "newest" ){
         var name = prompt("Agregue un nuevo proveedor", "Test");
         if (name != null) {

          data = {
            accion: 'add_provedor',
            name: name,
          };

          var add = $$.post(url, {data: JSON.stringify(data)});

          add.done(function(data){
          $$('#tbl_licitaciones > tr').each(function(){ //read each tr column
            $$(this).find('option:last').before($$('<option>', { 
              value: data,
              text : name.toUpperCase()
            }));
          });
          el.val(data);
        });

          add.fail(function(data){
           el.val(selectValue_ant);
           alert(data.responseText);
         });

        }else{
         $$(this).val(selectValue_ant);
       }
     }

   });


      $$('.cell > .cantidad, .costo').live('keyup', function(event) {
       var el = $$(this);
       clase  = el.attr('class'); 

       if(clase == 'cantidad'){
        var cantidad = el.val();
        var costo = el.closest('tr').find('.costo').val();
        el.closest('tr').find('.monto').val((cantidad * costo).toFixed(2));
      }

      if(clase == 'costo'){
        var costo = el.val();
        var cantidad = el.closest('tr').find('.cantidad').val();
        el.closest('tr').find('.monto').val((cantidad * costo).toFixed(2));
      }

    });



    });

    function search(){
      var evento  = $$("#txt_buscar_evento").val().trim();
      if (evento !== ""){
        var vars = null
        document.getElementById("btn_search").disabled = true;
        vars = "accion=search&evento="+evento;
        var win = new Window({className: "alphacube", width:600, height:300, zIndex: 100, resizable: true, title: "Folio",
          onClose:function(win){document.getElementById("btn_search").disabled = false;},
          showEffectOptions: {duration:0.2}});
        win.setAjaxContent(url, {method:'post', postBody:vars});
        win.setConstraint(true, {left:5, right:5, top: 30, bottom:10})
        win.showCenter(); 
      }
      else{
        alert("Debe indicar un numero de evento");
      }
    }


    function search_selected(idLicitacion, noEvento){

     Windows.closeAll();
     $$("#tbl_licitaciones").html('');
     $$("#txt_numero_evento").val('');
     $$("#txt_cliente").val('');
     $$("#txt_fecha_licitacion").val('');
     $$("#txt_estado").val('');
     $$('#licitacion_file').val('');

     data = {  
      accion: 'cargar_licitacion',
      id_licitacion: idLicitacion,
      evento: noEvento
    };

    var search = $$.post(url,{data:JSON.stringify(data)});

    search.done(function(data){

     try{
       resp = $$.parseJSON(data);
     }catch(e){
       console.log('something was wrong!');
       return;
     }

     // console.log(resp);

     $$('#licitacionID').val(idLicitacion);
     $$('#eventoID').val(noEvento);

     $$('#txt_numero_evento').val(resp.numero_evento);
     $$('#txt_fecha_licitacion').val(resp.fecha_licitacion);
     $$('#txt_estado').val(resp.estado);
     
     if (resp.razon_social != ''){
      $$('#txt_cliente').val(resp.razon_social);
    }else{
      $$('#txt_cliente').val(resp.cliente_nuevo);
    }

    if(resp.estado == ''){
      $$('#txt_estado').val('NACIONAL');
    }

    if (resp.rows.length == 0){
      $$('#empty').html('No se ha capturado información para este evento');
      return;
    }
      rows(resp.rows, resp.provedores); // PINTA TODOS LOS DATOS DE EL DOCUMETO 
    });

    search.fail(function(data){
      alert(data.responseText);
    });
  } 




  function rows(data,provedores){
    for (var i = 0; i < data.length; i++){
     tr = '<tr data-id-licitacion='+data[i].id_licitacion+' data-partida="'+data[i].partida+'" data-clave_cbn="'+data[i].clave_cbn+'" data-id-oferente="'+data[i].id_oferente+'">';
     tr +='<td class="cell"></td>';
     tr +='<td class="cell"><input type="text" onkeypress="return validar(event,this,&quot;numeros&quot;)" class="partida" value="'+data[i].partida+'" style="text-transform:uppercase" readonly></td>';
     tr +='<td class="cell"><input type="text" onkeypress="return validar(event,this,&quot;texto&quot;)"   class="clave_cbn" value="'+data[i].clave_cbn+'" style="text-transform:uppercase" readonly></td>';
     tr +='<td class="cell"><input type="text" onkeypress="return validar(event,this,&quot;texto&quot;)"   class="descripcion" value="'+data[i].descripcion+'" style="text-transform:uppercase" readonly></td>';
     tr +='<td class="cell"><input type="text" onkeypress="return validar(event,this,&quot;texto&quot;)"   class="nombre_comercial" value="'+data[i].nombre_comercial+'" style="text-transform:uppercase" readonly></td>';
     tr +='<td class="cell"><input type="text" onkeypress="return validar(event,this,&quot;texto&quot;)"   class="presentacion" value="'+data[i].presentacion+'" style="text-transform:uppercase" readonly></td>';
     tr +='<td class="cell"><input type="text" onkeypress="return validar(event,this,&quot;texto&quot;)" class="marca" value="'+data[i].marca+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><select class="oferente" style="width:100%;"> <option value="" selected>--Seleccione una opción--</option>   <option value="newest">Agregar Nuevo</option> </select></td>';
     tr +='<td class="cell"><input type="text" onkeypress="return validar(event,this,&quot;numeros&quot;)"   class="cantidad" value="'+data[i].cantidad+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" onkeypress="return validar(event,this,&quot;precio&quot;)"  class="costo" value="'+data[i].costo+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" onkeypress="return validar(event,this,&quot;texto&quot;)"   class="cantidad_maxima" value="'+data[i].cantidad_maxima+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell"><input type="text" onkeypress="return validar(event,this,&quot;precio&quot;)"  class="monto" value="'+(data[i].cantidad * data[i].costo).toFixed(2)+'" style="text-transform:uppercase" readonly></td>';
     tr +='<td style="text-align:center" class="cell"><img src="iconos/delete.png" onclick="delete_row(this)" style="cursor:pointer;"></td>';
     tr +='<td class="cell"></td>';
     tr +='</tr>';
     var el = $$(tr);
     $$('#tbl_licitaciones').append(el);

    $$.each(provedores, function (i, item) { // CARGA EL SELECT CON LOS LABORATORIOS
      el.find('.oferente option:last').before($$('<option>', { 
        value: item.id_oferente,
        text : item.oferente
      }));
    });

    el.find('.oferente').val(data[i].id_oferente);

  }
}


function delete_row(data){
  var r = confirm("¿Esta seguro que quiere eliminar este renglon?");
  if (r == true) {
    tr = $$(data).closest("tr");
    id_licitacion = tr.data('id-licitacion');
    partida    = tr.data('partida');
    clave      = tr.data('clave_cbn');
    oferente   = tr.data('id-oferente');

    data = {
      accion: 'delete',
      id_licitacion: id_licitacion,
      partida: partida,
      clave: clave,
      id_oferente: oferente
    };

    if (clave == ''){
      tr.remove();
      return;
    }

    var remove = $$.post(url, {data: JSON.stringify(data)});

    remove.done(function(data){
      tr.remove();
    });

    remove.fail(function(data){
      alert('Ocurrio un error al eliminar los datos');
    });
  }
}


function guardar(){
  var rows = [];
  try{
    $$('#tbl_licitaciones > tr').each(function(){ //read each tr column
      row = {
        marca : $$(this).find(".partida").val().trim(),
        id_oferente : $$(this).find(".clave_cbn").val().trim(),
        cantidad : $$(this).find(".descripcion").val().trim(),
        cantidad_maxima : $$(this).find(".nombre_comercial").val().trim(),
        costo : $$(this).find(".empresa").val().trim()
      }
      // validar_row(this,row);
      rows.push(row);
    });
  }catch(err){
   return;
 }

 if (rows.length == 0){
  alert('No existe información para guardar');
  return;
}

id_licitacion = $$('#licitacionID').val();

data = {
  accion: 'update',
  id_licitacion : id_licitacion,
  rows:rows
};

var update = $$.post(url, {data: JSON.stringify(data)});

update.done(function(data){
 alert(data);
 location.reload();
});

update.fail(function(data){
  alert(data.responseText);
});

}

// function validar_row(tr,data){

//   required = ['partida','clave_cbn','descripcion','cantidad_minima'];
//   extraRequired = ['clave_sistema','costo','precio'];
//   extraAll = ['clave_sistema','costo','precio','laboratorio','codigo_barras','empresa'];

//     for(var i = 0; i < required.length; i++){ //validating required fields
//       if(data[required[i]] == '') {
//        alert("El campo "+required[i].replace('_',' ').toUpperCase() +" no puede quedar vacio");
//        $$(tr).find('.'+required[i]).focus();
//        throw 'Error';
//      }
//    }

//    if (data.proveedor != ''){
//     for(var i = 0; i < extraRequired.length; i++){ // validating optional required fields
//       if(data[extraRequired[i]] == ''){
//        alert("El campo "+extraRequired[i].replace('_',' ').toUpperCase() +" no puede quedar vacio");
//        $$(tr).find('.'+extraRequired[i]).focus();
//        throw 'Error';
//      }
//    }
//  }

//  if(data.proveedor == ''){
//   for(var i = 0; i < extraAll.length; i++){ // validating optional fields when there's not proveedor
//     if(data[extraAll[i]] != ''){
//      alert("Agregue un PROVEEDOR para insertar el campo "+extraAll[i].replace('_',' ').toUpperCase());
//      $$(tr).find('.proveedor').focus();
//      throw 'Error';
//    }
//  }
// }
// }










function cargar_excel(){

  if($$('#licitacionID').val() == ''){
   return;
 }

 if($$('#licitacion_file')[0].files.length !== 0){
  var inputFile = $$('#licitacion_file'); 
  var file = inputFile[0].files[0];
  var size_max = 20248729; 
  if ((file.name.endsWith('.xls') || file.name.endsWith('.xlsx')) && (file.size < size_max)){
     // console.log('working well');
   }else{
     alert('Error: Unicamente se permiten archivos de Microsoft Excel');
     $$('#licitacion_file').val("");
     return;
   }
 }else{
   alert("Error: Debe Seleccionar un archivo");
   return;
 }

 var data  = new FormData();
 data.append('accion','upload_excel_file');
 data.append('licitacion', $$('#licitacion_file')[0].files[0]);
 data.append('id_licitacion', $$('#licitacionID').val());

 var r = confirm("Esta opcion remplazara todos los elementos actuales, desea continuar?");
 if (r == true) {
   jQuery.ajax({
     url:url,
     type:'POST',
     contentType:false,
     data:data,
     processData:false,
     cache:false,
     beforeSend: function(){
        //parent.document.getElementById("AjaxloadData").style.display = "block";
      },
      complete: function(){
     //parent.document.getElementById("AjaxloadData").style.display = "none";
   },
 }).done(function(data){
  search_selected($$('#licitacionID').val(),$$('#eventoID').val());
}).fail(function(data){
  alert(data.responseText);
});
}
}


