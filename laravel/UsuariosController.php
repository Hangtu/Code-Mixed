<?php
// Creado por Hang Tu Wong Ley Franco
// 08/08/2016
namespace Test\Http\Controllers\Administracion;
use Session;
use DB;
use Validator;
use Input;
use Crypt;

use Illuminate\Http\Request;
use Test\Http\Requests;
use Test\Http\Controllers\Controller;
use Test\Modelos\adm_tipo;
use Test\Modelos\adm_usuario;
use Test\Modelos\adm_usuario_Test;

class UsuariosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index() //Vista principal
    {		
      $menuPrincipal = true;
      return view('Administracion.Usuarios.index')->with('menuPrincipal',$menuPrincipal);
    }

        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
        public function nuevo()//Vista para crear un nuevo usuario
        {
         $tipos = adm_tipo::pluck('descripcion', 'id_tipo')->sort();
          // $tipos = adm_tipo::select('id_tipo', DB::raw("CONCAT(descripcion, ' ', descripcion) AS full_name"))
          // ->orderBy('descripcion')
          // ->lists('full_name', 'id_tipo'); ejemplo de concatenacion de campos
         return view('Administracion.Usuarios.index')->with('tipos',$tipos);
       }

        public function edit()//Vista para actualizar un usuario
        {   
          $update = true;
          $tipos = adm_tipo::pluck('descripcion', 'id_tipo')->sort();
          return view('Administracion.Usuarios.index')->with('tipos',$tipos)->with('update',$update);
        }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){ //CREAR USUARIO


      //Se pone un alias a los request del form
      $attributeNames = array( 
        'txtUsuario' => 'Usuario',
        'txtPassword' => 'Contraseña',
        'txtPassword2' => 'Re-Contraseña',
        'txtNombre' => 'Nombre',
        'txtPaterno' => 'Apellido Paterno',
        'txtMaterno' => 'Apellido Materno',
        'cmbTipoUsuario' => 'Tipo de Usuario',     
        );

       //regex:/(^[A-Za-z0-9 ]+$)+/ - //LETRAS NUMEROS Y ESPACIOS;
       //regex:/^[\pL\s\-]+$/u      - // SOLO LETRAS Y ESPACIOS;
      $validator = Validator::make($request->all(), [  
        'txtUsuario' => 'required|between:1,50|alpha_num',
        'txtPassword' => 'required|between:4,40|same:txtPassword2|alpha_num',
        'txtPassword2' => 'required|alpha_num',
        'txtNombre' => 'required|regex:/^[\pL\s\-]+$/u|between:1,100',
        'txtPaterno' => 'required|regex:/^[\pL\s\-]+$/u|between:1,100',
        'txtMaterno' => 'required|regex:/^[\pL\s\-]+$/u|between:1,100',
        'cmbTipoUsuario' => 'required|integer|exists:adm_tipo,id_tipo',
        'txtCorreoSiil' => 'email',
        'txtCorreoTest' => 'email',
        'txtCorreoQuiro' => 'email'
        ]);

       $validator->setAttributeNames($attributeNames); //Se renombran a los atributos

      //OBTENGO LOS ERRORES Y LOS MUESTRO
       $messages =  $validator->errors()->all();
       $errores ="";
       foreach ($messages as $message) {
        $errores.='<li>'.$message.'</li>';
      }
      if ($validator->fails()) {
        return response($errores,400);
      }

      $users = DB::SELECT("SELECT id_usuario FROM adm_usuario 
        WHERE usuario = ?",array($request->input('txtUsuario')));

      if(sizeof($users) != 0){
        return response('Ese usuario existe en la base de datos',400);
      }

      $fecha_captura = date('Y-m-d H:i:s');
      $usuario =  $request->input('txtUsuario');
      $password = sha1($request->input('txtPassword'));
      $nombre = strtoupper($request->input('txtNombre'));
      $paterno = strtoupper($request->input('txtPaterno'));
      $materno = strtoupper($request->input('txtMaterno'));
      $id_tipo = $request->input('cmbTipoUsuario');



      $correo_siil  = $request->input('txtCorreoSiil');
      $correo_Test = $request->input('txtCorreoTest');
      $correo_quiro = $request->input('txtCorreoQuiro');

      $usuario_captura = $_SESSION['idUser'];


      $db = DB::connection('alpha');

      $db->insert("insert into adm_usuario (usuario, password, nombre, id_tipo, fecha_captura, paterno, materno, estatus, correo_electronico, correo_electronico_Test, correo_electronico_quiropractico, id_usuario_captura ) values ('$usuario','$password','$nombre','$id_tipo','$fecha_captura','$paterno','$materno',1,'$correo_siil','$correo_Test','$correo_quiro','$usuario_captura')");

      return response('El usuario ha sido creado');
    }

    	/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    	public function update(Request $request) //ACTUALIZAR USUARIOS
    	{	
        //DESENCRIPTO LOS DATOS DEL USUARIO ID
        $request->userID = (INT) Crypt::decrypt($request->userID);

        //SI NO SE HA SELECCIONADO UN USUARIO
        if ($request->userID == ''){
          return response('Tiene que buscar un usuario',400);
        }

        $attributeNames = array( //CAMBIO NOMBRE DE LOS INPUT DE LAS VARIABLES PARA QUE SE MUESTREN CON ESE NOMBRE SI HAY ERROR
          'txtUsuario' => 'Usuario',
          'txtPassword' => 'Contraseña',
          'txtPassword2' => 'Re-Contraseña',
          'txtNombre' => 'Nombre',
          'txtPaterno' => 'Apellido Paterno',
          'txtMaterno' => 'Apellido Materno',
          'cmbTipoUsuario' => 'Tipo de Usuario',     
          );

        $val1="";
        $val2="";

        //SI LA CONTRASEÑAS SE QUIEREN ACTUALIZAR
        if ($request->input('txtPassword') != '' || $request->input('txtPassword2') != ''){
          $val1="required|between:4,40|same:txtPassword2|alpha_num";
          $val2="required|alpha_num";
        }
        //regex:/(^[A-Za-z0-9 ]+$)+/ - //LETRAS NUMEROS Y ESPACIOS;
        //regex:/^[\pL\s\-]+$/u      - // SOLO LETRAS Y ESPACIOS;
        $validator = Validator::make($request->all(), [
         'txtUsuario' => 'required|between:1,50|alpha_num',
         'txtPassword' => $val1,
         'txtPassword2' => $val2,
         'txtNombre' => 'required|regex:/^[\pL\s\-]+$/u|between:1,100',
         'txtPaterno' => 'required|regex:/^[\pL\s\-]+$/u|between:1,100',
         'txtMaterno' => 'required|regex:/^[\pL\s\-]+$/u|between:1,100',
         'cmbTipoUsuario' => 'required|integer|exists:adm_tipo,id_tipo',
         'status' => 'boolean',
         'userID' => 'required',
         'txtCorreoSiil' => 'email',
         'txtCorreoTest' => 'email',
         'txtCorreoQuiro' => 'email'
         ]);

        $validator->setAttributeNames($attributeNames);
        $messages =  $validator->errors()->all();
        $errores ="";
        foreach ($messages as $message) {
          $errores.='<li>'.$message.'</li>';
        }
        if ($validator->fails()) {
          return response($errores,400);
        }

        //SI NO ES EL MISMO NOMBRE DE USUARIO
        if ($request->input('txtUsuario') != $request->input('userName')){
          $users = DB::SELECT("SELECT id_usuario FROM adm_usuario 
            WHERE usuario = ?",array($request->input('txtUsuario')));
          if(sizeof($users) != 0){
            return response('Ese nombre de usuario existe en la base de datos',400);
          }
        }

        // SE BUSCA EL USUARIO QUE SE QUIERE MODIFICAR Y SE ACTUALIZA
        $fecha_modificacion = date('Y-m-d H:i:s');
        $id_usuario = $request->userID;
        $usuario = $request->input('txtUsuario');
        $nombre = strtoupper($request->input('txtNombre'));
        $paterno = strtoupper($request->input('txtPaterno'));
        $materno = strtoupper($request->input('txtMaterno'));
        $tipo = $request->input('cmbTipoUsuario');

        $password = sha1($request->input('txtPassword'));
        
        $status='';
        if(isset($request->status)){
          $status = $request->status;
        }else{
          $status = 0;
        }

        $pass='';
        if ($request->input('txtPassword') != '') { // SI SE ACTUALIZA LA CONTRASEÑA
         $pass = ",password = '$password'";
       }

       $correo_siil  = $request->input('txtCorreoSiil');
       $correo_Test = $request->input('txtCorreoTest');
       $correo_quiro = $request->input('txtCorreoQuiro');

       $usuario_modifacion = $_SESSION['idUser'];

       $alpha = DB::connection('alpha');

       $alpha->update("update adm_usuario set usuario = '$usuario', nombre = '$nombre', paterno = '$paterno', materno = '$materno', id_tipo = '$tipo', estatus = '$status', correo_electronico = '$correo_siil', correo_electronico_Test = '$correo_Test',correo_electronico_quiropractico = '$correo_quiro', id_usuario_modificacion = '$usuario_modifacion' ,fecha_modificacion = '$fecha_modificacion'".$pass."where id_usuario = '$id_usuario'");

       return response('El usuario ha sido modificado');
     }

    	public function search(Request $request){ //BUSCAR USUARIOS
    		$search = $request->search;
    		$users = DB::SELECT("SELECT 
    			u.id_usuario, u.usuario, u.nombre, u.paterno, u.materno, u.estatus, u.id_tipo, u.password,u.correo_electronico,u.correo_electronico_Test,u.correo_electronico_quiropractico ,t.descripcion
    			FROM adm_usuario u 
    			INNER JOIN adm_tipo t ON u.id_tipo = t.id_tipo
    			WHERE (u.nombre||' '||u.paterno||' '||u.materno) ILIKE (?) OR u.usuario ILIKE (?) LIMIT 30",array("%$search%","%$search%"));

      //ENCRIPTO EL ID_USUARIO DE TODOS LOS RESULTADOS
       foreach ($users as $user) {
         $user->id_usuario = Crypt::encrypt($user->id_usuario);
       }
       return response($users);
     }
     
   }




