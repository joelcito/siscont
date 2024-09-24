<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home';

     /**
     * Redirección personalizada después del login
     */
    protected function redirectTo()
    {

        $usuario = Auth::user();

        // dd($usuario);

        if($usuario->isAdmin()){
            return '/home'; // Redirige a la vista de usuarios regulares
        }else{
            if($usuario->isFacturacionCompraVenta()){
                return '/factura/formularioFacturacionCv'; // Redirige a la vista de usuarios regulares
            }else{
                return '/factura/formularioFacturacionTc'; // Redirige a la vista de usuarios regulares
            }
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
