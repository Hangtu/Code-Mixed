/*
	Nombre: Hang Tu Wong Ley
    Fecha:2016-10-19
    */
    var url = "ventas/licitaciones/consulta_licitacion/acciones.php";
    var $$ = jQuery;

    var PROVEDORES = "";
    var LABORATORIOS = "";
    var copySelect = false;
    var selectProvedores;
    var selectLaboratorios;


    jQuery(document).ready(function($){
      $$('#evento').val('10');
      // buscar();

      $$('#evento').keypress(function(e) {
        if(e.which == 13) {
         buscar();
       }
     });


    });

    function buscar(){
      copySelect = false;
      var folio  = $$("#evento").val();
      if (folio !== ""){
        var vars = null
        document.getElementById("consultar").disabled = true;
        vars = "search="+folio;

        var win = new Window({className: "alphacube", width:600, height:300, zIndex: 100, resizable: true, title: "Folio",
          onClose:function(win){document.getElementById("consultar").disabled = false;},
          showEffectOptions: {duration:0.2}});
        win.setAjaxContent(url, {method:'post', postBody:vars});
        win.setConstraint(true, {left:5, right:5, top: 30, bottom:10})
        win.showCenter(); 
      }
      else{
        alert("Debe de indicar un numero de evento");
      }
    }


    function consultar(idLicitacion, noEvento){
     Windows.closeAll();

     $$("#tbl_licitaciones").html('');
     $$("#txt_evento").val('');
     $$("#txt_cliente").val('');
     $$("#txt_fecha_licitacion").val('');
     $$("#txt_estado").val('');
     $$('#licitacion').val('');

     data = {  
      accion: 'cargar_licitacion',
      id_licitacion: idLicitacion,
      evento: noEvento
    };

    $$('#loading').show();
    setTimeout(function(){
      var search = $$.post(url,{data:JSON.stringify(data)},function(){

      });

      search.done(function(data){
        $$('#loading').hide();
        try{
         resp = $$.parseJSON(data);
       }catch(e){
         console.log('something was wrong!');
         return;
       }

       PROVEDORES = resp.provedores; 
       LABORATORIOS = resp.laboratorios;

       $$('#txt_evento').val(resp.numero_evento);
       $$('#txt_fecha_licitacion').val(resp.fecha_licitacion);
       $$('#txt_estado').val(resp.estado);

       if (resp.razon_social != ''){
        $$('#txt_cliente').val(resp.razon_social);
      }else{
        $$('#txt_cliente').val(resp.cliente_nuevo);
      }


      $$('#licitacionID').val(resp.id_licitacion);
      $$('#noEventoID').val(noEvento);

      if(resp.estado == ''){
       $$('#txt_estado').val('NACIONAL');
     }
       rows(resp.rows, resp.provedores, resp.laboratorios); // PINTA TODOS LOS DATOS DE EL DOCUMETO 
     });

      search.fail(function(data){
        alert(data.responseText);
      });
    },1000);
  } 

  function rows(data,provedores,laboratorios){


    for (var i = 0; i < data.length; i++){
     tr = '<tr data-id-licitacion='+data[i].id_licitacion+' data-partida="'+data[i].partida+'" data-clave_cbn="'+data[i].clave_cbn+'">';
     tr +='<td class="cell" ></td>';
     tr +='<td class="cell" ><input type="text" onkeypress="return validar(event,this,&quot;numeros&quot;)" class="partida" value="'+data[i].partida+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell" ><input type="text" onkeypress="return validar(event,this,&quot;texto&quot;)"   class="clave_cbn" value="'+data[i].clave_cbn+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell" ><input type="text" onkeypress="return validar(event,this,&quot;texto&quot;)"   class="descripcion" value="'+data[i].descripcion+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell" ><input type="text" onkeypress="return validar(event,this,&quot;texto&quot;)"   class="nombre_comercial" value="'+data[i].nombre_comercial+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell" ><input type="text" onkeypress="return validar(event,this,&quot;texto&quot;)"   class="presentacion" value="'+data[i].presentacion+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell" ><input type="text" onkeypress="return validar(event,this,&quot;numeros&quot;)" class="cantidad_minima" value="'+data[i].cantidad_minima+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell" ><input type="text" onkeypress="return validar(event,this,&quot;numeros&quot;)" class="cantidad_maxima" value="'+data[i].cantidad_maxima+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell" ><select class="proveedor" style="max-width:160px"><option value="" selected>--Seleccione una opción--</option></select></td>';
     tr +='<td class="cell" ><input type="text" onkeypress="return validar(event,this,&quot;texto&quot;)"   class="clave_sistema" value="'+data[i].clave_sistema+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell" ><input type="text" onkeypress="return validar(event,this,&quot;precio&quot;)"  class="costo" value="'+data[i].costo+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell" ><input type="text" onkeypress="return validar(event,this,&quot;precio&quot;)"  class="precio" value="'+data[i].precio+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell" ><select class="laboratorio" style="max-width:130px"><option value="" selected>--Seleccione una opción--</option></select></td>';
     tr +='<td class="cell" ><input type="text" onkeypress="return validar(event,this,&quot;texto&quot;)"   class="codigo_barras" value="'+data[i].codigo_barras+'" style="text-transform:uppercase"></td>';
     tr +='<td class="cell" ><select class="empresa" style="max-width:67px"> <option value="" selected>--Seleccione una opción--</option> <option value="Test">Test</option> <option value="QUIROPRACTICO">QUIROPRACTICO</option> </select></td>';
     tr +='<td style="text-align:center" class="cell"><img src="iconos/delete.png" onclick="delete_row(this)" style="cursor:pointer;"></td>';
     tr +='<td class="cell" ></td>';
     tr +='</tr>';

     var el = $$(tr);
     $$('#tbl_licitaciones').append(el);

     if (copySelect == false){
    $$.each(provedores, function (i, item) { // CARGA EL SELECT CON LOS PROVEDORES
      el.find('.proveedor').append($$('<option>', { 
        value: item.id_proveedor,
        text : item.nombre
      }));
    });

    selectProvedores = el.find('.proveedor').html();

     $$.each(laboratorios, function (i, item) { // CARGA EL SELECT CON LOS LABORATORIOS
      el.find('.laboratorio').append($$('<option>', { 
        value: item.id_laboratorio,
        text : item.laboratorio
      }));
    });
     selectLaboratorios = el.find('.laboratorio').html();
     copySelect = true;
   }else{
    el.find('.proveedor').html(selectProvedores);
    el.find('.laboratorio').html(selectLaboratorios);
  }
    el.find('.proveedor').val(data[i].proveedor); // selecciona el proveedor 
    el.find('.laboratorio').val(data[i].laboratorio); // selecciona el laboratorio
    el.find('.empresa').val(data[i].empresa); // selecciona la empresa de la base de datos
  }
}



function add_row(){
  var licitacionID = $$('#licitacionID').val();
  var array = [{id_licitacion: licitacionID,
    partida:'',clave_cbn:'',descripcion:'',nombre_comercial:'',presentacion:'',cantidad_minima:'',cantidad_maxima:'',nom_proveedor:'',
    clave_sistema:'',costo:'',precio:'',nom_laboratorio:'',codigo_barras:'',empresa:''}];
    rows(array, PROVEDORES, LABORATORIOS);
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


  function modificar(){

    var rows = [];
    try{
    $$('#tbl_licitaciones > tr').each(function(){ //read each tr column
      row = {
        partida : $$(this).find(".partida").val().trim(),
        clave_cbn : $$(this).find(".clave_cbn").val().trim(),
        descripcion : $$(this).find(".descripcion").val().trim(),
        nombre_comercial : $$(this).find(".nombre_comercial").val().trim(),
        presentacion : $$(this).find(".presentacion").val().trim(),
        cantidad_minima : $$(this).find(".cantidad_minima").val().trim(),
        cantidad_maxima : $$(this).find(".cantidad_maxima").val().trim(),
        proveedor : $$(this).find(".proveedor").val().trim(),
        clave_sistema : $$(this).find(".clave_sistema").val().trim(),
        costo : $$(this).find(".costo").val().trim(),
        precio : $$(this).find(".precio").val().trim(),
        laboratorio : $$(this).find(".laboratorio").val().trim(),
        codigo_barras : $$(this).find(".codigo_barras").val().trim(),
        empresa : $$(this).find(".empresa").val().trim()
      } 
      validar_row(this,row);
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

function validar_row(tr,data){

  required = ['partida','clave_cbn','descripcion','cantidad_minima'];
  extraRequired = ['clave_sistema','costo','precio'];
  extraAll = ['clave_sistema','costo','precio','laboratorio','codigo_barras','empresa'];

    for(var i = 0; i < required.length; i++){ //validating required fields
      if(data[required[i]] == '') {
       alert("El campo "+required[i].replace('_',' ').toUpperCase() +" no puede quedar vacio");
       $$(tr).find('.'+required[i]).focus();
       throw 'Error';
     }
   }

   if (data.proveedor != ''){
    for(var i = 0; i < extraRequired.length; i++){ // validating optional required fields
      if(data[extraRequired[i]] == ''){
       alert("El campo "+extraRequired[i].replace('_',' ').toUpperCase() +" no puede quedar vacio");
       $$(tr).find('.'+extraRequired[i]).focus();
       throw 'Error';
     }
   }
 }

 if(data.proveedor == ''){
  for(var i = 0; i < extraAll.length; i++){ // validating optional fields when there's not proveedor
    if(data[extraAll[i]] != ''){
     alert("Agregue un PROVEEDOR para insertar el campo "+extraAll[i].replace('_',' ').toUpperCase());
     $$(tr).find('.proveedor').focus();
     throw 'Error';
   }
 }
}
}


function cargar_excel(){

  if($$('#licitacionID').val() == ''){
   return;
 }

 if($$('#licitacion')[0].files.length !== 0){
  var inputFile = $$('#licitacion'); 
  var file = inputFile[0].files[0];
  var size_max = 20248729; 
  if ((file.name.endsWith('.xls') || file.name.endsWith('.xlsx')) && (file.size < size_max)){
     // console.log('working well');
   }else{
     alert('Error: Unicamente se permiten archivos de Microsoft Excel');
     $$('#licitacion').val("");
     return;
   }
 }else{
   alert("Error: Debe Seleccionar un archivo");
   return;
 }


 var data  = new FormData();
 data.append('accion','upload_excel_file');
 data.append('licitacion', $$('#licitacion')[0].files[0]);
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
   consultar($$('#licitacionID').val(),$$('#noEventoID').val());
 }).fail(function(data){
  alert(data.responseText);
});
}
}


