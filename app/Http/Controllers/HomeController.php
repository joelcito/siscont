<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        // dd(Auth::user()->empresa->empresasDocumentos[1]->siat_tipo_documento_sector->codigo_clasificador);

        // dd(Auth::user()->empresa->empresasDocumentos);

        $cantidaEmpresas = Empresa::count();

        $empresa = Auth::user()->empresa;

        return view('home.inicio')->with(compact('cantidaEmpresas', 'empresa'));
    }
}
