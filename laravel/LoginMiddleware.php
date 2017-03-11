<?php
namespace test\Http\Middleware;
use Closure;
use Artisan;
use Validator;

class LoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next){
        session_name("test");
        session_start();

        if(isset($_SESSION['idUser']) &&  isset($_SESSION['passwd'])){

            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            session()->put('id_usuario', $_SESSION['idUser']);
            session()->put('nombre',     $_SESSION['name']);
            session()->put('usuario',    $_SESSION['user']);
            session()->put('password',   $_SESSION['passwd']);

            // $validator = Validator::make($request->all(), [
            //     'title' => 'required|unique:posts|max:255',
            //     'body' => 'required',
            // ]);

            // return redirect('post/create')
            //             ->withErrors($validator)
            //             ->withInput();

           //echo "asdasdas";
           //exit(0);

            return $next($request);
        }
        else{
            session()->flush();
            return redirect()->away('http://server.test.com');
        }
      }
}
