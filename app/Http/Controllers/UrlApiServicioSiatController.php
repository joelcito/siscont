<?php

namespace App\Http\Controllers;

use App\Models\SiatTipoDocumentoSector;
use App\Models\UrlApiServicioSiat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UrlApiServicioSiatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function listado(Request $request)
    {
        $documentosSectores = SiatTipoDocumentoSector::all();

        return view('url_api_servicio_siat.listado')->with(compact('documentosSectores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function ajaxListado(Request $request)
    {
        if($request->ajax()){
            $data['estado'] = 'success';
            $data['listado'] = $this->listadoArrayEmpresa();
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function agregarApiServicio(Request $request){
        if($request->ajax()){
            $urlServicio                           = new UrlApiServicioSiat();
            $urlServicio->usuario_creador_id       = Auth::user()->id;
            $urlServicio->siat_documento_sector_id = $request->input('new_api_servicio_documento_sector');
            $urlServicio->ambiente                 = $request->input('new_api_servicio_ambiente');
            $urlServicio->nombre                   = $request->input('new_api_servicio_nombre');
            $urlServicio->url_servicio             = $request->input('new_api_servicio_url_servicio');
            $urlServicio->modalidad             = $request->input('new_api_servicio_modalidad');
            $urlServicio->save();
            $data['estado'] = 'success';
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    protected function listadoArrayEmpresa(){
        $url_apis_servicios = UrlApiServicioSiat::all();
        return view('url_api_servicio_siat.ajaxListado')->with(compact('url_apis_servicios'))->render();
    }
}
