<?php

namespace App\Http\Controllers;

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

        return view('home.inicio');
    }
}
